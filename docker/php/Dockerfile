FROM php:fpm-alpine

RUN docker-php-ext-install pdo pdo_mysql \
    && docker-php-ext-enable pdo_mysql

RUN mkdir /app/ \
    && mkdir /app/storage \
    && chmod a+rw /app/storage