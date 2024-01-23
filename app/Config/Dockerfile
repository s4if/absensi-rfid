FROM php:8.2-apache

COPY --from=composer /usr/bin/composer /usr/bin/composer

RUN apt-get update -y && apt-get install -y libonig-dev \
	 libzip-dev libpng-dev libjpeg-dev libwebp-dev \
	 libicu-dev

RUN docker-php-source extract 

RUN docker-php-ext-install mbstring

RUN docker-php-ext-install zip

RUN docker-php-ext-install gd

RUN docker-php-ext-install intl

COPY . /var/www/html/

ENV APACHE_DOCUMENT_ROOT /var/www/html/public/

RUN COMPOSER_ALLOW_SUPERUSER=1 composer install --no-dev --optimize-autoloader