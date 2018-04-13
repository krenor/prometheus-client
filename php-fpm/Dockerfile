FROM php:7.1-fpm-alpine

RUN apk upgrade --update && \
    apk add --no-cache --virtual build-dependencies ${PHPIZE_DEPS}

RUN pecl install \
      xdebug && \
    docker-php-ext-enable \
      xdebug

RUN apk del \
      build-dependencies

COPY php-fpm/xdebug.ini \
     /usr/local/etc/php/conf.d/

WORKDIR /var/www

EXPOSE 9000
