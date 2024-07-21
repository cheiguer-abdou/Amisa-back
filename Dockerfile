FROM php:8.3.3

RUN docker-php-ext-install pdo pdo_mysql
CMD php artisan serve --host=127.0.0.1 --port=9090
