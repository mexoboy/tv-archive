FROM php:7.1-fpm-alpine

# Install dependency packages for phalcon
RUN apk update \
 && apk --no-cache add ffmpeg \
 && apk --no-cache --virtual .tmp-dependency add \
    git \
    autoconf \
    g++ \
    make \
    pcre-dev

# Checkout and build phalcon
RUN git clone https://github.com/phalcon/cphalcon.git /tmp/cphalcon \
 && cd /tmp/cphalcon/build \
 && /bin/sh install \
 && echo "extension=phalcon.so" > /usr/local/etc/php/conf.d/phalcon.ini \
 && rm -rf /tmp/cphalcon

# Clean phalcon temporary dependencies
RUN apk del .tmp-dependency

RUN docker-php-ext-install pdo \
 && docker-php-ext-install pdo_mysql

# Install composer
RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');" \
 && php composer-setup.php --filename=composer --install-dir=\/usr\/local\/bin \
 && php -r "unlink('composer-setup.php');"

VOLUME /var/www/tv-archive
WORKDIR /var/www/tv-archive
