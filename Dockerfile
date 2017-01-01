FROM php:7.0-apache
MAINTAINER Bruno Perel

ADD .htaccess /var/www/html/dm-server/.htaccess
ADD app /var/www/html/dm-server/app
ADD index.php /var/www/html/dm-server/index.php
ADD composer.json /var/www/html/dm-server/composer.json

RUN a2enmod rewrite

RUN apt-get update && apt-get install -y git wget unzip mariadb-client

RUN docker-php-ext-install pdo pdo_mysql opcache

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

RUN cd /var/www/html/dm-server && composer install --no-plugins --no-scripts