FROM php:8.4.8-fpm

RUN apt-get update && \
    apt-get install -y --no-install-recommends \
      libpng-dev \
      libjpeg-dev \
      libfreetype6-dev \
    && docker-php-ext-configure gd \
         --with-freetype=/usr/include/ \
         --with-jpeg=/usr/include/ \
    && docker-php-ext-install pdo_mysql pcntl exif gd \
    && curl -sS https://getcomposer.org/installer | \
       php -- --install-dir=/usr/local/bin --filename=composer \
    && rm -rf /var/lib/apt/lists/* \\

RUN pecl install xdebug \
    && docker-php-ext-enable xdebug

COPY ./xdebug/xdebug.ini "${PHP_INI_DIR}/conf.d"

WORKDIR /var/www