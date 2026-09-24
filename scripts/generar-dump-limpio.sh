#!/usr/bin/env bash
set -euo pipefail
cd "$(dirname "$0")/.."

# ---- Ajustables -------------------------------------------------------------
# IDs de páginas que NO deben llegar a producción (borrador antiguo y duplicado).
PAGINAS_BORRAR="${PAGINAS_BORRAR:-3 20}"
URL_PROD="https://garantiza.pe"
# Todas las URL con las que se haya abierto el sitio en desarrollo.
URLS_DEV=("http://34.132.80.1" "https://garantiza-dev.novaly.com" "http://garantiza-dev.novaly.com")
# -----------------------------------------------------------------------------

GOLD="gz_golden"
SALIDA="produccion/db/garantiza-golden.sql.gz"
DB_USER="$(grep -E '^DB_USER=' .env | cut -d= -f2-)"

sql()  { docker compose exec -T db sh -c 'MYSQL_PWD="$MYSQL_ROOT_PASSWORD" exec mysql -uroot "$@"' _ "$@"; }
gsql() { sql -N -B "$GOLD" -e "$1"; }
wpg()  { docker compose run --rm -T -e WORDPRESS_DB_NAME="$GOLD" wpcli "$@" 2>/dev/null; }

echo "==> 1/6 Copiando la base de desarrollo a $GOLD"
sql -e "DROP DATABASE IF EXISTS $GOLD; CREATE DATABASE $GOLD CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci; GRANT ALL ON $GOLD.* TO '$DB_USER'@'%';"
docker compose exec -T db sh -c \
    'export MYSQL_PWD="$MYSQL_ROOT_PASSWORD"; mysqldump -uroot --single-transaction --no-tablespaces "$MYSQL_DATABASE" | mysql -uroot "$1"' _ "$GOLD"

P="$(wpg db prefix | tr -d '[:space:]')"
[ -n "$P" ] || { echo "ERROR: no se pudo leer el prefijo de tablas" >&2; exit 1; }

echo "==> 2/6 Contenido de prueba"
# shellcheck disable=SC2086
wpg post delete $PAGINAS_BORRAR --force || true
for tipo in post revision; do
    ids="$(wpg post list --post_type="$tipo" --post_status=any --format=ids || true)"
    # shellcheck disable=SC2086
    [ -z "$ids" ] || wpg post delete $ids --force >/dev/null
done
ids="$(wpg post list --post_type=page --post_status=auto-draft,trash --format=ids || true)"
# shellcheck disable=SC2086
[ -z "$ids" ] || wpg post delete $ids --force >/dev/null
# Libro de Reclamaciones retirado a pedido del cliente (v1.0.1): página,
# formulario de Fluent Forms y ajuste del Personalizador.
libro="$(gsql "SELECT ID FROM ${P}posts WHERE post_type='page' AND post_name='libro-de-reclamaciones'" || true)"
# shellcheck disable=SC2086
[ -z "$libro" ] || wpg post delete $libro --force >/dev/null
form_libro="$(wpg theme mod get gz_claims_form_id 2>/dev/null | tr -d '[:space:]' || true)"
if [ -n "$form_libro" ] && [ "$form_libro" != "0" ]; then
    gsql "DELETE FROM ${P}fluentform_form_meta WHERE form_id=$form_libro; DELETE FROM ${P}fluentform_forms WHERE id=$form_libro;"
fi
wpg theme mod remove gz_claims_form_id >/dev/null || true
ids="$(wpg comment list --format=ids || true)"
# shellcheck disable=SC2086
[ -z "$ids" ] || wpg comment delete $ids --force >/dev/null

echo "==> 3/6 Usuarios: solo queda un usuario provisional sin acceso"
for id in $(wpg user list --field=ID); do
    [ "$id" = "1" ] || wpg user delete "$id" --reassign=1 --yes >/dev/null
done
gsql "UPDATE ${P}users SET user_login='gz_provisional', user_nicename='gz-provisional',
      display_name='Provisional', user_email='provisional@garantiza.pe',
      user_pass='!', user_activation_key='', user_url='' WHERE ID=1;
      UPDATE ${P}usermeta SET meta_value='Provisional' WHERE user_id=1 AND meta_key='nickname';
      UPDATE ${P}usermeta SET meta_value='' WHERE user_id=1 AND meta_key IN ('first_name','last_name');
      DELETE FROM ${P}usermeta WHERE meta_key IN ('session_tokens','_application_passwords','community-events-location');"

echo "==> 4/6 Registros de formularios, analítica y bitácoras"
existentes="$(gsql 'SHOW TABLES')"
for t in fluentform_submissions fluentform_submission_meta fluentform_entry_details \
         fluentform_logs fluentform_form_analytics wpmailsmtp_debug_events; do
    if echo "$existentes" | grep -qx "${P}${t}"; then
        gsql "TRUNCATE TABLE ${P}${t};"
    fi
done

echo "==> 5/6 URL de producción y ajustes"
for u in "${URLS_DEV[@]}"; do
    wpg search-replace "$u" "$URL_PROD" --all-tables-with-prefix --report-changed-only || true
done
wpg option update admin_email "provisional@garantiza.pe" >/dev/null
wpg option delete new_admin_email >/dev/null || true
wpg option update blog_public 1 >/dev/null
wpg option delete gz_provisionado >/dev/null || true
wpg option delete gz_retencion_ultima >/dev/null || true
wpg transient delete --all --network >/dev/null || true

echo "==> 6/6 Exportando"
mkdir -p "$(dirname "$SALIDA")"
docker compose exec -T db sh -c \
    'MYSQL_PWD="$MYSQL_ROOT_PASSWORD" exec mysqldump -uroot --single-transaction --no-tablespaces --skip-comments "$1"' _ "$GOLD" \
    | gzip -9 > "$SALIDA"

echo
echo "------------------------- REVISAR ANTES DE ENTREGAR -------------------------"
echo "Páginas:";      gsql "SELECT ID, post_name, post_status FROM ${P}posts WHERE post_type='page'"
echo "Formularios:";  gsql "SELECT id, title, status FROM ${P}fluentform_forms"
echo "Ajustes tema:"; wpg option get theme_mods_landing --format=json
echo "Usuarios:";     gsql "SELECT ID, user_login FROM ${P}users"
registros="$(gsql "SELECT COUNT(*) FROM ${P}fluentform_submissions")"
echo "Registros de formularios: $registros  (debe ser 0)"
restos="$(zcat "$SALIDA" | grep -c -E '34\.132\.80\.1|novaly|julio|samy' || true)"
echo "Líneas con rastros de desarrollo (IP, novaly, julio, samy): $restos  (debe ser 0)"
echo "-----------------------------------------------------------------------------"

# Barrera dura: el volcado se versiona, así que nunca debe salir con datos
# personales ni rastros del entorno de desarrollo.
if [ "$registros" != "0" ] || [ "$restos" != "0" ]; then
    rm -f "$SALIDA"
    sql -e "DROP DATABASE $GOLD;"
    echo "ERROR: el volcado no está limpio; se eliminó $SALIDA. No confirmar nada." >&2
    exit 1
fi

sql -e "DROP DATABASE $GOLD;"
ls -lh "$SALIDA"
