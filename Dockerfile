# ─── 1) Base image ───────────────────────────────
FROM php:8.2-cli

# ─── 2) System deps, Node.js/npm, PHP extensions ──
RUN apt-get update \
 && apt-get install -y --no-install-recommends \
      curl gnupg zip unzip libzip-dev git \
 && curl -fsSL https://deb.nodesource.com/setup_18.x | bash - \
 && apt-get install -y nodejs \
 && docker-php-ext-configure zip \
 && docker-php-ext-install pdo_mysql zip \
 && rm -rf /var/lib/apt/lists/*

# ─── 3) Install Composer globally ──────────────────
ENV COMPOSER_ALLOW_SUPERUSER=1 \
    COMPOSER_MEMORY_LIMIT=-1
RUN curl -sS https://getcomposer.org/installer | php -- \
     --install-dir=/usr/local/bin --filename=composer

WORKDIR /app

# ─── 4) Install PHP deps without running hooks ─────
COPY composer.json composer.lock ./
RUN composer install \
    --no-dev \
    --optimize-autoloader \
    --no-interaction \
    --prefer-dist \
    --no-scripts

# ─── 5) Bring in your full application code ────────
COPY . .

# ─── 6) Run package discovery now that artisan exists ─
RUN php artisan package:discover --ansi

# ─── 7) Install JS deps ────────────────────────────
RUN npm ci

# ─── 8) Expose port and start your serve:dev command ─
EXPOSE 8080
CMD ["php", "artisan", "serve:dev"]
