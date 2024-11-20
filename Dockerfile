FROM php:8.3-apache
RUN 

RUN RUN docker-php-source extract \
	apt-get update && apt-get install -y \
		libfreetype-dev \
		libjpeg62-turbo-dev \
		libpng-dev \
	&& docker-php-ext-configure gd --with-freetype --with-jpeg \
	&& docker-php-ext-install -j$(nproc) gd \
	docker-php-ext-install intl mbstring xml xmlrpc zip iconv \
	&& docker-php-source delete

ENV APACHE_DOCUMENT_ROOT /var/www/html/public
ENV CI_ENVIRONMENT = development
ENV database.default.DBDriver = SQLite3
ENV database.default.database = '/var/www/html/writable/db.sqlite'
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf
RUN a2enmod rewrite



