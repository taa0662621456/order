FROM php:8.3-cli
RUN apt-get update && apt-get install -y git unzip libicu-dev libzip-dev && docker-php-ext-install intl
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
WORKDIR /app
COPY . .
RUN composer install --no-interaction --prefer-dist || true
CMD ["php", "-v"]
