FROM php:8.3-cli
RUN apt-get update && apt-get install -y git unzip libsqlite3-dev
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
WORKDIR /app
COPY . /app
RUN composer install --no-interaction --prefer-dist
CMD ["php", "-S", "0.0.0.0:8000", "-t", "public"]
