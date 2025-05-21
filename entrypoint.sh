#!/bin/sh
set -e

# 1) Run migrations, etc (optional—drop if you don’t need it)
# php artisan migrate --force &

# 2) Start Laravel on 0.0.0.0:8080
php artisan serve --host=0.0.0.0 --port=8080 &

# 3) Start Vite
npm run dev