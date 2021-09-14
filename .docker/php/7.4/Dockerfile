FROM php:7.4-alpine

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

RUN docker-php-ext-install bcmath
