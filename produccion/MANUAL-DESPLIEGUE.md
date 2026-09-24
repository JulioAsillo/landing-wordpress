# Landing Garantiza — Manual de despliegue

Sitio informativo en WordPress para **garantiza.pe**, empaquetado para Docker Swarm con Traefik como proxy inverso.

Este manual está pensado para ejecutarse sin asistencia del desarrollador. Tiempo estimado: 30 a 45 minutos.

---

## 1. Qué contiene el paquete

| Carpeta / archivo | Contenido |
| --- | --- |
| `imagenes/garantiza-imagenes-<versión>.tar.gz` | Las tres imágenes Docker ya construidas, para `docker load`. |
| `fuente/garantiza-fuente-<versión>.tar.gz` | Código fuente completo (tema, Dockerfile, `.gitlab-ci.yml`) para construir las imágenes en GitLab CI y para desarrollos futuros. |
| `despliegue/` | `docker-stack.yml`, `garantiza.env.example` y los scripts `crear-secrets.sh`, `desplegar.sh`, `verificar.sh`. |
| `SHA256SUMS` | Sumas de verificación de todo el paquete. |

Verificar la integridad antes de empezar:

```bash
sha256sum -c SHA256SUMS
```

### Arquitectura

```
Internet ──► Traefik (TLS, de la plataforma) ──► nginx ──► wordpress (PHP-FPM) ──► db (MySQL)
                                     red del proxy │        └──── red interna del stack ────┘
```

| Servicio | Imagen | Función | Datos persistentes |
| --- | --- | --- | --- |
| `nginx` | `garantiza-nginx` | Sirve estáticos, pasa PHP a WordPress, aplica cabeceras de seguridad y límite de intentos de inicio de sesión. Único servicio conectado a Traefik. | — |
| `wordpress` | `garantiza-wp` | WordPress 7.1 sobre PHP 8.4, con el tema y los plugins dentro (Fluent Forms 6.2.14, WP Mail SMTP 4.9.0). | Volumen `uploads` |
| `db` | `garantiza-db` | MySQL 8.4 con el contenido inicial del sitio. | Volumen `db_data` |

**Las imágenes son inmutables.** WordPress, el tema y los plugins viajan dentro de la imagen; la instalación y actualización desde el panel están deshabilitadas a propósito. Todo cambio de código llega como una versión nueva de la imagen (sección 11.3). Solo persisten la base de datos y los archivos subidos.

---

## 2. Requisitos

