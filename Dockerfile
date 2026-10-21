# TIDONG Laravel 13 - Render Docker production image.
# Each container start applies ONLY pending Laravel migrations before Apache runs.
FROM node:22-bookworm-slim AS assets
WORKDIR /build
COPY package.json package-lock.json ./
RUN npm ci --ignore-scripts --no-audit --no-fund
COPY vite.config.js ./
COPY resources ./resources
COPY public ./public
RUN rm -f public/hot public/fonts-manifest.dev.json \
    && npm run build \
    && test -s public/build/manifest.json

FROM php:8.3-apache-bookworm AS php-base
RUN set -eux; \
    apt-get update; \
    apt-get install -y --no-install-recommends \
      libpq-dev libicu-dev libzip-dev libonig-dev \
      libpng-dev libjpeg62-turbo-dev libfreetype6-dev \
      unzip ca-certificates; \
    docker-php-ext-configure gd --with-freetype --with-jpeg; \
    docker-php-ext-install -j "$(nproc)" \
      pdo_pgsql pgsql pdo_mysql bcmath mbstring intl zip gd opcache; \
    a2enmod rewrite headers; \
    rm -rf /var/lib/apt/lists/*

FROM php-base AS deps
WORKDIR /var/www/html
COPY --from=composer:2 /usr/bin/composer /usr/local/bin/composer
COPY composer.json composer.lock ./
RUN COMPOSER_ALLOW_SUPERUSER=1 composer install \
      --no-dev --prefer-dist --no-interaction --no-progress \
      --no-scripts --no-autoloader --no-plugins \
    && test -f vendor/autoload.php || test -d vendor/composer

FROM php-base AS runtime
WORKDIR /var/www/html
COPY --from=composer:2 /usr/bin/composer /usr/local/bin/composer
COPY . ./
COPY --from=deps /var/www/html/vendor ./vendor
COPY --from=assets /build/public/build ./public/build
COPY docker/apache-site.conf.template /etc/apache2/sites-available/000-default.conf.template
COPY docker/php-production.ini /usr/local/etc/php/conf.d/99-tidong-production.ini
COPY docker/entrypoint.sh /usr/local/bin/tidong-start
RUN set -eux; \
    rm -f public/hot public/fonts-manifest.dev.json; \
    mkdir -p storage/framework/cache/data storage/framework/sessions \
      storage/framework/views storage/logs bootstrap/cache; \
    rm -f bootstrap/cache/packages.php bootstrap/cache/services.php bootstrap/cache/config.php; \
    COMPOSER_ALLOW_SUPERUSER=1 composer dump-autoload \
      --no-dev --optimize --no-interaction --no-scripts; \
    php artisan package:discover --ansi; \
    sed "s/__PORT__/10000/g" /etc/apache2/sites-available/000-default.conf.template > /etc/apache2/sites-available/000-default.conf; \
    sed -i "s/^Listen 80$/Listen 10000/" /etc/apache2/ports.conf; \
    sed -i 's/\r$//' /usr/local/bin/tidong-start; \
    chmod 0755 /usr/local/bin/tidong-start; \
    chown -R www-data:www-data storage bootstrap/cache /var/run/apache2 /var/lock/apache2 /var/log/apache2; \
    chmod -R u+rwX,g-rwx,o-rwx storage bootstrap/cache; \
    rm /usr/local/bin/composer

ENV APP_ENV=production APP_DEBUG=false PORT=10000 \
    DB_SCHEMA=tidong_v2 DB_SSLMODE=require SESSION_SECURE_COOKIE=true
EXPOSE 10000
USER www-data
ENTRYPOINT ["/usr/local/bin/tidong-start"]
