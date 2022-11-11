FROM php:8.0-fpm

RUN apt-get update && apt-get upgrade -y \
	libonig-dev \
	libmcrypt-dev \
	zlib1g-dev \
	libpng-dev \
	libxml2-dev

## ZIP
RUN apt-get install -y \
    libzip-dev \
    zip \
    && docker-php-ext-install zip

## PDO
RUN docker-php-ext-install pdo
RUN docker-php-ext-install pdo pdo_mysql

## CONFIGURE
RUN docker-php-ext-configure gd

## GD
RUN docker-php-ext-install gd
RUN docker-php-ext-install gd mysqli
RUN docker-php-ext-install dom
RUN docker-php-ext-install intl

## LIBS
RUN docker-php-ext-install opcache
RUN docker-php-ext-install bcmath
RUN docker-php-ext-install mbstring

## XDEBUG
# Instalando o Xdebug
RUN echo "Instalando o Xdebug"
RUN pecl install xdebug-3.1.5 && docker-php-ext-enable xdebug \
    && echo "xdebug.client_port=9010" >> /usr/local/etc/php/php.ini \
    && echo "xdebug.discover_client_host=true" >> /usr/local/etc/php/php.ini \
    && echo "xdebug.idekey=PHPSTORM" >> /usr/local/etc/php/php.ini \
    && echo "xdebug.start_with_request=yes" >> /usr/local/etc/php/php.ini \
    && echo "xdebug.mode=develop,debug,coverage" >> /usr/local/etc/php/php.ini \
    && echo "xdebug.log=/tmp/xdebug.log" >> /usr/local/etc/php/php.ini


RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

ADD php.ini /usr/local/etc/php/conf.d/php.ini

EXPOSE 9000
CMD ["php-fpm"]
