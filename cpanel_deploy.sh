#!/bin/bash

set -e

APP_DIR="/home/ffvmkdiy/repositories/mujaheed"

echo "🚀 Deploying Laravel application..."

cd "$APP_DIR"

echo "📦 Installing Composer dependencies..."
composer install --no-dev --optimize-autoloader --no-interaction

echo "🗄️ Running migrations..."
php artisan migrate --force

echo "🧹 Clearing caches..."
php artisan optimize:clear

echo "⚡ Rebuilding caches..."
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan optimize

echo "🔐 Fixing permissions..."
chmod -R 775 "$APP_DIR/storage"
chmod -R 775 "$APP_DIR/bootstrap/cache"

echo "📁 Ensuring public storage directory exists..."
mkdir -p /home/ffvmkdiy/public_html/storage

echo "✅ Deployment completed successfully."
