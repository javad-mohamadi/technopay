FROM php:8.4-fpm-alpine

WORKDIR /var/www

RUN apk add --update --no-cache \
    libpng-dev \
    libjpeg-turbo-dev \
    freetype-dev \
    libzip-dev \
    icu-dev \
    libexif-dev \
    bash \
    shadow \
    git \
    curl \
    build-base \
    autoconf \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) gd pdo pdo_mysql zip exif \
    \
    && pecl install redis \
    && docker-php-ext-enable redis \
    \
    && rm -rf /var/cache/apk/* /tmp/pear

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

EXPOSE 9000

ENTRYPOINT ["./docker/entrypoint-app.sh"]
