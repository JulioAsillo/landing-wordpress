#!/usr/bin/env bash
# Crea los secrets de Swarm que necesita el stack. Ejecutar en un nodo manager.
#   - Claves de MySQL: se generan al azar. Nadie necesita conocerlas.
#   - Clave SMTP: se pide por teclado, sin mostrarla ni escribirla en disco.
# Si un secret ya existe se deja como está.
set -euo pipefail

aleatoria() { head -c 64 /dev/urandom | base64 | tr -dc 'A-Za-z0-9' | cut -c1-32; }

crear() {
    local nombre="$1" valor="$2"
    if docker secret inspect "$nombre" >/dev/null 2>&1; then
        echo "  = $nombre ya existe, no se modifica"
    else
        printf '%s' "$valor" | docker secret create "$nombre" - >/dev/null
        echo "  + $nombre creado"
    fi
}

docker info --format '{{.Swarm.ControlAvailable}}' 2>/dev/null | grep -q true \
    || { echo "ERROR: este nodo no es manager de un Swarm." >&2; exit 1; }

NOMBRE_SMTP="${1:-gz_smtp_password}"

echo "Secrets de base de datos:"
crear gz_db_password      "$(aleatoria)"
crear gz_db_root_password "$(aleatoria)"

echo "Secret de correo ($NOMBRE_SMTP):"
if docker secret inspect "$NOMBRE_SMTP" >/dev/null 2>&1; then
    echo "  = $NOMBRE_SMTP ya existe, no se modifica"
else
    while true; do
        read -r -s -p "  Contraseña de la cuenta SMTP: " c1; echo
        read -r -s -p "  Repetir: " c2; echo
        [ -n "$c1" ] && [ "$c1" = "$c2" ] && break
        echo "  No coinciden o está vacía. Intentar de nuevo."
    done
    crear "$NOMBRE_SMTP" "$c1"
    unset c1 c2
fi

echo; docker secret ls --filter name=gz_
