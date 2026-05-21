#!/bin/sh
set -e

echo "==> Vite & Gourmand — Démarrage"

echo "==> Dump autoload..."
php -d extension_dir=/usr/local/lib/php/extensions/no-debug-non-zts-20230831 \
    /usr/bin/composer dump-autoload --optimize --no-dev 2>/dev/null || \
composer dump-autoload --optimize --no-dev

echo "==> Vidage du cache..."
php -d error_reporting=E_ALL\&~E_WARNING bin/console cache:clear --env=prod --no-warmup
php -d error_reporting=E_ALL\&~E_WARNING bin/console cache:warmup --env=prod

echo "==> Demarrage Nginx + PHP-FPM..."
exec supervisord -c /etc/supervisor/conf.d/supervisord.conf
