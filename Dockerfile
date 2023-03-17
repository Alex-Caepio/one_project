FROM php:7.4.27-apache

ENV SUPERCRONIC_URL=https://github.com/aptible/supercronic/releases/download/v0.1.12/supercronic-linux-386 \
    SUPERCRONIC=supercronic-linux-386 \
    SUPERCRONIC_SHA1SUM=9c40fcff02fa4e153d2f55826e1fa362cd0e448e

COPY ./config/vhost.conf /etc/apache2/sites-enabled/000-default.conf

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer \
&& apt-get update && apt-get install -y git libzip-dev unzip libpng-dev mysql-common default-mysql-client\
&& docker-php-ext-install zip pdo_mysql gd && a2enmod rewrite headers

RUN curl -fsSLO "$SUPERCRONIC_URL" && echo "${SUPERCRONIC_SHA1SUM} ${SUPERCRONIC}" | sha1sum -c - && chmod +x "$SUPERCRONIC"\
&& mv "$SUPERCRONIC" "/usr/local/bin/${SUPERCRONIC}" && ln -s "/usr/local/bin/${SUPERCRONIC}" /usr/local/bin/supercronic

WORKDIR /var/www/html
COPY . /var/www/html
RUN chmod 644 /var/www/html/.env && composer install && php artisan migrate && chmod -R 777 /var/www/html/storage/*
