FROM php:7.1-fpm-alpine

RUN apk upgrade --update && \
    apk add --no-cache --virtual build-dependencies ${PHPIZE_DEPS}

RUN pecl install \
      apcu \
      xdebug && \
    docker-php-ext-enable \
      apcu \
      xdebug && \
    echo "apc.enable_cli=1" >> /usr/local/etc/php/conf.d/apc.ini

RUN apk del \
      build-dependencies

COPY php-fpm/xdebug.ini \
     /usr/local/etc/php/conf.d/

WORKDIR /var/www

EXPOSE 9000
