FROM php:7.1-apache
MAINTAINER Bruno Perel

COPY composer.json /var/www/html/dm-server/composer.json

RUN a2enmod rewrite

RUN apt-get update && apt-get install -y git wget unzip mariadb-client nano ssmtp

RUN echo 'sendmail_path = "/usr/sbin/ssmtp -t"' > /usr/local/etc/php/conf.d/mail.ini

RUN yes '' | pecl install channel://pecl.php.net/apcu_bc-1.0.4
RUN echo 'extension=apcu.so' > /usr/local/etc/php/conf.d/apcu.ini

RUN docker-php-ext-install pdo pdo_mysql exif opcache

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

RUN cd /var/www/html/dm-server && \
  composer install --no-plugins --no-scripts
