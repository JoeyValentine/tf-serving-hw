FROM php:7.2-apache

RUN apt-get update -y
RUN apt-get install python3-pip -y
RUN pip3 install requests
RUN pip3 install Pillow
RUN pip3 install numpy
RUN docker-php-ext-install mysqli

WORKDIR /var/www/html

COPY htdocs/ /var/www/html/

EXPOSE 80

ENTRYPOINT ["docker-php-entrypoint"]
CMD ["apache2-foreground"]
