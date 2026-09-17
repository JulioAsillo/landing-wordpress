# syntax=docker/dockerfile:1
# =============================================================================
#  Landing Garantiza — imágenes de producción
#
#  Un solo Dockerfile, tres imágenes (una por --target):
#     wp     WordPress + PHP-FPM con el tema y los plugins dentro
#     nginx  Nginx con la configuración endurecida y los mismos archivos
#     db     MySQL con el contenido inicial del sitio (solo se importa la
#            primera vez, cuando el volumen de datos está vacío)
#
#  El contexto de build es la RAÍZ del repositorio:
#     docker build -f produccion/Dockerfile --target wp -t garantiza-wp:1.0.0 .
#
#  Las versiones van fijadas aquí. Para actualizar un componente se cambia
#  el ARG, se reconstruye y se publica una versión nueva de la imagen.
# =============================================================================
ARG WP_TAG=7.1-php8.4-fpm-alpine
ARG WPCLI_TAG=cli-php8.4
ARG NGINX_TAG=1.27-alpine
ARG MYSQL_TAG=8.4

# --- Plugins, con versión fija, desde el repositorio oficial -----------------
FROM alpine:3.20 AS plugins
ARG FLUENTFORM_VERSION=6.2.14
ARG WPMAILSMTP_VERSION=4.9.0
RUN apk add --no-cache curl unzip
RUN set -eux; \
    mkdir /plugins; \
    for p in "fluentform.${FLUENTFORM_VERSION}" "wp-mail-smtp.${WPMAILSMTP_VERSION}"; do \
        curl -fsSL -o /tmp/p.zip "https://downloads.wordpress.org/plugin/${p}.zip"; \
        unzip -q /tmp/p.zip -d /plugins; \
        rm /tmp/p.zip; \
    done

FROM wordpress:${WPCLI_TAG} AS wpcli

# --- WordPress ---------------------------------------------------------------
FROM wordpress:${WP_TAG} AS wp

# La imagen oficial guarda WordPress en /usr/src/wordpress y lo copia a
# /var/www/html al arrancar el contenedor. Todo lo propio se añade en el
# origen, así viaja dentro de la imagen y no depende de ningún volumen.
RUN set -eux; \
    rm -rf /usr/src/wordpress/wp-content/plugins/akismet \
           /usr/src/wordpress/wp-content/plugins/hello.php \
           /usr/src/wordpress/wp-content/themes/twenty*

COPY --from=plugins /plugins/ /usr/src/wordpress/wp-content/plugins/
COPY theme-src/   /usr/src/wordpress/wp-content/themes/landing/
COPY mu-plugins/  /usr/src/wordpress/wp-content/mu-plugins/

# WP-CLI dentro de la imagen: lo usa el aprovisionamiento inicial y sirve
# después para soporte (docker exec ... wp ...).
COPY --from=wpcli /usr/local/bin/wp /usr/local/bin/wp

COPY produccion/php/php.prod.ini /usr/local/etc/php/conf.d/zz-garantiza.ini
COPY php/fpm-env.conf            /usr/local/etc/php-fpm.d/zz-env.conf
COPY produccion/php/gz-entrypoint.sh produccion/php/gz-init.sh /usr/local/bin/
RUN chmod 0755 /usr/local/bin/gz-entrypoint.sh /usr/local/bin/gz-init.sh /usr/local/bin/wp

ENV WP_CLI_CACHE_DIR=/tmp/wp-cli
ENTRYPOINT ["gz-entrypoint.sh"]
CMD ["php-fpm"]

# --- Nginx -------------------------------------------------------------------
FROM nginx:${NGINX_TAG} AS nginx
# Mismos archivos que la imagen de WordPress: Nginx sirve los estáticos y
# necesita ver los .php para resolver las rutas (nunca los ejecuta).
COPY --from=wp /usr/src/wordpress/ /var/www/html/
COPY produccion/nginx/default.prod.conf /etc/nginx/conf.d/default.conf
COPY nginx/snippets/ /etc/nginx/snippets/

# --- MySQL con el contenido inicial ------------------------------------------
FROM mysql:${MYSQL_TAG} AS db
# Se genera con scripts/generar-dump-limpio.sh. Si falta, el build falla.
COPY produccion/db/garantiza-golden.sql.gz /docker-entrypoint-initdb.d/10-garantiza.sql.gz
