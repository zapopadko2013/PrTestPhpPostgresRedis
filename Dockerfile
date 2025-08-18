FROM php:8.2-fpm

# Устанавливаем зависимости Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
COPY . /var/www/html
WORKDIR /var/www/html
RUN composer install --no-dev

# Открываем порт для PHP-FPM
EXPOSE 9000

# Указываем команду запуска
CMD ["php-fpm"]