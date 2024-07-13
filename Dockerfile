FROM php:8.2-fpm

RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libzip-dev \
    && docker-php-ext-install zip pdo_mysql
    

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html


COPY scripts/install-symfony.sh /usr/local/bin/
RUN chmod +x /usr/local/bin/install-symfony.sh


CMD ["sh", "/usr/local/bin/install-symfony.sh", "php-fpm","-F"]
