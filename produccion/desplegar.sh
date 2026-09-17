#!/usr/bin/env bash
# Despliega o actualiza el stack. Uso:  ./desplegar.sh [-c otro-archivo.yml]
set -euo pipefail
cd "$(dirname "$0")"

error() { echo "ERROR: $*" >&2; exit 1; }

[ -f garantiza.env ] || error "falta garantiza.env (copiar de garantiza.env.example y completar)"
set -a; . ./garantiza.env; set +a

for v in GZ_VERSION GZ_DOMINIO GZ_URL GZ_ADMIN_USER GZ_ADMIN_EMAIL \
         TRAEFIK_NETWORK TRAEFIK_ENTRYPOINT TRAEFIK_CERTRESOLVER \
         SMTP_HOST SMTP_PORT SMTP_USER SMTP_FROM; do
    [ -n "${!v:-}" ] || error "la variable $v está vacía en garantiza.env"
done
export GZ_IMAGE_PREFIX="${GZ_IMAGE_PREFIX:-}"

for s in gz_db_password gz_db_root_password "${GZ_SECRET_SMTP:-gz_smtp_password}"; do
    docker secret inspect "$s" >/dev/null 2>&1 || error "falta el secret $s (ejecutar ./crear-secrets.sh)"
done
docker network inspect "$TRAEFIK_NETWORK" >/dev/null 2>&1 \
    || error "no existe la red $TRAEFIK_NETWORK (revisar TRAEFIK_NETWORK)"
[ -n "$(docker node ls -q --filter node.label=garantiza=true)" ] \
    || error "ningún nodo tiene la etiqueta garantiza=true (paso 4 del manual)"

docker stack deploy --with-registry-auth -c docker-stack.yml "$@" garantiza
echo
echo "Desplegado. Seguir el arranque con:  docker stack ps garantiza"
