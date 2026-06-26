FROM php-8.2

COPY ./src /var/www/html

RUN chown -R www-data:www-data /var/www/html