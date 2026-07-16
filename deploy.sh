#!/bin/sh

# Cache configuration, routes, and views for optimal production performance
php artisan optimize

# Create the symbolic link from public/storage to storage/app/public
php artisan storage:link --force

# Run database migrations automatically on every successful deploy
php artisan migrate --force

# Start Apache web server in the foreground to keep the container running
apache2-foreground