FROM php:8.2-apache

# Instalar extensiones necesarias
RUN docker-php-ext-install pdo pdo_mysql

# Copiar código del backend al contenedor
COPY . /var/www/html/

# Habilitar Apache Rewrite (importante para APIs)
RUN a2enmod rewrite
