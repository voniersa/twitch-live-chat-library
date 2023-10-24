FROM php:8.2-cli

RUN apt-get update
RUN apt-get install -y libzip-dev
RUN docker-php-ext-install zip

RUN pecl install xdebug \
    && docker-php-ext-enable xdebug

COPY . /var/www/code/
WORKDIR /var/www/code/
