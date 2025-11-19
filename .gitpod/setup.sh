#!/bin/bash

# Gitpod Laravel CRM Setup Script
# This script sets up the Laravel CRM application in Gitpod environment

set -e

echo "🚀 Starting Laravel CRM setup for Gitpod..."

# Colors for output
GREEN='\033[0;32m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Step 1: Check if .env exists
if [ ! -f .env ]; then
    echo -e "${BLUE}📋 Creating .env file from .env.example...${NC}"
    cp .env.example .env
    php artisan key:generate
    echo -e "${GREEN}✅ .env file created${NC}"
fi

# Step 2: Configure database for Gitpod
echo -e "${BLUE}🗄️  Configuring MySQL database...${NC}"
mysql -u root -e "CREATE DATABASE IF NOT EXISTS laravel_crm;" 2>/dev/null || true
mysql -u root -e "CREATE USER IF NOT EXISTS 'laravel'@'localhost' IDENTIFIED BY 'secret';" 2>/dev/null || true
mysql -u root -e "GRANT ALL PRIVILEGES ON laravel_crm.* TO 'laravel'@'localhost';" 2>/dev/null || true
mysql -u root -e "FLUSH PRIVILEGES;" 2>/dev/null || true

# Update .env for Gitpod MySQL
sed -i 's/DB_CONNECTION=.*/DB_CONNECTION=mysql/' .env
sed -i 's/DB_HOST=.*/DB_HOST=127.0.0.1/' .env
sed -i 's/DB_PORT=.*/DB_PORT=3306/' .env
sed -i 's/DB_DATABASE=.*/DB_DATABASE=laravel_crm/' .env
sed -i 's/DB_USERNAME=.*/DB_USERNAME=laravel/' .env
sed -i 's/DB_PASSWORD=.*/DB_PASSWORD=secret/' .env

# Set APP_URL to Gitpod workspace URL
if [ -n "$GITPOD_WORKSPACE_URL" ]; then
    APP_URL=$(echo $GITPOD_WORKSPACE_URL | sed 's/https:\/\//https:\/\/8000-/')
    sed -i "s|APP_URL=.*|APP_URL=${APP_URL}|" .env
    echo -e "${GREEN}✅ APP_URL set to ${APP_URL}${NC}"
fi

echo -e "${GREEN}✅ Database configured${NC}"

# Step 3: Install Composer dependencies
if [ ! -d "vendor" ]; then
    echo -e "${BLUE}📦 Installing Composer dependencies...${NC}"
    composer install --no-interaction --prefer-dist --optimize-autoloader
    echo -e "${GREEN}✅ Composer dependencies installed${NC}"
fi

# Step 4: Install NPM dependencies
if [ ! -d "node_modules" ]; then
    echo -e "${BLUE}📦 Installing NPM dependencies...${NC}"
    npm install
    echo -e "${GREEN}✅ NPM dependencies installed${NC}"
fi

# Step 5: Build frontend assets
echo -e "${BLUE}🎨 Building frontend assets...${NC}"
npm run build
echo -e "${GREEN}✅ Frontend assets built${NC}"

# Step 6: Run migrations and seeders
echo -e "${BLUE}🗄️  Running database migrations and seeders...${NC}"
php artisan migrate:fresh --seed --force
echo -e "${GREEN}✅ Database migrated and seeded${NC}"

# Step 7: Clear caches
echo -e "${BLUE}🧹 Clearing caches...${NC}"
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear
echo -e "${GREEN}✅ Caches cleared${NC}"

# Step 8: Create storage link
if [ ! -L "public/storage" ]; then
    echo -e "${BLUE}🔗 Creating storage link...${NC}"
    php artisan storage:link
    echo -e "${GREEN}✅ Storage link created${NC}"
fi

echo ""
echo -e "${GREEN}🎉 Laravel CRM setup completed successfully!${NC}"
echo ""
echo -e "${BLUE}📝 Quick commands:${NC}"
echo -e "  ${GREEN}php artisan serve${NC}       - Start Laravel development server"
echo -e "  ${GREEN}npm run dev${NC}             - Start Vite dev server"
echo -e "  ${GREEN}php artisan tinker${NC}      - Open Laravel tinker console"
echo -e "  ${GREEN}php artisan migrate${NC}     - Run database migrations"
echo ""
echo -e "${BLUE}🌐 Your application will be available at:${NC}"
if [ -n "$GITPOD_WORKSPACE_URL" ]; then
    APP_URL=$(echo $GITPOD_WORKSPACE_URL | sed 's/https:\/\//https:\/\/8000-/')
    echo -e "  ${GREEN}${APP_URL}${NC}"
fi
echo ""
