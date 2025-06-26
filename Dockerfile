FROM php:8.2-fpm-alpine

RUN apk add --no-cache --update \
    bash git curl zip unzip libzip-dev libpng-dev libjpeg-turbo-dev freetype-dev oniguruma-dev icu-dev libxml2-dev

RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd

RUN docker-php-ext-install pdo pdo_mysql zip intl

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

COPY composer.json composer.lock ./

COPY . .

RUN composer install --no-interaction --prefer-dist --optimize-autoloader

RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

EXPOSE 9000

CMD ["php-fpm"]
