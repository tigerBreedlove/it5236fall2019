FROM php:7.2-apache

RUN docker-php-ext-install pdo pdo_mysql

# Copy the database credentials file to the server
COPY webapp/ /var/www/html/
COPY api/ /var/www/html/api/
COPY credentials.php /var/www/html/api
