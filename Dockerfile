FROM php:8.3-fpm-alpine

RUN apk update && apk add --no-cache \
    nginx \
    supervisor \
    git \
    curl \
    unzip \
    libzip-dev \
    icu-dev \
    icu-libs \
    oniguruma-dev \
    libxml2-dev \
    openssl-dev \
    autoconf \
    g++ \
    make \
    pkgconfig \
    ca-certificates \
    && docker-php-ext-install \
        pdo \
        pdo_mysql \
        zip \
        intl \
        mbstring \
        xml \
        opcache \
    && pecl install mongodb-1.20.0 \
    && docker-php-ext-enable mongodb \
    && apk del autoconf g++ make pkgconfig \
    && rm -rf /tmp/pear

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

COPY docker/php/php.ini     /usr/local/etc/php/conf.d/app.ini
COPY docker/php/opcache.ini /usr/local/etc/php/conf.d/opcache.ini
COPY docker/nginx/nginx.conf   /etc/nginx/nginx.conf
COPY docker/nginx/default.conf /etc/nginx/http.d/default.conf
COPY docker/supervisor/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

WORKDIR /var/www/html

COPY composer.json composer.lock ./

RUN php -r "echo PHP_VERSION;" \
    && php -m \
    && COMPOSER_MEMORY_LIMIT=-1 composer install \
        --no-dev \
        --optimize-autoloader \
        --no-interaction \
        --prefer-dist \
        --no-scripts \
        -v

COPY . .

RUN mkdir -p var/cache var/log \
    && chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html/public \
    && chmod +x docker/entrypoint.sh

EXPOSE 8080

ENTRYPOINT ["docker/entrypoint.sh"]
