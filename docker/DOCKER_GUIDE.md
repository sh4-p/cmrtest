# Docker Deployment Guide

Complete guide for running the Advanced CRM system with Docker.

## 📋 Prerequisites

- Docker Engine 20.10+
- Docker Compose 2.0+
- Git

## 🚀 Quick Start

### Development Mode

```bash
# 1. Clone the repository
git clone <your-repo-url>
cd cmrtest

# 2. Copy Docker environment file
cp .env.docker .env

# 3. Generate application key
docker-compose run --rm app php artisan key:generate

# 4. Build and start services
docker-compose --profile dev up -d --build

# 5. Run migrations and seeders
docker-compose exec app php artisan migrate:fresh --seed

# 6. Access the application
# Frontend: http://localhost:8080
# Vite HMR: http://localhost:5173
# MySQL: localhost:3306
# Redis: localhost:6379
```

### Production Mode

```bash
# 1. Set environment to production
export DOCKERFILE=Dockerfile
export APP_ENV=production

# 2. Build production image
docker-compose build --no-cache

# 3. Start services (without dev profile)
docker-compose up -d

# 4. Run migrations
docker-compose exec app php artisan migrate --force

# 5. Optimize application
docker-compose exec app php artisan optimize
docker-compose exec app php artisan config:cache
docker-compose exec app php artisan route:cache
docker-compose exec app php artisan view:cache
```

## 📦 Services

### Main Services (Always Running)

| Service | Container Name | Ports | Description |
|---------|---------------|-------|-------------|
| app | crm-app | 9000 | PHP 8.4-FPM application |
| nginx | crm-nginx | 8080, 443 | Web server |
| mysql | crm-mysql | 3306 | MySQL 8.0 database |
| redis | crm-redis | 6379 | Redis cache & queue |

### Development Services (Profile: dev)

| Service | Container Name | Ports | Description |
|---------|---------------|-------|-------------|
| node | crm-node | 5173 | Vite dev server with HMR |

### Optional Services

| Service | Profile | Description |
|---------|---------|-------------|
| queue | queue | Laravel queue worker |
| scheduler | scheduler | Laravel task scheduler |

## 🔧 Configuration

### Environment Variables

Create `.env` file from `.env.docker` template:

```bash
cp .env.docker .env
```

Key Docker-specific variables:

```env
# Docker Configuration
DOCKERFILE=Dockerfile.dev          # Use Dockerfile for production
APP_PORT=8080                      # Nginx HTTP port
VITE_PORT=5173                     # Vite dev server port
DB_HOST=mysql                      # MySQL service name
REDIS_HOST=redis                   # Redis service name
```

### Docker Compose Profiles

Run specific service combinations:

```bash
# Development with Vite HMR
docker-compose --profile dev up -d

# With queue worker
docker-compose --profile queue up -d

# With scheduler
docker-compose --profile scheduler up -d

# All services
docker-compose --profile dev --profile queue --profile scheduler up -d
```

## 🛠️ Common Commands

### Application Management

```bash
# Start all services
docker-compose up -d

# Stop all services
docker-compose down

# View logs
docker-compose logs -f app
docker-compose logs -f nginx

# Restart a service
docker-compose restart app

# Execute artisan commands
docker-compose exec app php artisan migrate
docker-compose exec app php artisan db:seed
docker-compose exec app php artisan cache:clear

# Install/Update dependencies
docker-compose exec app composer install
docker-compose exec app npm install

# Access container shell
docker-compose exec app bash
docker-compose exec mysql mysql -u crm_user -pcrm_password advanced_crm
```

### Database Operations

```bash
# Fresh migration with seeding
docker-compose exec app php artisan migrate:fresh --seed

# Create database backup
docker-compose exec mysql mysqldump -u root -proot_password advanced_crm > backup.sql

# Restore database
docker-compose exec -T mysql mysql -u root -proot_password advanced_crm < backup.sql

# Access MySQL CLI
docker-compose exec mysql mysql -u crm_user -pcrm_password advanced_crm
```

### Build & Deploy

```bash
# Rebuild specific service
docker-compose build app

# Rebuild without cache
docker-compose build --no-cache

# Build and restart
docker-compose up -d --build

# View service status
docker-compose ps

# Check health status
docker-compose ps
```

## 📊 Dockerfile Overview

### Dockerfile (Production)

Multi-stage build for optimized production image:

**Stage 1: Node.js Builder**
- Base: `node:20-alpine`
- Builds frontend assets (`npm run build`)
- Optimized production build

