FROM php:8.2-apache

RUN apt-get update && \
    apt-get install -y sendmail && \
    a2enmod rewrite

COPY . /var/www/html/

EXPOSE 80
