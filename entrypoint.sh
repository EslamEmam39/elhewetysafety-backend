#!/bin/sh
set -e

# لو .env مش موجود، انسخه من .env.example
if [ ! -f .env ]; then
  echo "Creating .env from .env.example..."
  cp .env.example .env
fi

# تثبيت الباكدجات لو vendor مش موجود أو composer.lock اتغير
echo "Installing composer dependencies..."
composer install --no-interaction --optimize-autoloader

# توليد APP_KEY لو مش موجود
if ! grep -q "^APP_KEY=base64" .env; then
  echo "Generating app key..."
  php artisan key:generate --force
fi

# استنى الداتا بيز تبقى جاهزة فعليًا (depends_on بس بيستنى الكونتينر يقوم، مش إن Postgres جاهز يستقبل اتصالات)
echo "Waiting for database..."
until php artisan db:show > /dev/null 2>&1; do
  sleep 1
done

echo "Running migrations..."
php artisan migrate --force

echo "Starting server..."
exec php artisan serve --host=0.0.0.0 --port=8000
