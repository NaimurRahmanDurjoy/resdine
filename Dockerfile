# 1. Use Production-ready Apache image instead of CLI
FROM php:8.3-apache

WORKDIR /var/www/html

# 2. Install system dependencies, PHP extensions, and SUPERVISOR (Added supervisor here)
RUN apt-get update && apt-get install -y \
    unzip \
    git \
    libzip-dev \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    supervisor \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo pdo_mysql zip gd bcmath pcntl

# 3. Enable Apache Modules (Added proxy modules to route WebSocket traffic to Reverb)
RUN a2enmod rewrite proxy proxy_http proxy_wstunnel

# 4. Install Node.js and NPM for compiling Vue + Vite assets
RUN curl -sL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get install -y nodejs

# 5. Change Apache Document Root to Laravel's public directory
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# 6. Configure Apache to proxy WebSocket requests internally to Reverb (Port 8080)
RUN echo 'ProxyPass /app ws://127.0.0.1:8080/app\n\
ProxyPassReverse /app ws://127.0.0.1:8080/app' >> /etc/apache2/apache2.conf

# 7. Copy Composer from the official image
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# 8. Copy all project files into the container
COPY . .

# 9. Install PHP dependencies via Composer
RUN composer install --no-dev --optimize-autoloader

ARG VITE_REVERB_APP_KEY
ARG VITE_REVERB_HOST
ARG VITE_REVERB_PORT
ENV VITE_REVERB_APP_KEY=$VITE_REVERB_APP_KEY
ENV VITE_REVERB_HOST=$VITE_REVERB_HOST
ENV VITE_REVERB_PORT=$VITE_REVERB_PORT

# 10. Install Node dependencies and build the Vue/Inertia production bundle
RUN npm install && npm run build

# 11. Set correct permissions for Laravel storage and cache directories
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# 12. Bind Apache port to Render's dynamic PORT environment variable
RUN sed -i 's/Listen 80/Listen ${PORT}/g' /etc/apache2/ports.conf
RUN sed -i 's/<VirtualHost \*:80>/<VirtualHost \*:${PORT}>/g' /etc/apache2/sites-available/000-default.conf

EXPOSE ${PORT}

# 13. Run the startup script for handling dynamic runtime operations
CMD ["sh", "./deploy.sh"]