.PHONY: up down logs restart build wp shell db-dump db-restore prune install

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

install:       ## Instalación desatendida de WordPress
	docker compose run --rm wpcli core install \
		--url="$$(grep WP_URL .env | cut -d= -f2)" \
		--title="Landing" \
		--admin_user=admin \
		--admin_password="$$(openssl rand -base64 16)" \
		--admin_email=dev@localhost \
		--skip-email

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
