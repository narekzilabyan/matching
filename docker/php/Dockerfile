FROM php:7.4-fpm

RUN apt-get update && \
    apt-get upgrade -y && \
    apt-get install -y curl git zip unzip libpq-dev libwebp-dev libzip-dev libpng-dev python3 libfreetype6-dev libjpeg62-turbo-dev procps sudo acl

RUN docker-php-ext-install pdo_mysql pdo_pgsql tokenizer exif zip && \
    docker-php-ext-configure gd --enable-gd --with-freetype --with-jpeg && \
    docker-php-ext-install -j$(nproc) gd && \
    apt -y autoremove

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

WORKDIR /var/www/matching

EXPOSE 9000
