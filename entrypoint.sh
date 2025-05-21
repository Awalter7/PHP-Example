#!/bin/sh
set -e

rm -rf node_modules
npm install
npm run build

php artisan serve --host=0.0.0.0 --port=8000

