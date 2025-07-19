#/bin/bash
cp .env.example .env && podman run --rm \
    -v "$(pwd):/var/www/html":z \
    -w /var/www/html \
    laravelsail/php84-composer:latest \
    composer install --ignore-platform-reqs && php artisan key:generate
