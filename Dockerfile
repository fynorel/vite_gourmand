FROM php:8.4-fpm-bookworm

ADD https://github.com/mlocati/docker-php-extension-installer/releases/latest/download/install-php-extensions /usr/local/bin/

RUN chmod +x /usr/local/bin/install-php-extensions \
    && apt-get update -y \
    && apt-get install -y --no-install-recommends \
        nginx \
        supervisor \
        git \
        curl \
        unzip \
    && install-php-extensions \
        pdo_mysql \
        zip \
        intl \
        mbstring \
        xml \
        opcache \
        mongodb \
    && rm -rf /var/lib/apt/lists/*

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

COPY docker/php/php.ini     /usr/local/etc/php/conf.d/app.ini
COPY docker/php/opcache.ini /usr/local/etc/php/conf.d/opcache.ini
COPY docker/nginx/nginx.conf   /etc/nginx/nginx.conf
COPY docker/nginx/default.conf /etc/nginx/http.d/default.conf
COPY docker/supervisor/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

WORKDIR /var/www/html

COPY composer.json composer.lock ./

RUN COMPOSER_MEMORY_LIMIT=-1 composer install \
    --no-dev \
    --optimize-autoloader \
    --no-interaction \
    --prefer-dist \
    --no-scripts

COPY . .

RUN mkdir -p var/cache var/log \
    && chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html/public \
    && chmod +x docker/entrypoint.sh

EXPOSE 8080

ENTRYPOINT ["docker/entrypoint.sh"]
