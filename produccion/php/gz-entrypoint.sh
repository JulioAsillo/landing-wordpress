#!/bin/sh
# Envoltorio del entrypoint oficial de WordPress.
# El volumen de "uploads" nace con dueño root; PHP corre como www-data y
# no podría escribir en él. Se corrige antes de arrancar.
set -e
mkdir -p /var/www/html/wp-content/uploads
chown www-data:www-data /var/www/html/wp-content/uploads
exec docker-entrypoint.sh "$@"
