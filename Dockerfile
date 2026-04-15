FROM php:8.3-cli

RUN apt-get update && apt-get install -y \
    git curl unzip zip libzip-dev \
    libpng-dev libonig-dev libxml2-dev libicu-dev \
    nodejs npm \
    && docker-php-ext-install pdo pdo_mysql zip intl

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

COPY . .

RUN composer install --optimize-autoloader --no-interaction --no-dev
RUN npm ci
RUN npm run build

RUN mkdir -p storage/framework/cache \
    storage/framework/sessions \
    storage/framework/views \
    storage/logs \
    bootstrap/cache

RUN chmod -R 775 storage bootstrap/cache
RUN chmod +x start.sh

EXPOSE 8080

CMD ["sh", "start.sh"]