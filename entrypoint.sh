#!/bin/sh
set -e

php artisan serve --host=0.0.0.0 --port=8000

rm -rf node_modules
npm install
npm run build