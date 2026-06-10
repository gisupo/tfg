FROM php:8.3-fpm-alpine
RUN docker-php-ext-install pdo pdo_mysql
RUN curl -sS https://getcomposer.org | php -- --install-dir=/usr/local/bin --filename=composer
WORKDIR /var/www/html
COPY . .
RUN composer install --no-dev --optimize-autoloader
CMD php artisan migrate --force && php artisan db:seed --force && php artisan serve --host 0.0.0.0 --port 80
