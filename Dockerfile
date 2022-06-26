# base layer with all OS dependencies IN ==============================================================================
FROM php:8.1.4-fpm-alpine as base
RUN apk update && apk add ${PHPIZE_DEPS} \
    g++ make git wget ca-certificates openssl openssh bzip2-dev zlib-dev libpng-dev tzdata fcgi cyrus-sasl-dev libpq-dev
# php8-dev
RUN update-ca-certificates
# install php packages
# possible options in `docker-php-exe-install'
# bcmath   |fileinfo |json      |pdo_firebird |readline   |standard   |zend_test
# bz2      |filter   |ldap      |pdo_mysql    |reflection |sysvmsg    |zip
# calendar |ftp      |mbstring  |pdo_oci      |session    |sysvsem
# ctype    |gd       |mysqli    |pdo_odbc     |shmop      |sysvshm
# curl     |gettext  |oci8      |pdo_pgsql    |simplexml  |tidy
# dba      |gmp      |odbc      |pdo_sqlite   |snmp       |tokenizer
# dom      |hash     |opcache   |pgsql        |soap       |xml
# enchant  |iconv    |pcntl     |phar         |sockets    |xmlreader
# exif     |imap     |pdo       |posix        |sodium     |xmlwriter
# ffi      |intl     |pdo_dblib |pspell       |spl        |xsl
RUN docker-php-ext-install bcmath
RUN docker-php-ext-install bz2
RUN docker-php-ext-install mysqli
RUN docker-php-ext-install pdo_mysql
RUN docker-php-ext-install pgsql
RUN docker-php-ext-install pdo_pgsql
RUN docker-php-ext-install gd
RUN pecl install redis && docker-php-ext-enable redis
# development layer ===================================================================================================
FROM base as dev
ENV XDEBUG_CONF=/usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini
# install php composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
RUN apk add --no-cache -t .deps $PHPIZE_DEPS && \
    pecl install xdebug && \
    docker-php-ext-enable xdebug

# CODE layer ==========================================================================================================
FROM base as ci
WORKDIR /application/
COPY . .
RUN composer install

FROM ci as prod
WORKDIR /application/
RUN composer install --no-dev
ENV DYNAMIC_FPM_HOST="127.0.0.1"
RUN mkdir -p /run/nginx && \
    # create a conf file
    cat .docker/nginx/nginx.conf > /etc/nginx/conf.d/default.conf && \
    # change fpm host
    sed -i 's/php-fpm:9000/127.0.0.1:9000/' /etc/nginx/conf.d/default.conf && \
    # remove .docker folder
    rm -rf .docker && \
    # update composer autoloader
    composer dump-autoload && php artisan optimize
CMD php-fpm -DR && nginx -g "daemon off;"
