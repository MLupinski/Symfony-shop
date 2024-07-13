#!/bin/bash

if [ ! -f /var/www/html/composer.json ]; then
    composer create-project symfony/skeleton /var/www/html
fi


composer install -d /var/www/html

echo "Symfony installation script completed."

echo "Starting PHP-FPM"
exec $@
