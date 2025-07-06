FROM php:8.4.10-fpm-alpine

RUN docker-php-ext-install pdo pdo_mysql

ENV COMPOSER_ALLOW_SUPERUSER=1
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
COPY ./app/composer.* ./


RUN composer install --prefer-dist --no-scripts --no-progress --no-interaction \
    && composer clear-cache
COPY ./app .
RUN composer dump-autoload --optimize

RUN apk add --no-cache --virtual .build-deps $PHPIZE_DEPS linux-headers \
    && pecl install xdebug \
    && docker-php-ext-enable xdebug \
    && apk del .build-deps

COPY php/xdebug/xdebug.ini "$PHP_INI_DIR/conf.d"

