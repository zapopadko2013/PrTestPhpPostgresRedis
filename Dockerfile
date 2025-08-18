# Используем официальный образ PHP
FROM php:8.2-fpm-alpine

# Устанавливаем необходимые зависимости и расширение pdo_pgsql
RUN apk add --no-cache postgresql-dev \
    && docker-php-ext-install pdo pdo_pgsql

# Остальная часть вашего Dockerfile
WORKDIR /var/www/html
COPY . .

CMD ["php-fpm"]