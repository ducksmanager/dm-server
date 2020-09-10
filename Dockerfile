FROM php:7.4-fpm
MAINTAINER Bruno Perel

RUN apt-get update && apt-get install -y git wget unzip mariadb-client nano msmtp libicu-dev

RUN echo 'sendmail_path = "/usr/sbin/msmtp -t"' > /usr/local/etc/php/conf.d/mail.ini

RUN pecl install apcu && \
    echo "extension=apcu.so" > /usr/local/etc/php/conf.d/apcu.ini

RUN docker-php-ext-install -j$(nproc) pdo_mysql exif opcache intl

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
