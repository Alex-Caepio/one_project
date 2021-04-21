FROM php:7.4-apache
COPY ./config/vhost.conf /etc/apache2/sites-enabled/000-default.conf
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer \
&& apt-get update && apt-get install -y git libzip-dev unzip libpng-dev mysql-common default-mysql-client\
&& docker-php-ext-install zip pdo_mysql gd && a2enmod rewrite headers

WORKDIR /var/www/html
COPY . /var/www/html
RUN chmod 644 /var/www/html/.env && composer install && chmod -R 777 /var/www/html/storage/*
