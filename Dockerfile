FROM docker.io/php:8.3-apache

# Copy and install PHP extensions installer and Composer
COPY --from=docker.io/mlocati/php-extension-installer /usr/bin/install-php-extensions /usr/local/bin/
COPY --from=docker.io/composer /usr/bin/composer /usr/bin/composer

RUN apt-get update && apt-get install -y \
		libfreetype-dev \
		libjpeg62-turbo-dev \
		libpng-dev \
	&& install-php-extensions intl mbstring xml xmlrpc zip iconv gd \
	&& rm -rf /var/lib/apt/lists/*

ENV APACHE_DOCUMENT_ROOT /var/www/html/public
ENV CI_ENVIRONMENT = development
ENV database.default.DBDriver = SQLite3
ENV database.default.database = '/var/www/html/writable/db.sqlite'

# Install Composer dependencies without development packages
COPY composer.* ./
RUN composer install --no-dev --no-autoloader --no-scripts --no-cache

# Copy application source and optimize Composer autoload
COPY . ./
RUN composer dump-autoload -o --no-dev --classmap-authoritative

RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf
RUN a2enmod rewrite

EXPOSE 80


