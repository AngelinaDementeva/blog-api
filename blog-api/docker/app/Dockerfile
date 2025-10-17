FROM php:8.3-fpm

RUN apt-get update && apt-get install -y \
    git curl libonig-dev libzip-dev zip unzip libpng-dev \
    && docker-php-ext-install pdo pdo_mysql

# Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html
RUN chown -R www-data:www-data /var/www/html
