# ---------------------------------------------------------------
# Base image: PHP-FPM 8.4 with the PostgreSQL driver
# ---------------------------------------------------------------
FROM php:8.4-fpm-alpine AS app-base

# libpq stays installed: pdo_pgsql.so needs libpq.so.5 at runtime.
RUN apk add --no-cache \
        libpq \
    && apk add --no-cache --virtual .build-deps \
        $PHPIZE_DEPS \
        postgresql-dev \
        linux-headers \
    && docker-php-ext-install pdo_pgsql \
    && apk del .build-deps

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# ---------------------------------------------------------------
# Stage 1: install Composer dependencies (production only)
# ---------------------------------------------------------------
FROM app-base AS vendor

WORKDIR /var/www/html

COPY composer.json composer.lock ./

RUN composer install \
    --no-dev \
    --prefer-dist \
    --no-interaction \
    --no-scripts \
    --no-autoloader

# ---------------------------------------------------------------
# Stage 2: build the frontend assets with Vite (glibc image: the
# lockfile only ships glibc native bindings for Rolldown)
# ---------------------------------------------------------------
FROM node:22 AS assets

WORKDIR /app

# Wayfinder's PHP generation step is skipped inside the container;
# the generated TypeScript files are already committed to the repo.
ENV WAYFINDER_COMMAND=true

COPY package.json package-lock.json .npmrc ./
RUN npm ci

COPY resources ./resources
COPY vite.config.ts tsconfig.json ./
RUN npm run build

# ---------------------------------------------------------------
# Final stage: application code + vendor + built assets
# ---------------------------------------------------------------
FROM app-base

WORKDIR /var/www/html

COPY . .

COPY --from=vendor /var/www/html/vendor ./vendor
COPY --from=assets /app/public/build ./public/build

RUN composer dump-autoload --optimize --no-dev \
    && chown -R www-data:www-data storage bootstrap/cache

COPY docker/php/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

EXPOSE 9000

ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
CMD ["php-fpm"]
