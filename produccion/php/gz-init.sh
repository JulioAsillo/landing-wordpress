#!/bin/sh
# =============================================================================
#  Aprovisionamiento inicial — se ejecuta UNA vez tras el primer despliegue:
#
#     docker exec -u www-data <contenedor_wordpress> gz-init.sh
#
#  Qué hace:
#    1. Espera a que MySQL termine de importar el contenido inicial.
#    2. Ajusta las URL guardadas en la base si el dominio no es el previsto.
#    3. Crea el usuario administrador y elimina el usuario provisional.
#    4. Marca el sitio como aprovisionado.
#
#  Es seguro repetirlo: si el sitio ya está aprovisionado no toca nada.
# =============================================================================
set -eu

URL_ORIGEN="https://garantiza.pe"   # URL con la que se generó el contenido inicial
WP="wp --path=/var/www/html"

if [ "$(id -u)" = "0" ]; then
    echo "ERROR: ejecutar como www-data:  docker exec -u www-data <contenedor> gz-init.sh" >&2
    exit 1
fi

: "${GZ_URL:?Falta GZ_URL en garantiza.env}"
: "${GZ_ADMIN_USER:?Falta GZ_ADMIN_USER en garantiza.env}"
: "${GZ_ADMIN_EMAIL:?Falta GZ_ADMIN_EMAIL en garantiza.env}"

echo "==> Esperando a la base de datos (la primera importación tarda hasta un minuto)..."
i=0
until $WP core is-installed >/dev/null 2>&1; do
    i=$((i + 1))
    if [ "$i" -gt 60 ]; then
        echo "ERROR: WordPress no responde tras 3 minutos. Revisar: docker service logs garantiza_db" >&2
        exit 1
    fi
    sleep 3
done

if [ "$($WP option get gz_provisionado 2>/dev/null || true)" = "1" ]; then
    echo "==> El sitio ya estaba aprovisionado. No se hizo ningún cambio."
    exit 0
fi

if [ "$GZ_URL" != "$URL_ORIGEN" ]; then
    echo "==> Ajustando URL: $URL_ORIGEN -> $GZ_URL"
    $WP search-replace "$URL_ORIGEN" "$GZ_URL" --all-tables-with-prefix --skip-columns=guid --report-changed-only
fi

echo "==> Creando el administrador '$GZ_ADMIN_USER'..."
CLAVE="$(head -c 48 /dev/urandom | base64 | tr -dc 'A-Za-z0-9' | cut -c1-24)"
NUEVO_ID="$($WP user create "$GZ_ADMIN_USER" "$GZ_ADMIN_EMAIL" --role=administrator --user_pass="$CLAVE" --porcelain)"

for id in $($WP user list --field=ID); do
    if [ "$id" != "$NUEVO_ID" ]; then
        $WP user delete "$id" --reassign="$NUEVO_ID" --yes
    fi
done

$WP option update admin_email "$GZ_ADMIN_EMAIL" >/dev/null
$WP option update blog_public "${GZ_INDEXAR:-1}" >/dev/null
$WP transient delete --all >/dev/null
$WP option update gz_provisionado 1 >/dev/null

cat <<MSG

=====================================================================
  Sitio aprovisionado.

  Panel:       $GZ_URL/wp-admin/
  Usuario:     $GZ_ADMIN_USER
  Contraseña:  $CLAVE

  Esta contraseña se muestra UNA sola vez. Guardarla en el gestor de
  contraseñas y cambiarla en el primer ingreso.
=====================================================================
MSG
