FROM php:8.3-cli-alpine
RUN apk add --no-cache git unzip libzip-dev
RUN docker-php-ext-install pdo pdo_mysql zip
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
WORKDIR /var/www/html
COPY . .
RUN composer install --no-dev --optimize-autoloader
CMD php artisan migrate --force && php artisan db:seed --force && php artisan serve --host 0.0.0.0 --port 80
