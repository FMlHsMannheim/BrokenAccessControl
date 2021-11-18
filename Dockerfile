FROM php:7.4-apache
LABEL Name=brokenaccesscontrol Version=0.0.1
COPY BrokenAccessControl/ /var/www/html/BrokenAccessControl/

EXPOSE 80

RUN chmod 777 -R /var/www/html/BrokenAccessControl/users/
RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"
RUN docker-php-ext-install mysqli
RUN a2enmod rewrite
RUN service apache2 restart