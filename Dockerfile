FROM php:8.2-fpm

# Устанавливаем необходимые зависимости и расширение pdo_pgsql
RUN apt-get update && apt-get install -y \
    libpq-dev \
    && rm -rf /var/lib/apt/lists/* \
    && docker-php-ext-install pdo pdo_pgsql

# Остальная часть вашего Dockerfile
WORKDIR /var/www/html
COPY . .
CMD ["php-fpm"]