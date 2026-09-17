#!/usr/bin/env bash
# =============================================================================
#  Arma el paquete de entrega para el cliente.
#      ./scripts/empaquetar.sh 1.0.0
#
#  Resultado: entrega/garantiza-<versión>/
#     imagenes/    las tres imágenes en un .tar.gz (docker load)
#     fuente/      código fuente para construir en su GitLab CI
#     despliegue/  stack, parámetros y scripts
#     MANUAL-DESPLIEGUE.md  +  SHA256SUMS
# =============================================================================
set -euo pipefail
cd "$(dirname "$0")/.."

V="${1:?Uso: ./scripts/empaquetar.sh <versión>, por ejemplo 1.0.0}"
DUMP="produccion/db/garantiza-golden.sql.gz"
DEST="entrega/garantiza-$V"

[ -f "$DUMP" ] || { echo "ERROR: falta $DUMP. Ejecutar antes scripts/generar-dump-limpio.sh" >&2; exit 1; }
[ -z "$(git status --porcelain)" ] || echo "AVISO: hay cambios sin confirmar; el paquete de fuente solo incluye lo que está en el último commit."

rm -rf "$DEST"; mkdir -p "$DEST"/{imagenes,fuente,despliegue}

echo "==> Construyendo imágenes $V"
for t in wp nginx db; do
    docker build -f produccion/Dockerfile --target "$t" \
        --label "org.opencontainers.image.version=$V" \
        --label "org.opencontainers.image.revision=$(git rev-parse --short HEAD)" \
        -t "garantiza-$t:$V" .
done

echo "==> Exportando imágenes"
docker save "garantiza-wp:$V" "garantiza-nginx:$V" "garantiza-db:$V" | gzip > "$DEST/imagenes/garantiza-imagenes-$V.tar.gz"

echo "==> Código fuente"
git archive --format=tar --prefix="garantiza-$V/" HEAD > "$DEST/fuente/f.tar"
# El contenido inicial no está en Git (lleva datos de configuración): se añade aquí.
tar --append -f "$DEST/fuente/f.tar" --transform "s|^|garantiza-$V/|" "$DUMP"
gzip -9 "$DEST/fuente/f.tar"; mv "$DEST/fuente/f.tar.gz" "$DEST/fuente/garantiza-fuente-$V.tar.gz"

echo "==> Archivos de despliegue"
cp produccion/docker-stack.yml produccion/docker-stack.ensayo.yml produccion/garantiza.env.example \
   produccion/crear-secrets.sh produccion/desplegar.sh produccion/verificar.sh "$DEST/despliegue/"
sed -i "s/^GZ_VERSION=.*/GZ_VERSION=$V/" "$DEST/despliegue/garantiza.env.example"
cp produccion/MANUAL-DESPLIEGUE.md "$DEST/"

( cd "$DEST" && find . -type f ! -name SHA256SUMS -print0 | sort -z | xargs -0 sha256sum > SHA256SUMS )

echo; du -sh "$DEST"/*; echo; echo "Paquete listo en $DEST"
