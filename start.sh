#!/bin/bash
# Stop already running conaiters and volumes
docker-compose down -v
docker-compose build && docker-compose up -d

sleep 10

# Installing composer packages 
echo "--- Installing composer packages ----"
docker exec app composer install --prefer-dist

# Generating application cache
echo "--- Generating application cache ---"
docker exec app php artisan config:cache

# Running migration of application 
echo "--- Running migration ---"
docker exec app php artisan migrate

# Providing permission for storage folder
docker exec app bash -c "chmod -R 777 storage/*"

# Running unit test cases 
echo "--- Running Unit test cases ---"
docker exec app vendor/bin/phpunit tests/Unit/

# Running Feature test cases
echo "--- Running Feature test cases ---"
docker exec app vendor/bin/phpunit tests/Feature/	 