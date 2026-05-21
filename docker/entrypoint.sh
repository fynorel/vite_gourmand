#!/bin/sh
set -e

echo "==> Vite & Gourmand — Démarrage"

echo "==> Vidage du cache..."
php bin/console cache:clear --env=prod --no-warmup 2>/dev/null || true
php bin/console cache:warmup --env=prod 2>/dev/null || true

echo "==> Demarrage Nginx + PHP-FPM..."
exec supervisord -c /etc/supervisor/conf.d/supervisord.conf
