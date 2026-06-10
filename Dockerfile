FROM php:8.3-apache
RUN apt-get update && apt-get install -y libzip-dev zip git && docker-php-ext-install pdo pdo_mysql zip
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
WORKDIR /var/www/html
COPY . .
RUN composer install --no-dev --optimize-autoloader
RUN sed -i 's|/var/www/html|/var/www/html/public|g' /etc/apache2/sites-available/000-default.conf
RUN a2enmod rewrite
CMD php artisan migrate --force && php artisan db:seed --force && apache2-foreground
