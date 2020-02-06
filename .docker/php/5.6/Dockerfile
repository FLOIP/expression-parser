FROM php:5.6-alpine

COPY --from=composer /usr/bin/composer /usr/bin/composer

RUN docker-php-ext-install bcmath
