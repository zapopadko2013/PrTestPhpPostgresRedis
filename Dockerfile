FROM php:8.2-apache

# Устанавливаем необходимые зависимости
RUN apt-get update && apt-get install -y \
    libpq-dev \
    && rm -rf /var/lib/apt/lists/* \
    && docker-php-ext-install pdo pdo_pgsql

# Копируем пользовательский файл конфигурации Apache
COPY apache2.conf /etc/apache2/apache2.conf

# Копируем файлы проекта
COPY . /var/www/html/

EXPOSE 80

CMD ["apache2-foreground"]