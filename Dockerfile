FROM dunglas/frankenphp:php8.4-bookworm

# Install system dependencies and PHP extensions
RUN apt-get update && apt-get install -y \
    git curl unzip zip libpng-dev libxml2-dev libzip-dev libonig-dev libgd-dev \
    && docker-php-ext-install \
        pdo pdo_mysql mbstring xml curl zip gd bcmath intl tokenizer fileinfo opcache \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /app

# Copy composer files first for caching
COPY composer.json composer.lock ./

# Install PHP dependencies
RUN composer install --no-dev --optimize-autoloader --no-scripts --no-interaction

# Copy the rest of the application
COPY . .

# Run post-install scripts
RUN composer dump-autoload --optimize

# Set permissions
RUN mkdir -p storage/logs storage/framework/cache storage/framework/sessions storage/framework/views bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache

# Cache Laravel config for production
RUN php artisan storage:link || true

EXPOSE 8000

CMD php artisan migrate --force && php artisan config:cache && php artisan route:cache && php artisan view:cache && php artisan serve --host=0.0.0.0 --port=${PORT:-8000}
