.PHONY: help build up down restart logs shell test migrate seed fresh install clean dev prod

# Default target
help:
	@echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
	@echo "  Advanced CRM - Docker Management Commands"
	@echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
	@echo ""
	@echo "  Development Commands:"
	@echo "    make install      - Initial setup (deps + migrate + seed + build)"
	@echo "    make dev          - Start development environment (no HMR)"
	@echo "    make dev-hmr      - Start with Vite HMR (local dev only)"
	@echo "    make build-assets - Build frontend assets for production"
	@echo "    make up           - Start all containers"
	@echo "    make down         - Stop all containers"
	@echo "    make restart      - Restart all containers"
	@echo "    make logs         - View logs (all services)"
	@echo "    make shell        - Access app container shell"
	@echo ""
	@echo "  Database Commands:"
	@echo "    make migrate      - Run migrations"
	@echo "    make seed         - Run database seeders"
	@echo "    make fresh        - Fresh migration + seed"
	@echo "    make db-backup    - Backup database"
	@echo "    make db-shell     - Access MySQL CLI"
	@echo ""
	@echo "  Build & Deploy:"
	@echo "    make build        - Build containers"
	@echo "    make prod         - Start production environment"
	@echo "    make optimize     - Optimize for production"
	@echo ""
	@echo "  Testing & Quality:"
	@echo "    make test         - Run PHPUnit tests"
	@echo "    make pint         - Run Laravel Pint (code style)"
	@echo "    make analyze      - Run static analysis"
	@echo ""
	@echo "  Maintenance:"
	@echo "    make clean        - Clean cache and volumes"
	@echo "    make reset        - Full reset (remove all data)"
	@echo "    make update       - Update dependencies"
	@echo ""
	@echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"

# Development environment (production build, recommended for Codespaces)
dev:
	@echo "🚀 Starting development environment..."
	cp -n .env.docker .env || true
	docker-compose up -d
	@echo "✅ Development environment started!"
	@echo "📱 Application: http://localhost:8080"
	@echo "💡 Using production build (no HMR)"
	@echo "   For HMR support: make dev-hmr"

# Development environment with Vite HMR (local development only)
dev-hmr:
	@echo "🚀 Starting development environment with HMR..."
	cp -n .env.docker .env || true
	docker-compose --profile dev up -d
	@echo "✅ Development environment started!"
	@echo "📱 Application: http://localhost:8080"
	@echo "⚡ Vite HMR: http://localhost:5173"
	@echo ""
	@echo "⚠️  Note: HMR may not work in Codespaces/cloud IDEs"
	@echo "   If you see blank page, use 'make dev' instead"

# Build frontend assets
build-assets:
	@echo "🏗️  Building frontend assets..."
	docker-compose exec app npm run build
	@echo "✅ Assets built successfully!"

# Production environment
prod:
	@echo "🚀 Starting production environment..."
	DOCKERFILE=Dockerfile APP_ENV=production docker-compose up -d --build
	@echo "✅ Production environment started!"
	@echo "📱 Application: http://localhost:8080"

# Build containers
build:
	@echo "🔨 Building Docker containers..."
	docker-compose build --no-cache
	@echo "✅ Build completed!"

# Start containers
up:
	@echo "🚀 Starting containers..."
	docker-compose up -d
	@echo "✅ Containers started!"

# Stop containers
down:
	@echo "🛑 Stopping containers..."
	docker-compose down
	@echo "✅ Containers stopped!"

# Restart containers
restart:
	@echo "🔄 Restarting containers..."
	docker-compose restart
	@echo "✅ Containers restarted!"

# View logs
logs:
	docker-compose logs -f

# View specific service logs
logs-app:
	docker-compose logs -f app

logs-nginx:
	docker-compose logs -f nginx

logs-mysql:
	docker-compose logs -f mysql

# Access app container shell
shell:
	docker-compose exec app bash

# Access MySQL CLI
db-shell:
	docker-compose exec mysql mysql -u crm_user -pcrm_password advanced_crm

# Wait for database to be ready
wait-for-db:
	@echo "⏳ Waiting for MySQL to be ready..."
	@timeout=60; \
	while ! docker-compose exec -T mysql mysqladmin ping -h localhost -u root -proot_password --silent 2>/dev/null; do \
		timeout=$$((timeout - 1)); \
		if [ $$timeout -le 0 ]; then \
			echo "❌ MySQL failed to start in time"; \
			exit 1; \
		fi; \
		echo "  Waiting for MySQL... ($$timeout seconds remaining)"; \
		sleep 2; \
	done
	@echo "✅ MySQL is ready!"

