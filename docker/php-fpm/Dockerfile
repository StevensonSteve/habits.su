FROM php:8.4-fpm-alpine

RUN apk update && apk add --no-cache \
    git curl libpng-dev libwebp-dev libjpeg-turbo-dev freetype-dev \
    libzip-dev postgresql-dev icu-dev oniguruma-dev rabbitmq-c-dev \
    supervisor linux-headers autoconf make g++

RUN docker-php-ext-configure gd --with-freetype --with-jpeg --with-webp && \
    docker-php-ext-install pdo_mysql pdo_pgsql zip exif pcntl gd intl mbstring bcmath

RUN pecl install redis amqp && docker-php-ext-enable redis amqp

RUN apk add --no-cache bash nano vim

SHELL ["/bin/bash", "-c"]

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

RUN addgroup -g 1000 www-user && \
    adduser -u 1000 -G www-user -D www-user && \
    chown -R www-user:www-user /var/www

RUN git config --global --add safe.directory /var/www

WORKDIR /var/www

CMD ["php-fpm"]
