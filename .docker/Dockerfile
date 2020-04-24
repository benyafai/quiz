FROM php:7.4.5-apache

COPY . /src
COPY .docker/vhost.conf /etc/apache2/sites-available/000-default.conf

RUN rm -rf /var/www/html && mv /src /var/www/html &&\
    chown -R www-data:www-data /var/www/html/ &&\
    chmod -R 664 /var/www/html/ &&\ 
    find /var/www/html/ -type d -exec chmod 755 {} \;
RUN apt-get update &&\
    docker-php-ext-install pdo_mysql
RUN a2enmod rewrite \
    && echo "ServerTokens Prod\n" >> /etc/apache2/apache2.conf \
    && echo "ServerSignature Off\n" >> /etc/apache2/apache2.conf \
    && echo "php_admin_flag display_errors off\n" >> /etc/apache2/apache2.conf \
    && echo "php_admin_flag expose_php off\n" >> /etc/apache2/apache2.conf
RUN service apache2 restart
