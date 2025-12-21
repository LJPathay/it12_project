# Use PHP 8.3 with Nginx from ServerSideUp
FROM serversideup/php:8.3-fpm-nginx

# Switch to root to install dependencies and set permissions
USER root

# Set working directory
WORKDIR /var/www/html

# Copy application files (with correct ownership)
COPY --chown=www-data:www-data . .

# Image config
ENV AUTORUN_ENABLED=true
ENV AUTORUN_LARAVEL_MIGRATION=true
ENV AUTORUN_LARAVEL_CONFIG_CACHE=true
ENV AUTORUN_LARAVEL_ROUTE_CACHE=true
ENV AUTORUN_LARAVEL_VIEW_CACHE=true

# Disable internal database wait (Render handles health checks)
ENV AUTORUN_LARAVEL_MIGRATION_SKIP_DB_CHECK=true

# Laravel config
ENV APP_ENV=production
ENV APP_DEBUG=true
ENV LOG_CHANNEL=stderr

# Install dependencies during build
RUN install-php-extensions gd bcmath zip intl

RUN composer install --no-dev --no-interaction --prefer-dist --optimize-autoloader

# Ensure scripts and storage are correctly set
RUN if [ -d "/var/www/html/scripts" ]; then chmod -R +x /var/www/html/scripts; fi \
    && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache \
    && chown -R www-data:www-data /var/www/html

# Note: Do not use USER www-data here. The serversideup image starts as root 
# to run the S6 overlay init system and then drops privileges automatically.
