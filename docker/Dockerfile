# Используем официальный образ PHP
FROM php:8.2-apache

# Устанавливаем системные зависимости + библиотеки для GD
RUN apt-get update && \
    apt-get install -y \
        libpng-dev \
        libjpeg62-turbo-dev \
        libfreetype6-dev \
        libzip-dev \
        zip \
        unzip && \
    docker-php-ext-configure gd --with-freetype --with-jpeg && \
    docker-php-ext-install gd

RUN apt-get update && apt-get install -y acl
RUN setfacl -R -m u:www-data:rwx /var/run

# Устанавливаем PHP расширения
RUN docker-php-ext-install pdo pdo_mysql mysqli #Зачем?
RUN docker-php-ext-install sockets

# Включаем mod_rewrite в Apache
RUN a2enmod rewrite

# Устанавливаем Xdebug
RUN pecl install xdebug && docker-php-ext-enable xdebug

# Конфиг Xdebug
RUN echo "xdebug.mode=debug" >> /usr/local/etc/php/conf.d/xdebug.ini && \
    echo "xdebug.start_with_request=yes" >> /usr/local/etc/php/conf.d/xdebug.ini && \
    echo "xdebug.client_host=host.docker.internal" >> /usr/local/etc/php/conf.d/xdebug.ini && \
    echo "xdebug.client_port=9003" >> /usr/local/etc/php/conf.d/xdebug.ini && \
    echo "xdebug.log=/tmp/xdebug.log" >> /usr/local/etc/php/conf.d/xdebug.ini && \
    echo "xdebug.idekey=PHPSTORM" >> /usr/local/etc/php/conf.d/xdebug.ini && \
    echo "xdebug.discover_client_host=0" >> /usr/local/etc/php/conf.d/xdebug.ini
    
RUN mkdir -p /var/www/html/bitrix/tmp/sessions && \
    chown -R www-data:www-data /var/www/html/bitrix/tmp && \
    chmod -R 755 /var/www/html/bitrix/tmp
