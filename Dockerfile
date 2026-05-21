FROM webdevops/php-nginx:8.3

ENV WEB_DOCUMENT_ROOT=/var/www/html/public
ENV APP_ENV=prod

RUN apt-get update && apt-get install -y \
    supervisor \
    && pecl install mongodb \
    && docker-php-ext-enable mongodb \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

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
    && chown -R application:application /var/www/html \
    && chmod -R 755 /var/www/html/public

EXPOSE 80
