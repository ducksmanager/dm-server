FROM php:7.0-apache
MAINTAINER Bruno Perel

COPY .htaccess /var/www/html/dm-server/.htaccess
COPY docker-compose.yml /var/www/html/dm-server/docker-compose.yml
COPY app /var/www/html/dm-server/app
COPY assets /var/www/html/dm-server/assets
COPY scripts /var/www/html/dm-server/scripts
COPY index.php /var/www/html/dm-server/index.php
COPY composer.json /var/www/html/dm-server/composer.json

RUN a2enmod rewrite

RUN apt-get update && apt-get install -y git wget unzip mariadb-client

RUN pecl install xdebug-2.5.0

RUN docker-php-ext-install pdo pdo_mysql opcache && docker-php-ext-enable xdebug

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

RUN cd /var/www/html/dm-server && \
  composer install --no-plugins --no-scripts && \
  touch development.log && chown www-data:www-data development.log && \
  touch pimple.json && chown www-data:www-data pimple.json && \
  chmod +x scripts/*