# Initial installation
install: up wait-for-db
	@echo "📦 Installing dependencies..."
	docker-compose exec app composer install
	docker-compose exec app npm install
	@echo "🏗️  Building frontend assets..."
	docker-compose exec app npm run build
	@echo "🔑 Generating application key..."
	docker-compose exec app php artisan key:generate
	@echo "🗃️  Running migrations..."
	docker-compose exec app php artisan migrate:fresh --seed
	@echo "🔗 Creating storage link..."
	docker-compose exec app php artisan storage:link
	@echo "✅ Installation completed!"
	@echo "📱 Visit: http://localhost:8080"
	@echo "👤 Login: admin@crm.test / password"
	@echo ""
	@echo "💡 Note: Using production build (no HMR)"
	@echo "   To enable HMR: make dev-hmr"

# Run migrations
migrate:
	@echo "🗃️  Running migrations..."
	docker-compose exec app php artisan migrate
	@echo "✅ Migrations completed!"

# Run seeders
seed:
	@echo "🌱 Running seeders..."
	docker-compose exec app php artisan db:seed
	@echo "✅ Seeding completed!"

# Fresh migration with seeding
fresh:
	@echo "🗃️  Fresh migration with seeding..."
	docker-compose exec app php artisan migrate:fresh --seed
	@echo "✅ Database refreshed!"

# Backup database
db-backup:
	@echo "💾 Creating database backup..."
	mkdir -p backups
	docker-compose exec -T mysql mysqldump -u root -proot_password advanced_crm > backups/backup_$(shell date +%Y%m%d_%H%M%S).sql
	@echo "✅ Backup created in backups/ directory"

# Run tests
test:
	@echo "🧪 Running tests..."
	docker-compose exec app php artisan test

# Run Laravel Pint (code style)
pint:
	@echo "🎨 Running Laravel Pint..."
	docker-compose exec app ./vendor/bin/pint

# Static analysis
analyze:
	@echo "🔍 Running static analysis..."
	docker-compose exec app ./vendor/bin/phpstan analyze || true

# Optimize for production
optimize:
	@echo "⚡ Optimizing application..."
	docker-compose exec app php artisan optimize
	docker-compose exec app php artisan config:cache
	docker-compose exec app php artisan route:cache
	docker-compose exec app php artisan view:cache
	docker-compose exec app composer install --optimize-autoloader --no-dev
	@echo "✅ Optimization completed!"

# Clear cache
cache-clear:
	@echo "🧹 Clearing cache..."
	docker-compose exec app php artisan cache:clear
	docker-compose exec app php artisan config:clear
	docker-compose exec app php artisan route:clear
	docker-compose exec app php artisan view:clear
	@echo "✅ Cache cleared!"

# Clean everything
clean: cache-clear
	@echo "🧹 Cleaning volumes and cache..."
	docker-compose down -v
	@echo "✅ Cleaned!"

# Full reset (DANGEROUS - removes all data)
reset:
	@echo "⚠️  WARNING: This will delete all data!"
	@read -p "Are you sure? [y/N] " -n 1 -r; \
	echo; \
	if [[ $$REPLY =~ ^[Yy]$$ ]]; then \
		docker-compose down -v; \
		docker system prune -af --volumes; \
		echo "✅ Full reset completed!"; \
	else \
		echo "❌ Reset cancelled."; \
	fi

# Update dependencies
update:
	@echo "📦 Updating dependencies..."
	docker-compose exec app composer update
	docker-compose exec app npm update
	@echo "✅ Dependencies updated!"

# Check service health
health:
	@echo "🏥 Checking service health..."
	docker-compose ps
	@echo ""
	@echo "📊 Container Stats:"
	docker stats --no-stream crm-app crm-nginx crm-mysql crm-redis

# Run queue worker
queue:
	docker-compose --profile queue up -d

# Run scheduler
scheduler:
	docker-compose --profile scheduler up -d

# Generate IDE helper files
ide-helper:
	@echo "💡 Generating IDE helper files..."
	docker-compose exec app php artisan ide-helper:generate
	docker-compose exec app php artisan ide-helper:models --nowrite
	docker-compose exec app php artisan ide-helper:meta
	@echo "✅ IDE helper files generated!"

# Show service URLs
urls:
	@echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
	@echo "  Service URLs"
	@echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
	@echo ""
	@echo "  🌐 Application:    http://localhost:8080"
	@echo "  ⚡ Vite Dev:       http://localhost:5173"
	@echo "  🗄️  MySQL:          localhost:3306"
	@echo "  🔴 Redis:          localhost:6379"
	@echo ""
	@echo "  👤 Demo Users:"
	@echo "     • admin@crm.test    (Super Admin)"
	@echo "     • manager@crm.test  (Manager)"
	@echo "     • sales@crm.test    (Sales Rep)"
	@echo "     Password: password"
	@echo ""
	@echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"

# Quick start (alias for dev + install)
start: dev install urls
	@echo ""
	@echo "🎉 System is ready to use!"
