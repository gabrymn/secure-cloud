FROM php:fpm-alpine

#COPY startup.sh /usr/local/bin/startup.sh

RUN docker-php-ext-install pdo pdo_mysql

# Concede i permessi di esecuzione allo script di avvio
#RUN chmod +x /usr/local/bin/startup.sh

# Utilizza lo script come punto di avvio
#CMD ["/usr/local/bin/startup.sh"]
