FROM php:8.2-apache

# Enable Apache mod_rewrite (useful for pretty URLs or frameworks)
RUN a2enmod rewrite

# Copy all your site files to the Apache server's web root
COPY . /var/www/html/

EXPOSE 80
