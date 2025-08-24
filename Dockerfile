# Laravel için optimize edilmiş Docker image
FROM php:8.2-fpm

# Sistem paketlerini güncelle ve gerekli paketleri kur
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    zip \
    unzip \
    nginx \
    supervisor \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip \
    && pecl install redis \
    && docker-php-ext-enable redis \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Composer'ı kur
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Çalışma dizinini ayarla
WORKDIR /var/www

# Nginx konfigürasyonu
COPY docker/nginx/nginx.conf /etc/nginx/sites-available/default

# PHP konfigürasyonu
COPY docker/php/php.ini /usr/local/etc/php/conf.d/laravel.ini

# Supervisor konfigürasyonu
COPY docker/supervisor/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# Laravel dosyalarını kopyala
COPY . /var/www

# Permissions ayarla
RUN chown -R www-data:www-data /var/www \
    && chmod -R 755 /var/www/storage \
    && chmod -R 755 /var/www/bootstrap/cache

# Composer bağımlılıklarını yükle
RUN composer install --optimize-autoloader --no-interaction

# Port açmağı belirt
EXPOSE 80

# Supervisor ile nginx ve php-fpm'i başlat
CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/conf.d/supervisord.conf"]