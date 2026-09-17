FROM php:8.2-apache

COPY --from=mlocati/php-extension-installer /usr/bin/install-php-extensions /usr/local/bin/

RUN install-php-extensions gd imagick zip pdo_mysql mysqli

RUN a2enmod rewrite

RUN apt-get update && apt-get install -y \
    git \
    unzip \
    # nodejs \
    # npm \
    && rm -rf /var/lib/apt/lists/*

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# [Optional] Remove comment if this project uses npm or composer for dependency management. Adjust the commands as necessary for your project.

# COPY ./src/package*.json ./
# RUN npm ci --only=production

COPY ./src/composer.* ./
RUN COMPOSER_ALLOW_SUPERUSER=1 composer install --no-dev --optimize-autoloader --no-interaction --no-plugins --ignore-platform-reqs

COPY php/custom.ini /usr/local/etc/php/conf.d/custom.ini

COPY ./src /var/www/html
RUN chown -R www-data:www-data /var/www/html