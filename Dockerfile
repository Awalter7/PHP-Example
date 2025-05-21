# 1) Base image + system deps
FROM php:8.2-cli

RUN apt-get update \
 && apt-get install -y --no-install-recommends \
      curl gnupg unzip libzip-dev git \
 && curl -fsSL https://deb.nodesource.com/setup_18.x | bash - \
 && apt-get install -y nodejs zip \
 && docker-php-ext-install pdo_mysql zip \
 && rm -rf /var/lib/apt/lists/*

# 2) Composer
ENV COMPOSER_ALLOW_SUPERUSER=1 \
    COMPOSER_MEMORY_LIMIT=-1

RUN curl -sS https://getcomposer.org/installer | php -- \
     --install-dir=/usr/local/bin --filename=composer

WORKDIR /app

# 3) Front-end deps & build (needs your Vite config + resources)
COPY package.json package-lock.json vite.config.js postcss.config.js tailwind.config.js ./  
COPY resources/js resources/css ./resources
RUN npm ci \
 && npm run build

# 4) PHP deps
COPY composer.json composer.lock ./
RUN composer install \
    --no-dev \
    --optimize-autoloader \
    --no-interaction \
    --prefer-dist

# 5) Copy rest of app & discover packages
COPY . .
RUN php artisan package:discover --ansi

# 6) Expose & run
EXPOSE 8000
CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8000"]
