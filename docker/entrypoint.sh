#!/bin/bash
set -euo pipefail

cd /var/www/html

# Ensure SQLite file exists and is writable when using sqlite connection
if [[ "${DB_CONNECTION:-sqlite}" == "sqlite" ]]; then
    DB_PATH="${DB_DATABASE:-/var/www/html/database/database.sqlite}"
    mkdir -p "$(dirname "$DB_PATH")"
    [[ -f "$DB_PATH" ]] || touch "$DB_PATH"
    chown www-data:www-data "$DB_PATH"
    chmod 664 "$DB_PATH"
fi

# Create .env from example if missing
if [[ ! -f .env ]]; then
    cp .env.example .env
fi

# Generate app key if not provided
if [[ -z "${APP_KEY:-}" ]]; then
    php artisan key:generate --force --no-interaction
fi

# Run migrations and seeders
php artisan migrate:fresh --force --seed --no-interaction

exec "$@"
