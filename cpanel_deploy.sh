#!/bin/bash

# Automatic Post-Deploy Commands Script for cPanel SSH Terminal
echo "🚀 Running cPanel Laravel Post-Deployment Tasks..."

# 1. Install/Update PHP Dependencies
composer install --no-dev --optimize-autoloader

# 2. Run Database Migrations
php artisan migrate --force

# 3. Clear & Optimize Laravel Cache
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan optimize

echo "✅ cPanel Deployment Complete!"
