# Entorno de desarrollo — Landing WordPress

Stack: Nginx + WordPress (PHP-FPM 8.4) + MySQL 8.4 + Mailpit + WP-CLI.

## Arranque

```bash
cd /opt/proyectos/landing-wordpress

cp .env.example .env
openssl rand -base64 24        # generar contraseñas y pegarlas en .env
nano .env

make build
make up
make logs                      # verificar que no haya errores
```

Abrir la URL definida en `WP_URL` (siempre por HTTPS o por túnel SSH) y completar el asistente de WordPress (o usar `make install` para instalación desatendida).

## Comandos frecuentes

| Comando | Qué hace |
| --- | --- |
| `make up` / `make down` | Levantar / detener |
| `make logs` | Logs en vivo |
| `make shell` | Entrar al contenedor de WordPress |
| `make wp CMD="plugin list"` | Ejecutar WP-CLI |
| `make db-dump` | Respaldo de la base de datos |
| `make prune` | Liberar espacio en disco |

## Acceso a servicios internos

MySQL y Mailpit están atados a `127.0.0.1` a propósito: no quedan expuestos a internet. Para alcanzarlos desde tu equipo, abrir un túnel SSH:

```bash
ssh -i <clave> -L 8025:localhost:8025 -L 3306:localhost:3306 <usuario>@<servidor-dev>
```

Luego: bandeja de correos en `http://localhost:8025` y MySQL en `localhost:3306`.

## Correo en desarrollo

Todo correo que envíe WordPress es capturado por Mailpit y no sale a internet. Esto permite construir y probar el formulario sin tener aún las credenciales SMTP del cliente. Cuando lleguen, se instala WP Mail SMTP y se apuntan al servidor real; el comportamiento del formulario no cambia.

## Vigilancia del disco

El servidor tiene ~5.4 GB libres. Revisar con `df -h /` después de cada sesión de builds y ejecutar `make prune` cuando el uso supere el 80 %.

## Plugins base a instalar

```bash
make wp CMD="plugin install wp-mail-smtp fluentform webp-express --activate"
```

- **WP Mail SMTP** — envío confiable de correos (punto 2 del plan de trabajo)
- **Fluent Forms** — formulario de contacto, ligero comparado con las alternativas
- **WebP Express** — conversión de imágenes, apoya el trabajo de Core Web Vitals

## Notas sobre producción

Este entorno replica el stack declarado en la ficha técnica. Cuando el cliente confirme las versiones de su servidor de destino, se ajustan las etiquetas de imagen en `docker-compose.yml` y `php/Dockerfile` y se reconstruye. Si su base de datos resulta ser MariaDB en lugar de MySQL, basta cambiar la imagen del servicio `db`.

## Empaquetado para producción

El cliente despliega por su cuenta, sin acceso del desarrollador. Todo lo de producción vive en `produccion/` y no afecta al entorno de desarrollo.

```bash
./scripts/generar-dump-limpio.sh     # contenido inicial limpio, a partir de la base de desarrollo
./scripts/empaquetar.sh 1.0.0        # imágenes + fuente + scripts + manual -> entrega/garantiza-1.0.0/
```

| Ruta | Qué es |
| --- | --- |
| `produccion/Dockerfile` | Tres imágenes inmutables: `wp`, `nginx` y `db` |
| `produccion/docker-stack.yml` | Stack de Swarm + Traefik, con secrets |
| `produccion/MANUAL-DESPLIEGUE.md` | Manual para el área de infraestructura del cliente |
| `mu-plugins/gz-retencion.php` | Eliminación automática de registros a los 5 años |
| `.gitlab-ci.yml` | Construcción de las imágenes en el GitLab del cliente |

Antes de entregar, ensayar el despliegue completo en este servidor siguiendo solo el manual (ver `produccion/docker-stack.ensayo.yml`).
