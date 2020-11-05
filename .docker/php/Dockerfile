FROM phpdockerio/php74-fpm:latest

RUN apt-get update && apt-get install -y \
    sendmail \
    vim \
    php7.4-gd \ 
    php7.4-pgsql \
    php7.4-bcmath

# Clear cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

WORKDIR "/var/www/html"

COPY composer.json composer.lock ./
RUN composer install --no-scripts --no-autoloader

COPY . .
RUN composer dump-autoload --optimize