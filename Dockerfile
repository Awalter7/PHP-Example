# ─── 1) Base image ─────────────────────────────────────
FROM php:8.2-cli

# ─── 2) Install system tools, Node.js/npm & PHP extensions ─
RUN apt-get update \
 && apt-get install -y --no-install-recommends \
      curl gnupg zip unzip libzip-dev git \
 && curl -fsSL https://deb.nodesource.com/setup_18.x | bash - \
 && apt-get install -y nodejs \
 && docker-php-ext-configure zip \
 && docker-php-ext-install \
      pdo_mysql \
      zip \
 && rm -rf /var/lib/apt/lists/*

# ─── 3) Install Composer globally (unlimited memory) ───────
ENV COMPOSER_ALLOW_SUPERUSER=1 \
    COMPOSER_MEMORY_LIMIT=-1
RUN curl -sS https://getcomposer.org/installer | php -- \
     --install-dir=/usr/local/bin --filename=composer

WORKDIR /app

# ─── 4) Copy only the files needed for "composer install" ──
COPY composer.json composer.lock ./

# ─── 5) Install PHP deps but skip all post-install scripts ─
RUN composer install \
    --no-dev \
    --optimize-autoloader \
    --no-interaction \
    --prefer-dist \
    --no-scripts

# ─── 6) Now bring in your full Laravel app ────────────────
COPY . .

# ─── 7) Run the package:discover hook now that artisan exists ─
RUN php artisan package:discover --ansi

# ─── 8) Install JS deps ───────────────────────────────────
RUN npm ci

# ─── 9) Expose and start your combined dev servers ────────
EXPOSE 8080
CMD ["php", "artisan", "serve:dev"]
