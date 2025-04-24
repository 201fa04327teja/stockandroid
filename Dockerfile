FROM php:8.1-apache

# Install mysqli extension
RUN docker-php-ext-install mysqli

# Copy your PHP files into the container
COPY . /var/www/html/

EXPOSE 80
