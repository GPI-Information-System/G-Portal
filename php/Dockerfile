FROM php:8.2-apache

COPY --from=mlocati/php-extension-installer /usr/bin/install-php-extensions /usr/local/bin/

RUN install-php-extensions gd imagick zip pdo_mysql mysqli

RUN a2enmod rewrite

RUN git \
    unzip

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

COPY ./src /var/www/html

COPY ./src/composer.* ./

RUN COMPOSER_ALLOW_SUPERUSER=1 composer install --no-dev --optimize-autoloader --no-interaction --no-plugins --ignore-platform-reqs

COPY php/custom.ini /usr/local/etc/php/conf.d/custom.ini

RUN chown -R www-data:www-data /var/www/html