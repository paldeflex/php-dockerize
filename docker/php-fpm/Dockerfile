FROM composer:2 AS composer

FROM php:8.4.8-fpm

COPY --from=composer /usr/bin/composer /usr/local/bin/composer

RUN apt-get update && \
    apt-get install -y --no-install-recommends \
        libpng-dev \
        libjpeg-dev \
        libfreetype6-dev \
    && docker-php-ext-configure gd \
         --with-freetype=/usr/include/ \
         --with-jpeg=/usr/include/ \
    && docker-php-ext-install pdo_mysql pcntl exif gd \
    && rm -rf /var/lib/apt/lists/*

RUN pecl install xdebug \
    && docker-php-ext-enable xdebug

COPY ./xdebug/xdebug.ini "$PHP_INI_DIR/conf.d"

WORKDIR /var/www