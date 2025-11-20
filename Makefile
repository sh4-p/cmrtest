.PHONY: help build up down restart logs shell test migrate seed fresh install clean dev prod setup serve stop

# Detect if running in Codespace
IS_CODESPACE := $(shell if [ -n "$$CODESPACES" ]; then echo "true"; else echo "false"; fi)

# Default target
help:
	@echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
	@echo "  Advanced CRM - Docker Management Commands"
	@echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
	@echo ""
	@echo "  GitHub Codespace Commands:"
	@echo "    make setup        - One-time setup (deps + db + key)"
	@echo "    make serve        - Start Laravel + Vite servers"
	@echo "    make stop         - Stop all running servers"
	@echo ""
	@echo "  Docker Development Commands:"
	@echo "    make dev          - Start development environment with HMR"
	@echo "    make install      - Initial setup (deps + migrate + seed)"
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

# Development environment with Vite HMR
dev:
	@echo "🚀 Starting development environment..."
	cp -n .env.docker .env || true
	docker-compose --profile dev up -d
	@echo "✅ Development environment started!"
	@echo "📱 Application: http://localhost:8080"
	@echo "⚡ Vite HMR: http://localhost:5173"

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

# Wait for MySQL to be healthy
wait-for-mysql:
	@echo "⏳ Waiting for MySQL to be ready..."
	@timeout=60; \
	counter=0; \
	until docker-compose exec -T mysql mysqladmin ping -h localhost -u root -p$${DB_ROOT_PASSWORD:-root_password} --silent 2>/dev/null; do \
		counter=$$((counter + 1)); \
		if [ $$counter -gt $$timeout ]; then \
			echo "❌ MySQL failed to start within $$timeout seconds"; \
			exit 1; \
		fi; \
		echo "⏳ Waiting for MySQL... ($$counter/$$timeout)"; \
		sleep 1; \
	done
	@echo "✅ MySQL is ready!"

# Initial installation
install: up wait-for-mysql
	@echo "📦 Installing dependencies..."
	docker-compose exec app composer install
	docker-compose exec app npm install
	@echo "🔑 Generating application key..."
	docker-compose exec app php artisan key:generate
	@echo "🗃️  Running migrations..."
	docker-compose exec app php artisan migrate:fresh --seed
	@echo "🔗 Creating storage link..."
	docker-compose exec app php artisan storage:link
	@echo "✅ Installation completed!"
	@echo "📱 Visit: http://localhost:8080"
	@echo "👤 Login: admin@crm.test / password"

# Run migrations
migrate: wait-for-mysql
	@echo "🗃️  Running migrations..."
	docker-compose exec app php artisan migrate
	@echo "✅ Migrations completed!"

# Run seeders
seed: wait-for-mysql
	@echo "🌱 Running seeders..."
	docker-compose exec app php artisan db:seed
	@echo "✅ Seeding completed!"

# Fresh migration with seeding
fresh: wait-for-mysql
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

# ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
# GitHub Codespace Targets
# ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━

# One-time setup for Codespace
setup:
	@echo "🚀 Setting up CRM for GitHub Codespace..."
	@echo ""
	@echo "📋 Step 1/6: Checking environment..."
	@if [ ! -f .env ]; then \
		echo "📝 Creating .env file..."; \
		cp .env.example .env; \
	else \
		echo "✅ .env file exists"; \
	fi
	@echo ""
	@echo "📋 Step 2/6: Installing Composer dependencies..."
	/usr/bin/php /usr/local/bin/composer install --no-interaction --prefer-dist --optimize-autoloader
	@echo ""
	@echo "📋 Step 3/6: Installing NPM dependencies..."
	npm install
	@echo ""
	@echo "📋 Step 4/6: Generating application key..."
	/usr/bin/php artisan key:generate --force
	@echo ""
	@echo "📋 Step 5/6: Setting up SQLite database..."
	@touch database/database.sqlite
	/usr/bin/php artisan migrate:fresh --seed --force
	@echo ""
	@echo "📋 Step 6/6: Creating storage link..."
	/usr/bin/php artisan storage:link
	@echo ""
	@echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
	@echo "✅ Setup completed!"
	@echo ""
	@echo "👤 Test Users:"
	@echo "   • admin@advancedcrm.com (Super Admin)"
	@echo "   • admin@example.com     (Admin)"
	@echo "   • manager@example.com   (Manager)"
	@echo "   • sales@example.com     (Sales Rep)"
	@echo "   Password: password"
	@echo ""
	@echo "🎯 Next step: Run 'make serve' to start the servers"
	@echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"

# Start Laravel and Vite servers for Codespace
serve:
	@echo "🚀 Starting CRM application in Codespace..."
	@echo ""
	@if lsof -Pi :8000 -sTCP:LISTEN -t >/dev/null 2>&1 ; then \
		echo "⚠️  Port 8000 is in use. Stopping..."; \
		lsof -ti:8000 | xargs kill -9 2>/dev/null || true; \
		sleep 1; \
	fi
	@if lsof -Pi :5173 -sTCP:LISTEN -t >/dev/null 2>&1 ; then \
		echo "⚠️  Port 5173 is in use. Stopping..."; \
		lsof -ti:5173 | xargs kill -9 2>/dev/null || true; \
		sleep 1; \
	fi
	@echo "📱 Starting Laravel server on http://0.0.0.0:8000"
	@/usr/bin/php artisan serve --host=0.0.0.0 --port=8000 & \
	echo "⚡ Starting Vite dev server on http://0.0.0.0:5173"; \
	npm run dev

# Stop all Codespace servers
stop:
	@echo "🛑 Stopping servers..."
	@pkill -f "php artisan serve" 2>/dev/null || true
	@pkill -f "vite" 2>/dev/null || true
	@lsof -ti:8000 -ti:5173 | xargs kill -9 2>/dev/null || true
	@echo "✅ All servers stopped"
