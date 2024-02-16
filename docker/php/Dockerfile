FROM php:fpm-alpine

COPY startup.sh /usr/local/bin/startup.sh

RUN docker-php-ext-install pdo pdo_mysql

#RUN chmod +x /usr/local/bin/startup.sh

# Imposta lo script come entrypoint predefinito del container
#CMD ["/usr/local/bin/startup.sh"]
