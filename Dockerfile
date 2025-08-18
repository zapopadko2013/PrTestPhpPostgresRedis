FROM php:8.2-fpm

# Устанавливаем зависимости и расширение pdo_pgsql
RUN apt-get update && apt-get install -y \
    libpq-dev \
    && docker-php-ext-install pdo_pgsql

# Копируем код приложения
COPY . /var/www/html

# Устанавливаем права (если нужно)
RUN chown -R www-data:www-data /var/www/html