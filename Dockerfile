FROM php:8.4-fpm-bullseye

RUN apt-get update -y \
    && apt-get install -y --no-install-recommends \
        nginx \
        supervisor \
        git \
        curl \
        unzip \
        libzip-dev \
        libicu-dev \
        libonig-dev \
        libxml2-dev \
        pkg-config \
        libssl-dev \
        libzstd-dev \
        autoconf \
        g++ \
        make \
    && docker-php-ext-install pdo pdo_mysql zip intl mbstring xml opcache \
    && pecl install mongodb-1.15.3 \
    && docker-php-ext-enable mongodb \
    && rm -rf /var/lib/apt/lists/* /tmp/pear

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

COPY docker/php/php-fpm.conf /usr/local/etc/php-fpm.d/www.conf
COPY docker/php/php.ini     /usr/local/etc/php/conf.d/app.ini
COPY docker/php/opcache.ini /usr/local/etc/php/conf.d/opcache.ini
COPY docker/nginx/nginx.conf   /etc/nginx/nginx.conf
COPY docker/nginx/default.conf /etc/nginx/http.d/default.conf
COPY docker/supervisor/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

WORKDIR /var/www/html

# Copier TOUT le code source d'abord
COPY . .

# Puis installer les dépendances avec les scripts (bin/console est disponible)
ENV APP_ENV=prod
RUN COMPOSER_MEMORY_LIMIT=-1 composer install \
    --no-dev \
    --optimize-autoloader \
    --no-interaction \
    --prefer-dist \
    --ignore-platform-reqs \
    --no-scripts

RUN mkdir -p var/cache var/log \
    && chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html/public \
    && chmod +x docker/entrypoint.sh

EXPOSE 8080

ENTRYPOINT ["docker/entrypoint.sh"]
