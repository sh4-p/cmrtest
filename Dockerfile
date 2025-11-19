# ==============================================================================
# Stage 1: Node.js - Build Frontend Assets
# ==============================================================================
FROM node:20-alpine AS node-builder

WORKDIR /var/www

# Copy package files
COPY package*.json ./

# Install Node dependencies
RUN npm ci --no-audit --no-fund

# Copy application files needed for build
COPY . .

# Build frontend assets for production
RUN npm run build

# ==============================================================================
# Stage 2: PHP 8.4 - Application Runtime
# ==============================================================================
FROM php:8.4-fpm

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    libzip-dev \
    libpq-dev \
    supervisor \
    cron \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN docker-php-ext-install \
    pdo_mysql \
    pdo_pgsql \
    mbstring \
    exif \
    pcntl \
    bcmath \
    gd \
    zip \
    opcache

# Install Redis extension
RUN pecl install redis && docker-php-ext-enable redis

# Configure PHP for production
RUN cp "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"

# Get latest Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www

# Copy composer files
COPY composer*.json ./

# Install PHP dependencies (production mode)
RUN composer install --no-dev --optimize-autoloader --no-interaction --no-progress

# Copy application files
COPY . /var/www

# Copy built frontend assets from node-builder stage
COPY --from=node-builder /var/www/public/build /var/www/public/build

# Set proper permissions
RUN chown -R www-data:www-data /var/www \
    && chmod -R 755 /var/www/storage \
    && chmod -R 755 /var/www/bootstrap/cache

# Create Laravel scheduler cron job
RUN echo "* * * * * www-data cd /var/www && php artisan schedule:run >> /dev/null 2>&1" >> /etc/crontab

# Expose port 9000 for PHP-FPM
EXPOSE 9000

# Start PHP-FPM
CMD ["php-fpm"]
