# Set master image
FROM php:7.3-fpm-alpine

LABEL maintainer="Mai Quang Tuyen (maituyen100897@gmail.com)"

# Set working directory
WORKDIR /var/www/html

#Install composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
RUN composer self-update 1.10.10

# Install Additional dependencies
RUN apk update && apk add --no-cache \
    build-base shadow supervisor \
    php7-common \
    php7-pdo \
    php7-pdo_mysql \
    php7-mysqli \
    php7-mcrypt \
    php7-mbstring \
    php7-xml \
    php7-openssl \
    php7-json \
    php7-phar \
    php7-zip \
    php7-gd \
    php7-dom \
    php7-session \
    php7-zlib \
    busybox-suid

# Add and Enable PHP-PDO Extenstions
RUN docker-php-ext-install pdo pdo_mysql
RUN docker-php-ext-enable pdo_mysql
RUN docker-php-ext-install pcntl
RUN docker-php-ext-install sockets

# Remove Cache
RUN rm -rf /var/cache/apk/*

# Use the default production configuration ($PHP_INI_DIR is variable already set by the default image)
RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"

# Start Laravel worker (no need if enable Horizon)
# COPY .docker/supervisor.d/worker.conf /etc/supervisor.d/worker.conf
#---------------------------------

#----------ADD USER------------
# Production
# RUN addgroup -g 1001 mqt
# RUN adduser -D -u 1001 mqt -G mqt

# RUN chown -R mqt:mqt .


RUN adduser -D -u 502 www -G dialout
# Copy existing application directory permissions
COPY . .

RUN chown -R www:dialout . 
# Change current user to www
USER www

