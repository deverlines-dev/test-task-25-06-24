FROM php:8.4-cli-alpine3.20

COPY --from=node:23-alpine3.20 /usr/local/bin /usr/local/bin
COPY --from=node:23-alpine3.20 /usr/local/lib/node_modules /usr/local/lib/node_modules

COPY --from=composer:2.8 /usr/bin/composer /usr/bin/composer

RUN mkdir "/install" && cd /install; \
    apk add linux-headers; \
    apk add \
      $PHPIZE_DEPS \
      tzdata \
      libstdc++  \
      libpq \
      curl-dev \
      brotli-dev \
      openssl-dev  \
      pcre2-dev  \
      zlib-dev

RUN \
    docker-php-ext-configure pcntl --enable-pcntl \
    && docker-php-ext-install pcntl \
    && docker-php-ext-enable pcntl; \
    \
    apk add icu-dev \
    && docker-php-ext-configure intl \
    && docker-php-ext-install intl \
    && docker-php-ext-enable intl; \
    \
    pecl install apcu \
    && docker-php-ext-enable apcu; \
    \
    apk add zip libzip-dev \
    && docker-php-ext-configure zip \
    && docker-php-ext-install zip \
    && docker-php-ext-enable zip; \
    \
    docker-php-ext-install pdo_mysql \
    && docker-php-ext-enable pdo_mysql; \
    \
    docker-php-ext-install bcmath; \
    docker-php-ext-install sockets;

RUN curl -L -o /tmp/swoole.tar.gz https://github.com/swoole/swoole-src/archive/refs/tags/v6.0.2.tar.gz \
    && tar --strip-components=1 -xf /tmp/swoole.tar.gz \
    && phpize \
    && ./configure \
    && make \
    && make install \
    && echo "extension=swoole.so" > /usr/local/etc/php/conf.d/swoole.ini; \
    docker-php-ext-enable swoole

RUN rm  -rf /tmp/* /var/cache/apk/* /install/* ;\
    \
    mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"; \
    \
    addgroup -g 1000 www-user \
      && adduser -D -G www-user -u 1000 www-user

RUN mkdir -p /run; \
    chown www-user:www-user /run && chmod +x /run; \
    \
    mkdir -p /var/log; \
    chown www-user:www-user /var/log && chmod +x /var/log

COPY ./docker/docker-php-octane-entrypoint.sh /run/docker-php-octane-entrypoint.sh

RUN chown www-user:www-user /run/docker-php-octane-entrypoint.sh && chmod +x /run/docker-php-octane-entrypoint.sh;

USER www-user:www-user

EXPOSE 8000

WORKDIR /var/www/laravel

CMD ["sleep", "infinity"]