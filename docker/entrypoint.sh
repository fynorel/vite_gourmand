#!/bin/sh
set -e

echo "==> Vite & Gourmand — Démarrage"

echo "==> Nettoyage cache..."
rm -rf var/cache/prod

echo "==> Génération cache prod..."
APP_ENV=prod php bin/console cache:clear --no-warmup
APP_ENV=prod php bin/console cache:warmup

echo "==> Démarrage Nginx + PHP-FPM..."
exec supervisord -c /etc/supervisor/conf.d/supervisord.conf
