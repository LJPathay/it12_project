#!/usr/bin/env bash

echo "Running deployment script..."

# Install dependencies if not skipped
if [ -z "$SKIP_COMPOSER" ]; then
    echo "Installing composer dependencies..."
    composer install --no-dev --no-interaction --prefer-dist --optimize-autoloader
fi

# Run migrations
echo "Running migrations..."
# php artisan migrate --force

# Cache configuration
echo "Caching configuration..."
php artisan config:cache

# Cache routes
echo "Caching routes..."
php artisan route:cache

# Cache views
echo "Caching views..."
php artisan view:cache

echo "Deployment script finished."
