FROM php:5.5-alpine

COPY --from=composer:1 /usr/bin/composer /usr/bin/composer

RUN docker-php-ext-install bcmath
