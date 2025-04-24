# php image with apache httpd
FROM php:7.2-apache

# Install the application dependencies
# libpq-dev : Development files for building PostgreSQL client tools
# docker-php-ext-configure pgsql: Install pgsql
# extention and adds it to php.ini (Fitxer de conf de php)
RUN apt-get update && apt-get install -y libpq-dev \
    && docker-php-ext-install pgsql pdo_pgsql

# Add index.php to correct route
COPY index.php /var/www/html/