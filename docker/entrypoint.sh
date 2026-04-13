#!/usr/bin/env bash
set -e

cd /var/www/html

echo "[INFO] Starting entrypoint.sh..."

# Check if .env is missing
if [ ! -f .env ]; then
  echo "[INFO] .env missing creating .env file..."
  cp .env.example .env
  chmod 666 .env
fi

# Install dependencies if missing
if [ ! -f vendor/autoload.php ]; then
  echo "[INFO] Dependencies missing installing them now..."
  composer install --no-interaction --optimize-autoloader
fi

# Install Node.js dependencies if missing
if [ ! -d node_modules ]; then
  echo "[INFO] Node modules missing, installing them now..."
  npm install
fi

# Generate app key if missing
if ! grep -q "^APP_KEY=" .env || [ -z "$(grep '^APP_KEY=' .env | cut -d'=' -f2)" ]; then
  echo "[INFO] App key missing, generating app key..."
  php artisan key:generate
fi

# build Vite related things
echo "[INFO] Building npm dependencies..."
npm run build

# Run database migrations
echo "[INFO] Running database migrations..."
php artisan migrate:fresh
php artisan db:seed

# Create Pest cache (used for testing)
echo "[INFO] Creating Pest cache directory..."
mkdir -p .pest/cache
chmod 775 .pest/cache

exec php artisan serve --host=0.0.0.0 --port=8000
