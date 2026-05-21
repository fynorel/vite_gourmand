#!/bin/sh
set -e

echo "==> Vite & Gourmand — Démarrage"

echo "==> Vidage du cache..."
php bin/console cache:clear --env=prod --no-warmup
php bin/console cache:warmup --env=prod

echo "==> Verification BDD..."
php bin/console doctrine:query:sql "SELECT 1" > /dev/null 2>&1 && echo "BDD OK" || echo "BDD non disponible"

echo "==> Demarrage Nginx + PHP-FPM..."
exec supervisord -c /etc/supervisor/conf.d/supervisord.conf
