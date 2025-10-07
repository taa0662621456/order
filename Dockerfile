# syntax=docker/dockerfile:1
FROM php:8.3-cli

RUN apt-get update && apt-get install -y     git unzip libzip-dev librabbitmq-dev libicu-dev libonig-dev libsqlite3-dev     && docker-php-ext-install intl     && pecl install amqp     && docker-php-ext-enable amqp     && rm -rf /var/lib/apt/lists/*

# Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /app
COPY . /app

RUN composer install --no-interaction --no-progress --prefer-dist || true

CMD ["php", "-v"]
