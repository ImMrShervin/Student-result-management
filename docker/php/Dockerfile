FROM php:8.4-fpm-alpine

RUN apk add --no-cache \
    $PHPIZE_DEPS \
    libpng-dev \
    libjpeg-turbo-dev \
    freetype-dev \
    libzip-dev \
    icu-dev \
    oniguruma-dev \
    zip \
    unzip \
    git \
    curl \
    bash \
    nodejs \
    npm \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) \
        gd \
        pdo \
        pdo_mysql \
        mbstring \
    	exif \
        intl \
        zip \
        bcmath \
        opcache \
        pcntl \
    && pecl install redis \
    && docker-php-ext-enable redis \
    && apk del $PHPIZE_DEPS

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

COPY docker/php/php.ini /usr/local/etc/php/conf.d/srms.ini

RUN addgroup -g 1000 srms && adduser -D -u 1000 -G srms srms

USER srms

EXPOSE 9000

CMD ["php-fpm"]