FROM php:7.2-fpm-alpine

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
      redis \
      xdebug && \
    docker-php-ext-enable \
      apcu \
      memcached \
      redis \
      xdebug

RUN apk del \
      build-dependencies

COPY php-fpm/xdebug.ini \
     php-fpm/php.ini \
     /usr/local/etc/php/conf.d/

WORKDIR /var/www

EXPOSE 9000
