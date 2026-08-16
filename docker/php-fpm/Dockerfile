FROM php:8.4-fpm-alpine

ARG USER_ID=1000
ARG USER_NAME=www-data
ARG GROUP_ID=1000
ARG GROUP_NAME=www-data

RUN apk update && apk add --no-cache \
    git curl libpng-dev libwebp-dev libjpeg-turbo-dev freetype-dev \
    libzip-dev postgresql-dev icu-dev oniguruma-dev \
    linux-headers autoconf make g++ bash

RUN docker-php-ext-configure gd --with-freetype --with-jpeg --with-webp && \
    docker-php-ext-install pdo_pgsql zip exif pcntl gd intl mbstring bcmath

RUN pecl install redis && docker-php-ext-enable redis

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

RUN addgroup -g ${GROUP_ID} ${GROUP_NAME} && \
    adduser -u ${USER_ID} -G ${GROUP_NAME} -D ${USER_NAME} && \
    chown -R ${USER_NAME}:${GROUP_NAME} /var/www

RUN git config --global --add safe.directory /var/www

WORKDIR /var/www

USER ${USER_NAME}

CMD ["php-fpm"]
