# Imagen base oficial de WordPress sobre PHP-FPM 8.4
# (rama con soporte activo hasta diciembre de 2028, según ficha técnica)
FROM wordpress:php8.4-fpm-alpine

# imagick ya viene incluido en la imagen base; solo se verifica que esté activo.
# El "|| true" evita que el build falle si ya está habilitado.
RUN docker-php-ext-enable imagick || true

# Configuración de PHP alineada con lo que espera WordPress
COPY php.ini /usr/local/etc/php/conf.d/zz-landing.ini

# Expone las variables de entorno del contenedor a PHP (ver fpm-env.conf)
COPY fpm-env.conf /usr/local/etc/php-fpm.d/zz-env.conf
