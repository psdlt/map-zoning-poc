FROM php:7.4-apache

RUN apt-get update && apt-get install -y libzip-dev libc6-dev libpq-dev git libssl-dev
RUN echo "ServerName localhost" >> /etc/apache2/apache2.conf

RUN echo "y" | pecl install apcu
RUN docker-php-ext-install zip pdo_pgsql bcmath
RUN docker-php-ext-enable opcache apcu

RUN a2enmod rewrite headers
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

ADD docker/php.ini /usr/local/etc/php/conf.d/custom.ini
ADD docker/apache.conf /etc/apache2/sites-available/000-default.conf

WORKDIR /var/www/html
COPY . /var/www/html

CMD composer install && \
    mkdir -p var/cache && chmod -R 777 var/cache && \
    mkdir -p var/log && chmod -R 777 var/log && \
    mkdir -p var/profiling && chmod -R 777 var/profiling && \
    bin/console d:m:m -n --allow-no-migration && \
    apache2-foreground
