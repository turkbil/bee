# Multi-stage Dockerfile for Laravel 500 Tenant System
# Stage 1: Base PHP-FPM with extensions
FROM php:8.2-fpm-alpine AS base

# Install system dependencies
RUN apk add --no-cache \
    git \
    curl \
    libpng-dev \
    libxml2-dev \
    zip \
    unzip \
    oniguruma-dev \
    libzip-dev \
    freetype-dev \
    libjpeg-turbo-dev \
    libwebp-dev \
    icu-dev \
    mysql-client \
    redis \
    supervisor \
    nginx \
    autoconf \
    gcc \
    g++ \
    make

# Install PHP extensions
RUN docker-php-ext-configure gd --with-freetype --with-jpeg --with-webp \
    && docker-php-ext-install \
    pdo \
    pdo_mysql \
    mbstring \
    exif \
    pcntl \
    bcmath \
    gd \
    zip \
    intl \
    opcache

# Install Redis extension
RUN pecl install redis && docker-php-ext-enable redis

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Stage 2: Dependencies installation
FROM base AS dependencies

WORKDIR /var/www/html

# Copy composer files
COPY composer.json composer.lock ./

# Install PHP dependencies (production optimized)
RUN composer install --no-dev --optimize-autoloader --no-interaction --no-progress

# Stage 3: Assets (no build needed - Vite removed)
FROM base AS assets

WORKDIR /var/www/html

# Copy public assets directly (no build process needed)
COPY public/ public/
COPY resources/ resources/

# No npm build process since Vite was removed for tenant compatibility

# Stage 4: Development image
FROM base AS development

# Install Xdebug for development
RUN pecl install xdebug && docker-php-ext-enable xdebug

# Install Node.js for development
RUN apk add --no-cache nodejs npm

WORKDIR /var/www/html

# Copy composer files first for better layer caching
COPY composer.json composer.lock ./

# Install all dependencies (including dev)
RUN composer install --optimize-autoloader --no-interaction

# Copy application code
COPY . .

# Set development permissions
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html/storage \
    && chmod -R 755 /var/www/html/bootstrap/cache

# Copy development configurations
COPY docker/php-dev.ini /usr/local/etc/php/conf.d/
COPY docker/php-fpm-dev.conf /usr/local/etc/php-fpm.d/www.conf
COPY docker/nginx-dev.conf /etc/nginx/nginx.conf

ENV APP_ENV=local
ENV APP_DEBUG=true
ENV LOG_CHANNEL=stderr

EXPOSE 80

CMD ["sh", "-c", "php-fpm & nginx -g 'daemon off;'"]

# Stage 5: Production image
FROM base AS production

WORKDIR /var/www/html

# Copy application code
COPY . .

# Copy optimized dependencies from previous stages
COPY --from=dependencies /var/www/html/vendor vendor/
COPY --from=assets /var/www/html/public public/

# Set proper permissions
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html/storage \
    && chmod -R 755 /var/www/html/bootstrap/cache \
    && chmod -R 644 /var/www/html/.env*

# Copy configurations
COPY docker/php.ini /usr/local/etc/php/conf.d/
COPY docker/php-fpm.conf /usr/local/etc/php-fpm.d/www.conf
COPY docker/nginx.conf /etc/nginx/nginx.conf
COPY docker/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# Create optimized startup script with broken pipe fix
RUN echo '#!/bin/sh' > /usr/local/bin/start.sh \
    && echo 'set -e' >> /usr/local/bin/start.sh \
    && echo 'echo "🚀 Starting Laravel 500 Tenant System..."' >> /usr/local/bin/start.sh \
    && echo '# Buffer ayarları' >> /usr/local/bin/start.sh \
    && echo 'ulimit -n 65536' >> /usr/local/bin/start.sh \
    && echo 'echo "📦 Clearing caches safely..."' >> /usr/local/bin/start.sh \
    && echo 'php artisan config:clear || true' >> /usr/local/bin/start.sh \
    && echo 'sleep 1' >> /usr/local/bin/start.sh \
    && echo 'php artisan config:cache || true' >> /usr/local/bin/start.sh \
    && echo 'php artisan route:cache || true' >> /usr/local/bin/start.sh \
    && echo 'php artisan view:cache || true' >> /usr/local/bin/start.sh \
    && echo 'echo "📊 Running database migrations..."' >> /usr/local/bin/start.sh \
    && echo 'php artisan migrate --force' >> /usr/local/bin/start.sh \
    && echo 'echo "🌱 Seeding database..."' >> /usr/local/bin/start.sh \
    && echo 'php artisan db:seed --force' >> /usr/local/bin/start.sh \
    && echo 'echo "🔧 Starting services..."' >> /usr/local/bin/start.sh \
    && echo 'exec supervisord -c /etc/supervisor/conf.d/supervisord.conf' >> /usr/local/bin/start.sh \
    && chmod +x /usr/local/bin/start.sh

# Health check for container monitoring
HEALTHCHECK --interval=30s --timeout=10s --start-period=60s --retries=3 \
    CMD curl -f http://localhost:80/ || exit 1

# Environment variables
ENV APP_ENV=production
ENV APP_DEBUG=false
ENV LOG_CHANNEL=stderr

EXPOSE 80

CMD ["/usr/local/bin/start.sh"]