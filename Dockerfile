FROM php:8.2-apache

RUN apt-get update && apt-get install -y git zip unzip
RUN docker-php-ext-install pdo pdo_mysql
RUN a2enmod rewrite

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer