#!/bin/sh
set -e

npm ci
npm run build

php artisan serve --host=0.0.0.0 --port=8000 &