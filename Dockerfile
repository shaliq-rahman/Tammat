FROM php:8.4-cli-bookworm

# Install install-php-extensions helper
ADD https://github.com/mlocati/docker-php-extension-installer/releases/latest/download/install-php-extensions /usr/local/bin/
RUN chmod +x /usr/local/bin/install-php-extensions

# Install PHP extensions
RUN install-php-extensions \
    pdo pdo_mysql mbstring xml curl zip gd bcmath intl tokenizer fileinfo opcache

# Install system tools
RUN apt-get update && apt-get install -y git unzip && apt-get clean && rm -rf /var/lib/apt/lists/*

# Set PHP memory limit
RUN echo "memory_limit=-1" > /usr/local/etc/php/conf.d/memory.ini

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

ENV COMPOSER_ALLOW_SUPERUSER=1

WORKDIR /app

# Copy everything needed for composer install
COPY composer.json composer.lock ./
COPY packages/ ./packages/

# Install PHP dependencies
RUN composer install --no-dev --optimize-autoloader --no-scripts --no-interaction --ignore-platform-reqs

# Copy rest of application
COPY . .

RUN composer dump-autoload --optimize --no-scripts

# Set permissions
RUN mkdir -p storage/logs storage/framework/cache storage/framework/sessions storage/framework/views bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache

EXPOSE 8000

CMD php artisan migrate --force && php artisan config:cache && php artisan route:cache && php artisan view:cache && php artisan serve --host=0.0.0.0 --port=${PORT:-8000}
