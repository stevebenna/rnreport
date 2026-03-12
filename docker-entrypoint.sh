#!/bin/sh
set -e

# Ensure writable dir permissions for CodeIgniter
if [ "$(id -u)" = '0' ]; then
  chown -R www-data:www-data /var/www/html/writable || true
  chmod -R 0775 /var/www/html/writable || true
fi

# Install dependencies if missing (useful when mounting source as volume)
if [ -f composer.json ]; then
  if [ ! -d vendor ] || [ ! -f vendor/autoload.php ]; then
    echo "Installing PHP dependencies..."
    composer install --no-interaction --optimize-autoloader
  fi
fi

exec "$@"
