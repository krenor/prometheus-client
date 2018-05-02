FROM php:7.1-fpm-alpine

ENV MEMCACHED_DEPS \
  cyrus-sasl-dev \
  libmemcached-dev \
  zlib-dev

RUN apk upgrade --update && \
    apk add --no-cache --virtual build-dependencies \
      ${PHPIZE_DEPS} \
      ${MEMCACHED_DEPS} && \
    apk add --no-cache \
      libmemcached

RUN pecl install \
      apcu \
      memcached \
      xdebug && \
    docker-php-ext-enable \
      apcu \
      memcached \
      xdebug && \
    echo "apc.enable_cli=1" >> /usr/local/etc/php/conf.d/apc.ini

RUN apk del \
      build-dependencies

COPY php-fpm/xdebug.ini \
     /usr/local/etc/php/conf.d/

WORKDIR /var/www

EXPOSE 9000
