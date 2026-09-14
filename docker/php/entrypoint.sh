#!/bin/sh
set -e

# Bootstrap a .env from the example file on first boot, then generate an
# application key when none was provided. Runtime configuration is expected
# to arrive through environment variables.
if [ ! -f /var/www/html/.env ]; then
    cp /var/www/html/.env.example /var/www/html/.env
fi

if [ -z "$APP_KEY" ]; then
    php artisan key:generate --force --no-interaction
fi

# Wait for the database to accept connections before migrating.
until php artisan db:monitor --no-interaction > /dev/null 2>&1; do
    echo "Waiting for the database..."
    sleep 2
done

php artisan migrate --force --no-interaction
php artisan db:seed --force --no-interaction

exec "$@"
