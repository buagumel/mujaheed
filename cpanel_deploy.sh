#!/bin/bash

set -e

APP_DIR="/home/ffvmkdiy/repositories/mujaheed"
PUBLIC_DIR="/home/ffvmkdiy/public_html"

echo "🚀 Deploying Mujaheed Laravel application..."

cd "$APP_DIR"

echo "📦 Installing Composer dependencies..."
composer install --no-dev --optimize-autoloader

echo "🗄️ Running database migrations..."
php artisan migrate --force

echo "🧹 Clearing Laravel cache..."
php artisan optimize:clear

echo "⚡ Optimizing Laravel..."
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan optimize

echo "📁 Publishing public files..."
cp -a "$APP_DIR/public/." "$PUBLIC_DIR/"

echo "🔧 Fixing Laravel entry point..."

sed -i "s|require __DIR__.'/../vendor/autoload.php';|require '/home/ffvmkdiy/repositories/mujaheed/vendor/autoload.php';|" "$PUBLIC_DIR/index.php"

sed -i "s|__DIR__.'/../storage/framework/maintenance.php'|'/home/ffvmkdiy/repositories/mujaheed/storage/framework/maintenance.php'|" "$PUBLIC_DIR/index.php"

sed -i "s|__DIR__.'/../bootstrap/app.php'|'/home/ffvmkdiy/repositories/mujaheed/bootstrap/app.php'|" "$PUBLIC_DIR/index.php"

echo "🔗 Creating storage link..."

if [ ! -L "$APP_DIR/public/storage" ]; then
    ln -s "$APP_DIR/storage/app/public" "$APP_DIR/public/storage"
fi

echo "🔐 Setting permissions..."

chmod -R 775 "$APP_DIR/storage"
chmod -R 775 "$APP_DIR/bootstrap/cache"

echo "✅ Deployment complete!"
