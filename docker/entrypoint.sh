#!/bin/sh
set -e

echo "==> Vite & Gourmand — Démarrage"

echo "==> Dump autoload..."
composer dump-autoload --optimize --no-dev

echo "==> Vidage du cache..."
php bin/console cache:clear --env=prod --no-warmup
php bin/console cache:warmup --env=prod

echo "==> Demarrage Nginx + PHP-FPM..."
exec supervisord -c /etc/supervisor/conf.d/supervisord.conf
