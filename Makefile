.PHONY: up down logs restart build wp shell db-dump db-restore prune install debug-log mail-test mail-check

up:            ## Levanta todo el stack
	docker compose up -d

down:          ## Detiene los contenedores (conserva los datos)
	docker compose down

build:         ## Reconstruye la imagen de PHP
	docker compose build --no-cache wordpress

logs:          ## Sigue los logs en vivo
	docker compose logs -f --tail=100

restart:
	docker compose restart

shell:         ## Entra al contenedor de WordPress
	docker compose exec wordpress sh

wp:            ## Ejecuta WP-CLI. Uso: make wp CMD="plugin list"
	docker compose run --rm wpcli $(CMD)

install:       ## Instalación desatendida (usuario no predecible; muestra la contraseña una vez)
	@PASS="$$(openssl rand -base64 18)"; \
	ADMIN="$$(grep WP_ADMIN_USER .env | cut -d= -f2)"; \
	docker compose run --rm wpcli core install \
		--url="$$(grep WP_URL .env | cut -d= -f2)" \
		--title="Landing" \
		--admin_user="$$ADMIN" \
		--admin_password="$$PASS" \
		--admin_email="$$(grep WP_ADMIN_EMAIL .env | cut -d= -f2)" \
		--skip-email && \
	echo "Usuario: $$ADMIN  Contraseña: $$PASS  (guárdala en tu gestor de contraseñas)"

mail-check:    ## Muestra la configuración SMTP activa (sin revelar la contraseña)
	@docker compose run --rm -T wpcli eval 'foreach (["WPMS_ON","WPMS_MAILER","WPMS_SMTP_HOST","WPMS_SMTP_PORT","WPMS_SSL","WPMS_SMTP_AUTH","WPMS_SMTP_USER","WPMS_MAIL_FROM"] as $$c) { printf("%-18s %s\n", $$c, var_export(defined($$c) ? constant($$c) : null, true)); } printf("%-18s %s\n", "WPMS_SMTP_PASS", defined("WPMS_SMTP_PASS") && WPMS_SMTP_PASS !== "" ? "(definida)" : "(vacía)");'

mail-test:     ## Envía un correo de prueba. Uso: make mail-test TO=alguien@dominio.com
	@test -n "$(TO)" || (echo "Falta TO. Uso: make mail-test TO=alguien@dominio.com"; exit 1)
	@docker compose run --rm -T wpcli eval 'var_dump(wp_mail("$(TO)", "Prueba de envio - Landing Garantiza", "Correo de prueba enviado desde el sitio."));'

debug-log:     ## Sigue el log de depuración de WordPress (fuera de la raíz web)
	docker compose exec wordpress sh -c 'touch /tmp/wp-debug.log && tail -f /tmp/wp-debug.log'

db-dump:       ## Respaldo de la base de datos con fecha
	@mkdir -p backups
	docker compose exec -T db sh -c 'MYSQL_PWD="$$MYSQL_ROOT_PASSWORD" exec mysqldump -u root --single-transaction --no-tablespaces "$$MYSQL_DATABASE"' \
		> backups/dump-$$(date +%Y%m%d-%H%M).sql
	@echo "Respaldo generado en backups/"

db-restore:    ## Restaura. Uso: make db-restore FILE=backups/dump-xxx.sql
	docker compose exec -T db sh -c 'MYSQL_PWD="$$MYSQL_ROOT_PASSWORD" exec mysql -u root "$$MYSQL_DATABASE"' < $(FILE)

prune:         ## Libera espacio en disco (importante: solo hay 5.4 GB)
	docker system prune -af --volumes=false
	df -hT /
