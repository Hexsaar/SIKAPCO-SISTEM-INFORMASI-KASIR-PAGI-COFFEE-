FROM php:8.3-fpm

# Install dependencies sistem & ekstensi PHP
RUN apt-get update && apt-get install -y \
    nginx \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libzip-dev \
    zip \
    unzip \
    git \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd pdo pdo_mysql zip

# Copy konfigurasi Nginx custom
COPY nginx.conf /etc/nginx/sites-available/default

# Mengizinkan Composer berjalan sebagai superuser
ENV COMPOSER_ALLOW_SUPERUSER=1

# Copy project files
COPY . /var/www/html

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
RUN composer install --no-interaction --optimize-autoloader --no-dev

# Set permissions
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

EXPOSE 80

# PASTIKAN BARIS INI MENGGUNAKAN php-fpm -D
CMD php-fpm -D && nginx -g "daemon off;"