#!/bin/sh

# Cache configuration, routes, and views for optimal production performance
php artisan optimize

# Create the symbolic link from public/storage to storage/app/public
php artisan storage:link --force

# Run database migrations automatically on every successful deploy
php artisan migrate:fresh --force

# Start Supervisor to run both Apache and Laravel Reverb together
exec supervisord -c /var/www/html/supervisor.conf