- Docker Swarm activo y acceso a un nodo **manager**.
- Traefik v2 o v3 desplegado en el Swarm, con un entrypoint HTTPS, un resolver de certificados (Let's Encrypt) y la redirección HTTP→HTTPS configurada a nivel de plataforma.
- Registro DNS tipo A de `garantiza.pe` apuntando a la IP pública de Traefik.
- Una cuenta de correo para los envíos del sitio (servidor SMTP, puerto, usuario y contraseña).
- Recursos del stack: 2 GB de RAM y 5 GB de disco son suficientes.

---

## 3. Ficha de parámetros

Completar antes de empezar. Todos estos valores van en `garantiza.env` (paso 6), salvo la contraseña SMTP, que va como secret (paso 5).

| Parámetro | Variable | Valor |
| --- | --- | --- |
| Dominio del sitio | `GZ_DOMINIO` | `garantiza.pe` |
| URL completa | `GZ_URL` | `https://garantiza.pe` |
| Prefijo del registry (con `/` final) | `GZ_IMAGE_PREFIX` | ______________________ |
| Red overlay de Traefik | `TRAEFIK_NETWORK` | ______________________ |
| Entrypoint HTTPS de Traefik | `TRAEFIK_ENTRYPOINT` | ______________________ |
| Resolver de certificados | `TRAEFIK_CERTRESOLVER` | ______________________ |
| Nodo que alojará los datos | (paso 4) | ______________________ |
| Usuario administrador de WordPress | `GZ_ADMIN_USER` | ______________________ |
| Correo del administrador | `GZ_ADMIN_EMAIL` | ______________________ |
| Servidor SMTP | `SMTP_HOST` | ______________________ |
| Puerto SMTP / cifrado | `SMTP_PORT` / `SMTP_ENCRYPTION` | ______ / ______ |
| Cuenta SMTP (usuario y remitente) | `SMTP_USER` / `SMTP_FROM` | ______________________ |

Dónde encontrar los datos de Traefik:

```bash
docker network ls --filter driver=overlay          # red del proxy
docker service inspect <servicio_traefik> --format '{{json .Spec.TaskTemplate.ContainerSpec.Args}}'
#   --entrypoints.<NOMBRE>.address=:443            -> TRAEFIK_ENTRYPOINT
#   --certificatesresolvers.<NOMBRE>.acme...       -> TRAEFIK_CERTRESOLVER
```

---

## 4. Cargar las imágenes

Elegir **una** de las dos opciones.

### Opción A — Imágenes ya construidas (`docker load`)

```bash
gunzip -c imagenes/garantiza-imagenes-1.0.0.tar.gz | docker load
docker images | grep garantiza
```

Si el Swarm tiene **más de un nodo**, las imágenes deben estar en un registry al que lleguen todos:

```bash
REG=registry.gitlab.empresa.pe/grupo/garantiza      # ajustar
for i in wp nginx db; do
  docker tag  garantiza-$i:1.0.0 $REG/garantiza-$i:1.0.0
  docker push $REG/garantiza-$i:1.0.0
done
# En garantiza.env:  GZ_IMAGE_PREFIX=registry.gitlab.empresa.pe/grupo/garantiza/
```

Con un único nodo puede omitirse el registry y dejar `GZ_IMAGE_PREFIX` vacío.

### Opción B — Construir en GitLab CI

1. Crear un proyecto en GitLab y subir el contenido de `fuente/garantiza-fuente-1.0.0.tar.gz`.
2. Crear el tag `v1.0.0`. El pipeline incluido (`.gitlab-ci.yml`) construye y publica las tres imágenes en el Container Registry del proyecto.
3. En `garantiza.env`: `GZ_IMAGE_PREFIX=<ruta del registry del proyecto>/`.

El pipeline es una plantilla para runners con Docker-in-Docker; si los runners usan otro método, solo cambian las líneas de build y push. El runner necesita salida a Docker Hub y a `downloads.wordpress.org`.

### Etiquetar el nodo de datos

Los volúmenes son locales al nodo, así que el stack se fija al nodo que tenga esta etiqueta (también con un solo nodo):

```bash
docker node ls
docker node update --label-add garantiza=true <NOMBRE_DEL_NODO>
```

---

## 5. Crear los secrets

```bash
cd despliegue
./crear-secrets.sh
```

| Secret | Contenido | Origen |
| --- | --- | --- |
| `gz_db_password` | Clave del usuario de MySQL que usa WordPress | Aleatoria |
| `gz_db_root_password` | Clave de root de MySQL | Aleatoria |
| `gz_smtp_password` | Clave de la cuenta de correo | Se pide por teclado |

Las claves no se escriben en ningún archivo ni quedan en el historial de la terminal. Swarm las guarda cifradas y las entrega a cada contenedor como un archivo en `/run/secrets/`.

> Las claves de MySQL solo se aplican la **primera vez** que arranca la base (volumen vacío). Cambiar el secret después no cambia la clave dentro de MySQL.

---

## 6. Completar los parámetros

```bash
cp garantiza.env.example garantiza.env
nano garantiza.env          # valores de la ficha del paso 3
```

---

## 7. Desplegar

```bash
./desplegar.sh
docker stack ps garantiza       # esperar a que los tres servicios estén en Running
```

El script valida que existan los parámetros, los secrets, la red de Traefik y el nodo etiquetado antes de desplegar. En el primer arranque MySQL importa el contenido inicial (hasta un minuto); mientras tanto el sitio puede mostrar "Error establishing a database connection".

---

## 8. Aprovisionamiento inicial (una sola vez)

En el **nodo etiquetado**:

```bash
docker exec -u www-data $(docker ps -q -f label=com.docker.swarm.service.name=garantiza_wordpress) gz-init.sh
```

Crea el usuario administrador indicado en `garantiza.env`, elimina el usuario provisional y muestra la contraseña **una sola vez**. Guardarla en el gestor de contraseñas y cambiarla en el primer ingreso.

Es seguro repetir el comando: si el sitio ya está aprovisionado no modifica nada.

---

## 9. Verificar

```bash
./verificar.sh --correo alguien@garantiza.pe
```

Comprueba los servicios, las páginas, los bloqueos de seguridad y envía un correo de prueba. Después, manualmente:

- [ ] Enviar el formulario de contacto: llega el correo a `contacto@garantiza.pe` y la confirmación al visitante.
- [ ] El certificado TLS es válido y `http://` redirige a `https://`.
- [ ] Ingresar a `https://garantiza.pe/wp-admin/` y cambiar la contraseña.

---

## 10. Pendientes fuera del stack

- **SPF, DKIM y DMARC** del dominio de la cuenta de envío: sin ellos los correos del formulario pueden caer en spam.
- **Redirección HTTP→HTTPS**: se configura en Traefik. La cabecera HSTS ya la emite el Nginx del stack (sin `includeSubDomains`); confirmar que Traefik no la sobrescribe con otro valor.
- **Mantenimiento de seguridad** (sección 11.6): designar al responsable antes de pasar a producción.
- **www**: si se publicará `www.garantiza.pe`, crear el registro DNS y activar las líneas comentadas de `docker-stack.yml`.
- **Respaldos** (sección 11.1): programarlos según la política interna.

---

## 11. Operación

### 11.1 Respaldo y restauración

Todo el estado del sitio son la base de datos y el volumen `uploads`. En el nodo etiquetado:

```bash
DB=$(docker ps -q -f label=com.docker.swarm.service.name=garantiza_db)
WP=$(docker ps -q -f label=com.docker.swarm.service.name=garantiza_wordpress)

# Respaldo
docker exec $DB sh -c 'MYSQL_PWD="$(cat /run/secrets/gz_db_root_password)" mysqldump -uroot --single-transaction --no-tablespaces garantiza' | gzip > garantiza-db-$(date +%F).sql.gz
docker exec $WP tar -czf - -C /var/www/html/wp-content uploads > garantiza-uploads-$(date +%F).tar.gz

# Restauración
gunzip -c garantiza-db-FECHA.sql.gz | docker exec -i $DB sh -c 'MYSQL_PWD="$(cat /run/secrets/gz_db_root_password)" mysql -uroot garantiza'
docker exec -i $WP tar -xzf - -C /var/www/html/wp-content < garantiza-uploads-FECHA.tar.gz
```

Los respaldos contienen datos personales: les aplica el mismo plazo de retención que al sitio.

### 11.2 Retención de datos personales

Los registros de los formularios se eliminan automáticamente al cumplir **5 años** (`GZ_RETENCION_ANIOS`). Lo hace un componente incluido en la imagen (`mu-plugins/gz-retencion.php`), una vez al día, y no puede desactivarse desde el panel.

```bash
docker exec -u www-data $WP wp gz-retencion --dry-run     # cuántos registros cumplen el plazo (no borra)
docker exec -u www-data $WP wp gz-retencion               # ejecutar ahora
```

Cada eliminación queda en el log del servicio (`docker service logs garantiza_wordpress | grep gz-retencion`). El alcance es la base de datos del sitio; las copias recibidas por correo y los respaldos se gestionan con las políticas de la empresa.

### 11.3 Actualizar a una versión nueva

```bash
# cargar o construir las imágenes nuevas (paso 4), luego:
nano garantiza.env            # GZ_VERSION=1.1.0
./desplegar.sh
./verificar.sh
```

La base de datos y los archivos subidos se conservan. El contenido inicial incluido en `garantiza-db` solo se importa con el volumen vacío: nunca sobrescribe datos existentes. Al recrearse el contenedor de WordPress se cierran las sesiones abiertas del panel; basta volver a ingresar.

**Volver atrás:** poner la versión anterior en `GZ_VERSION` y ejecutar `desplegar.sh`.

### 11.4 Rotar la contraseña SMTP

Los secrets de Swarm son inmutables; se crea uno nuevo y se apunta el stack a él:

```bash
./crear-secrets.sh gz_smtp_password_v2
nano garantiza.env            # GZ_SECRET_SMTP=gz_smtp_password_v2
./desplegar.sh
docker secret rm gz_smtp_password
```

### 11.5 Comandos útiles

```bash
docker service logs -f garantiza_wordpress
docker exec -u www-data $WP wp user list
docker exec -u www-data $WP wp user update <usuario> --user_pass='<nueva>'     # recuperar acceso
docker volume prune        # cada redespliegue deja un volumen anónimo huérfano
```

### 11.6 Mantenimiento de seguridad (responsabilidad del cliente)

Las imágenes son inmutables: el sitio **no se actualiza solo** y el panel no permite instalar ni actualizar nada. Cada parche de seguridad llega como una versión nueva de la imagen (sección 11.3). Por eso alguien debe vigilar los avisos y decidir cuándo publicar.

| Dato | Valor (lo completa el cliente) |
| --- | --- |
| Responsable de vigilar los avisos | `____________________` |
| Periodicidad de revisión | `____________________` (recomendado: semanal) |
| Plazo para aplicar un parche de seguridad | `____________________` (recomendado: 7 días si es crítico o alto, 30 días el resto) |

Componentes a vigilar y dónde se fija su versión (`produccion/Dockerfile`):

| Componente | ARG | Dónde mirar los avisos |
| --- | --- | --- |
| WordPress 7.1 (PHP 8.4) | `WP_TAG` | wordpress.org/news/category/security |
| Fluent Forms | `FLUENTFORM_VERSION` | wordpress.org/plugins/fluentform (pestaña Development) y wpscan.com / wordfence.com |
| WP Mail SMTP | `WPMAILSMTP_VERSION` | wordpress.org/plugins/wp-mail-smtp y wpscan.com / wordfence.com |
| Nginx 1.30 | `NGINX_TAG` | nginx.org (sección news) |
| MySQL 8.4 LTS | `MYSQL_TAG` | dev.mysql.com (notas de versión) |

Procedimiento: cambiar el ARG, crear un tag nuevo (por ejemplo `v1.0.1`) para que GitLab CI construya y analice las imágenes, y desplegar con la sección 11.3. Las etiquetas `7.1-php8.4-fpm-alpine`, `1.30-alpine` y `8.4` recogen los parches de su rama con solo reconstruir, aunque el ARG no cambie.

El trabajo `analisis` de `.gitlab-ci.yml` revisa las imágenes con Trivy y falla ante vulnerabilidades altas o críticas con corrección disponible. Cubre el sistema base y PHP; **no** detecta vulnerabilidades de los plugins de WordPress, que siguen dependiendo de la vigilancia de esta sección.

---

## 12. Problemas frecuentes

| Síntoma | Causa probable | Qué revisar |
| --- | --- | --- |
| Traefik responde 404 | Traefik no ve el servicio | `TRAEFIK_NETWORK` es la red a la que Traefik está conectado; nombre del entrypoint. |
| Aviso de certificado inválido | Resolver mal escrito o DNS sin propagar | `TRAEFIK_CERTRESOLVER`; registro A; logs de Traefik. |
| Un servicio queda en `Pending` | Ningún nodo cumple la restricción | Etiqueta `garantiza=true` (paso 4). |
| `No such image` en otro nodo | Imagen cargada solo en un nodo | Usar registry y `GZ_IMAGE_PREFIX`. |
| "Error establishing a database connection" persistente | MySQL no terminó de iniciar o falló la importación | `docker service logs garantiza_db`. |
| Bucle de redirecciones | Traefik no envía `X-Forwarded-Proto` o `GZ_URL` no coincide con el dominio | `GZ_URL`; configuración de cabeceras de Traefik. |
| El formulario no envía correo | Credenciales SMTP o puerto bloqueado | `./verificar.sh --correo ...`; salida del nodo hacia el puerto SMTP; que `SMTP_FROM` sea la cuenta autenticada. |

### Desinstalar

```bash
docker stack rm garantiza
docker volume rm garantiza_db_data garantiza_uploads     # ELIMINA todos los datos
docker secret rm gz_db_password gz_db_root_password gz_smtp_password
```
