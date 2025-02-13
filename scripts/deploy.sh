#!/bin/bash

# Exit on error
set -e

# Pull the latest changes
git pull origin main

# Install/update Composer dependencies
composer install --no-interaction --prefer-dist --optimize-autoloader

# Install/update npm dependencies
npm install
npm run build

# Clear and cache routes, config, and views
php artisan optimize:clear
php artisan optimize

# Run database migrations
php artisan migrate --force

# Setup Elasticsearch
php artisan elasticsearch:setup

# Restart queue workers
php artisan queue:restart

# Clear OPcache
curl -X GET http://your-domain.com/clear-cache.php

# Output success message
echo "Deployment completed successfully!"