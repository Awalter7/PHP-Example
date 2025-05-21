# ─── 1) Use PHP 8.2 ─────────────────────────────
FROM php:8.2-cli

# ─── 2) System deps, Node.js/npm, PHP exts ───
RUN apt-get update \
 && apt-get install -y --no-install-recommends \
      curl gnupg zip unzip libzip-dev git \
 && curl -fsSL https://deb.nodesource.com/setup_18.x | bash - \
 && apt-get install -y nodejs \
 && docker-php-ext-configure zip \
 && docker-php-ext-install pdo_mysql zip \
 && rm -rf /var/lib/apt/lists/*

# ─── 3) Composer (unlimited memory) ───
ENV COMPOSER_ALLOW_SUPERUSER=1 \
    COMPOSER_MEMORY_LIMIT=-1
RUN curl -sS https://getcomposer.org/installer | php -- \
    --install-dir=/usr/local/bin --filename=composer

WORKDIR /app

# ─── 4) Copy only the files Composer’s hooks need ───
COPY composer.json composer.lock ./
COPY artisan bootstrap/ ./

# ─── 5) Run Composer now that artisan is present ───
RUN composer install \
    --no-dev \
    --optimize-autoloader \
    --no-interaction \
    --prefer-dist

# ─── 6) JS dependencies ───
COPY package.json package-lock.json ./
RUN npm ci

# ─── 7) Copy the rest of your app ───
COPY . .

# ─── 8) Expose and run your serve:dev ───
EXPOSE 8080
CMD ["php", "artisan", "serve:dev"]
