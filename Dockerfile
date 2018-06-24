FROM php:7.1-fpm
MAINTAINER Bruno Perel

RUN apt-get update && apt-get install -y git wget unzip mariadb-client nano ssmtp

RUN echo 'sendmail_path = "/usr/sbin/ssmtp -t"' > /usr/local/etc/php/conf.d/mail.ini

RUN yes '' | pecl install channel://pecl.php.net/apcu_bc-1.0.4
RUN echo 'extension=apcu.so' > /usr/local/etc/php/conf.d/apcu.ini

RUN docker-php-ext-install -j$(nproc) pdo pdo_mysql exif opcache

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

COPY composer.json /var/www/html/dm-server/composer.json

RUN cd /var/www/html/dm-server && \
  composer install --no-plugins --no-scripts && \
  touch development.log && chown www-data:www-data development.log && \
  touch pimple.json && chown www-data:www-data pimple.json

WORKDIR /var/www/html/dm-server
