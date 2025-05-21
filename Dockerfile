# ─── 1) Base PHP 8.2 + Node.js image ────────────────
FROM php:8.2-cli

# ─── 2) System deps, Node/npm, PHP extensions ───────
RUN apt-get update \
 && apt-get install -y --no-install-recommends \
      curl gnupg zip unzip libzip-dev git \
 && curl -fsSL https://deb.nodesource.com/setup_18.x | bash - \
 && apt-get install -y nodejs \
 && docker-php-ext-configure zip \
 && docker-php-ext-install pdo_mysql zip \
 && rm -rf /var/lib/apt/lists/*

# ─── 3) Composer (no memory limit) ───────────────────
ENV COMPOSER_ALLOW_SUPERUSER=1 \
    COMPOSER_MEMORY_LIMIT=-1
RUN curl -sS https://getcomposer.org/installer | php -- \
     --install-dir=/usr/local/bin --filename=composer

WORKDIR /app

# ─── 4) Install PHP deps *without* scripts ───────────
COPY composer.json composer.lock ./
RUN composer install \
    --no-dev \
    --optimize-autoloader \
    --no-interaction \
    --prefer-dist \
    --no-scripts

# ─── 5) Copy full app & run discovery ────────────────
COPY . .
RUN php artisan package:discover --ansi

# ─── 6) JS deps ──────────────────────────────────────
RUN npm ci

# ─── 7) Expose port & launch your serve:dev command ──


COPY entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

EXPOSE 8000
EXPOSE 5173 

ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]