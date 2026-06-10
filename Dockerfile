FROM php:8.3-cli-alpine
RUN docker-php-ext-install pdo pdo_mysql
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
WORKDIR /var/www/html
COPY . .
RUN composer install --no-dev --optimize-autoloader
CMD php artisan migrate --force && php artisan db:seed --force && php artisan serve --host 0.0.0.0 --port 80
