FROM php:7.3-fpm

RUN apt-get update && apt-get install -y \
    git \
    wget \
    && pecl install xdebug \
    && docker-php-ext-enable xdebug

RUN apt-get install -y libpq-dev \
    && docker-php-ext-configure pgsql -with-pgsql=/usr/local/pgsql \
    && docker-php-ext-install pdo pdo_pgsql pgsql

ADD ./php.ini /usr/local/etc/php/php.ini

RUN wget https://getcomposer.org/installer -O - -q \
    | php -- --install-dir=/bin --filename=composer --quiet


WORKDIR /var/www

CMD ["php-fpm"]
