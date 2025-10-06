FROM php:8.4-fpm-alpine

ARG USER_ID=1000
ARG USER_NAME=www-user
ARG GROUP_ID=1000
ARG GROUP_NAME=www-user
ARG PHP_MEMORY_LIMIT=512M

RUN apk update && apk add --no-cache \
    git curl libpng-dev libwebp-dev libjpeg-turbo-dev freetype-dev \
    libzip-dev postgresql-dev icu-dev oniguruma-dev rabbitmq-c-dev \
    supervisor linux-headers autoconf make g++

RUN docker-php-ext-configure gd --with-freetype --with-jpeg --with-webp && \
    docker-php-ext-install pdo_mysql pdo_pgsql zip exif pcntl gd intl mbstring bcmath

RUN pecl install redis amqp && docker-php-ext-enable redis amqp

RUN apk add --no-cache bash nano vim

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

RUN echo "memory_limit = ${PHP_MEMORY_LIMIT}" > /usr/local/etc/php/conf.d/memory-limit.ini

RUN addgroup -g ${GROUP_ID} ${GROUP_NAME} && \
    adduser -u ${USER_ID} -G ${GROUP_NAME} -D ${USER_NAME} && \
    chown -R ${USER_NAME}:${GROUP_NAME} /var/www

RUN git config --global --add safe.directory /var/www

WORKDIR /var/www

USER ${USER_NAME}

CMD ["php-fpm"]