**Stage 2: PHP Runtime**
- Base: `php:8.4-fpm`
- PHP extensions: pdo_mysql, redis, gd, zip, opcache
- Composer dependencies (--no-dev --optimize-autoloader)
- Copies built assets from Stage 1
- Production PHP configuration
- Cron job for Laravel scheduler

### Dockerfile.dev (Development)

Development-friendly image:

- Base: `php:8.4-fpm`
- Node.js 20 installed alongside PHP
- Xdebug enabled for debugging
- Development PHP configuration
- Hot module replacement support

## 🔐 Security Best Practices

### Production Checklist

- [ ] Use production Dockerfile
- [ ] Set `APP_ENV=production`
- [ ] Set `APP_DEBUG=false`
- [ ] Generate strong `APP_KEY`
- [ ] Use strong database passwords
- [ ] Enable HTTPS (configure SSL certificates)
- [ ] Restrict exposed ports
- [ ] Use Docker secrets for sensitive data
- [ ] Enable firewall rules
- [ ] Regular security updates

### Example Production .env

```env
APP_ENV=production
APP_DEBUG=false
APP_KEY=base64:YOUR_GENERATED_KEY_HERE

DB_PASSWORD=STRONG_RANDOM_PASSWORD
DB_ROOT_PASSWORD=STRONG_ROOT_PASSWORD

# Use external services in production
REDIS_PASSWORD=STRONG_REDIS_PASSWORD
MAIL_MAILER=smtp
QUEUE_CONNECTION=redis
CACHE_STORE=redis
```

## 🔍 Troubleshooting

### Container won't start

```bash
# Check logs
docker-compose logs app

# Verify configuration
docker-compose config

# Rebuild from scratch
docker-compose down -v
docker-compose build --no-cache
docker-compose up -d
```

### Permission issues

```bash
# Fix storage permissions
docker-compose exec app chmod -R 775 storage bootstrap/cache
docker-compose exec app chown -R www-data:www-data storage bootstrap/cache
```

### Database connection refused

```bash
# Wait for MySQL to be ready
docker-compose exec mysql mysqladmin ping -h localhost -u root -proot_password

# Check MySQL logs
docker-compose logs mysql

# Verify DB credentials in .env
docker-compose exec app cat .env | grep DB_
```

### Frontend assets not loading

```bash
# Rebuild assets
docker-compose exec app npm run build

# Clear cache
docker-compose exec app php artisan optimize:clear

# Check Nginx logs
docker-compose logs nginx
```

### Vite HMR not working

```bash
# Ensure node service is running
docker-compose --profile dev up -d

# Check Vite configuration
# Make sure vite.config.js has:
server: {
    host: '0.0.0.0',
    hmr: {
        host: 'localhost'
    }
}
```

## 📈 Performance Optimization

### Production Optimization

```bash
# Cache configuration
docker-compose exec app php artisan config:cache
docker-compose exec app php artisan route:cache
docker-compose exec app php artisan view:cache

# Optimize Composer autoloader
docker-compose exec app composer install --optimize-autoloader --no-dev

# Enable OPcache (already enabled in production Dockerfile)
```

### Resource Limits

Add to `docker-compose.yml`:

```yaml
services:
  app:
    deploy:
      resources:
        limits:
          cpus: '2'
          memory: 2G
        reservations:
          cpus: '1'
          memory: 512M
```

## 🔄 Backup & Restore

### Automated Backup Script

```bash
#!/bin/bash
BACKUP_DIR="./backups"
DATE=$(date +%Y%m%d_%H%M%S)

# Create backup directory
mkdir -p $BACKUP_DIR

# Backup database
docker-compose exec -T mysql mysqldump -u root -proot_password advanced_crm > $BACKUP_DIR/db_$DATE.sql

# Backup storage
tar -czf $BACKUP_DIR/storage_$DATE.tar.gz storage/app

echo "Backup completed: $BACKUP_DIR"
```

### Restore from Backup

```bash
# Restore database
docker-compose exec -T mysql mysql -u root -proot_password advanced_crm < backups/db_20250119_120000.sql

# Restore storage
tar -xzf backups/storage_20250119_120000.tar.gz
```

## 📚 Additional Resources

- [Docker Documentation](https://docs.docker.com/)
- [Docker Compose Documentation](https://docs.docker.com/compose/)
- [Laravel Documentation](https://laravel.com/docs)
- [PHP-FPM Configuration](https://www.php.net/manual/en/install.fpm.php)
- [Nginx Configuration](https://nginx.org/en/docs/)

## 🆘 Support

For issues and questions:
- Check container logs: `docker-compose logs`
- Verify service health: `docker-compose ps`
- Review Laravel logs: `storage/logs/laravel.log`
