.PHONY: up down logs restart build wp shell db-dump db-restore prune install debug-log

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

debug-log:     ## Sigue el log de depuración de WordPress (fuera de la raíz web)
	docker compose exec wordpress sh -c 'touch /tmp/wp-debug.log && tail -f /tmp/wp-debug.log'

db-dump:       ## Respaldo de la base de datos con fecha
	@mkdir -p backups
	docker compose exec -T db sh -c 'mysqldump -u root -p"$$MYSQL_ROOT_PASSWORD" "$$MYSQL_DATABASE"' \
		> backups/dump-$$(date +%Y%m%d-%H%M).sql
	@echo "Respaldo generado en backups/"

db-restore:    ## Restaura. Uso: make db-restore FILE=backups/dump-xxx.sql
	docker compose exec -T db sh -c 'mysql -u root -p"$$MYSQL_ROOT_PASSWORD" "$$MYSQL_DATABASE"' < $(FILE)

prune:         ## Libera espacio en disco (importante: solo hay 5.4 GB)
	docker system prune -af --volumes=false
	df -hT /
