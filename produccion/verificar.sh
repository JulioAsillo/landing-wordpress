#!/usr/bin/env bash
# Comprobación posterior al despliegue.
#   ./verificar.sh                       comprobaciones HTTP y de servicios
#   ./verificar.sh --correo a@dominio    además envía un correo de prueba
set -uo pipefail
cd "$(dirname "$0")" || exit 1
set -a; . ./garantiza.env; set +a

fallos=0
ok()   { echo "  [OK]    $*"; }
mal()  { echo "  [FALLA] $*"; fallos=$((fallos + 1)); }
codigo() { curl -k -s -o /dev/null -w '%{http_code}' --max-time 20 "$1"; }
espera() { local c; c="$(codigo "$2")"; [ "$c" = "$3" ] && ok "$1 -> $c" || mal "$1 -> $c (se esperaba $3)"; }

echo "Servicios:"
for s in nginx wordpress db; do
    r="$(docker service ls --filter "name=garantiza_$s" --format '{{.Replicas}}' | head -1)"
    [ "$r" = "1/1" ] && ok "garantiza_$s $r" || mal "garantiza_$s ${r:-no existe}"
done

echo "Sitio ($GZ_URL):"
espera "Portada"                 "$GZ_URL/"                          200
espera "API REST (formularios)"  "$GZ_URL/wp-json/"                  200
espera "Política de privacidad"  "$GZ_URL/politica-de-privacidad/"   200
espera "Términos y condiciones"  "$GZ_URL/terminos-y-condiciones/"   200
espera "Inicio de sesión"        "$GZ_URL/wp-login.php"              200
espera "xmlrpc bloqueado"        "$GZ_URL/xmlrpc.php"                403
espera "wp-config bloqueado"     "$GZ_URL/wp-config.php"             403

html="$(curl -k -s --max-time 20 "$GZ_URL/")"
echo "$html" | grep -q 'fluentform' && ok "El formulario de contacto está en la portada" || mal "No se encontró el formulario en la portada"
echo "$html" | grep -Eq '34\.132\.80\.1|novaly' && mal "Hay referencias al entorno de desarrollo en la portada" || ok "Sin referencias al entorno de desarrollo"

# --- Indexación y SEO (QA BUG-008 y BUG-009) -------------------------------
# En desarrollo el sitio va con blog_public=0 y sale "noindex, nofollow".
# Aquí se comprueba que el paquete de producción llega indexable y con el
# título y la descripción correctos, que es donde se levantan esas dos
# observaciones.
echo "$html" | grep -qi 'name="robots"[^>]*noindex' \
    && mal "La portada sale con noindex: revisar GZ_INDEXAR en garantiza.env" \
    || ok "La portada es indexable (sin noindex)"

titulo="$(printf '%s' "$html" | tr -d '\n' | sed -n 's/.*<title>\(.*\)<\/title>.*/\1/p')"
if [ "${#titulo}" -ge 30 ]; then
    ok "Título del documento (${#titulo} caracteres): $titulo"
else
    mal "Título demasiado corto o ausente: '${titulo}'"
fi

echo "$html" | grep -qi '<meta name="description"' \
    && ok "Meta description presente" \
    || mal "Falta la meta description en la portada"

if [ "${1:-}" = "--correo" ] && [ -n "${2:-}" ]; then
    echo "Correo:"
    c="$(docker ps -q -f label=com.docker.swarm.service.name=garantiza_wordpress | head -1)"
    if [ -z "$c" ]; then
        mal "El contenedor de WordPress no está en este nodo; ejecutar en el nodo etiquetado"
    else
        r="$(docker exec -u www-data "$c" wp --path=/var/www/html eval "var_export(wp_mail('$2','Prueba de envio - Garantiza','Correo de prueba del sitio.'));" 2>&1 | tail -1)"
        [ "$r" = "true" ] && ok "Correo entregado al servidor SMTP (revisar la bandeja de $2)" || mal "wp_mail devolvió: $r"
    fi
fi

echo
[ "$fallos" -eq 0 ] && echo "Todo correcto." || { echo "$fallos comprobación(es) con falla."; exit 1; }
