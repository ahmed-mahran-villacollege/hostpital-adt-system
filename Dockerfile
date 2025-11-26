# syntax=docker/dockerfile:1

## Composer dependencies
FROM composer:2 AS vendor
WORKDIR /app
# Install intl extension (composer image is Alpine-based, so use apk)
RUN set -eux; \
    if command -v apk > /dev/null; then \
        apk add --no-cache icu-dev icu-data-full icu-libs && \
        docker-php-ext-configure intl && \
        docker-php-ext-install intl; \
    else \
        apt-get update && \
        apt-get install -y libicu-dev && \
        docker-php-ext-install intl && \
        rm -rf /var/lib/apt/lists/*; \
    fi
COPY composer.json composer.lock ./
RUN composer install --no-dev --no-interaction --prefer-dist --optimize-autoloader --no-scripts

## Application runtime
FROM php:8.4-apache AS runtime
WORKDIR /var/www/html

ENV APACHE_DOCUMENT_ROOT=/var/www/html/public
ENV COMPOSER_ALLOW_SUPERUSER=1

RUN apt-get update && \
    apt-get install -y git unzip libzip-dev libpng-dev libjpeg62-turbo-dev libfreetype6-dev libonig-dev libxml2-dev libicu-dev && \
    docker-php-ext-configure gd --with-freetype --with-jpeg && \
    docker-php-ext-install pdo_mysql bcmath gd zip intl && \
    a2enmod rewrite && \
    sed -ri 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/000-default.conf /etc/apache2/sites-available/default-ssl.conf && \
    rm -rf /var/lib/apt/lists/*

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

COPY . .
COPY --from=vendor /app/vendor ./vendor

RUN chown -R www-data:www-data storage bootstrap/cache

EXPOSE 80
CMD ["apache2-foreground"]
