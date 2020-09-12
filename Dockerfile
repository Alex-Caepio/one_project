FROM php:7.3-alpine
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer \
&& apt-get update && apt-get install -y git libzip-dev unzip \
&& docker-php-ext-install zip pdo_mysql && a2enmod rewrite headers

WORKDIR /var/www/html
COPY . /var/www/html
RUN chmod 644 /var/www/znay_nashyh/.env.local && composer install