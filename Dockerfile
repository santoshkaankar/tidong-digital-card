FROM php:8.3-apache

# Install system dependencies and PHP extensions
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    zip \
    unzip \
    git \
    curl \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd pdo pdo_mysql

# Enable Apache mod_rewrite
RUN a2enmod rewrite

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www/html

# Copy project files
COPY . /var/www/html

# Setup .env file
RUN cp .env.example .env \
    && sed -i 's/DB_CONNECTION=mysql/DB_CONNECTION=sqlite/g' .env \
    && sed -i 's/# DB_DATABASE=/DB_DATABASE=/g' .env

# Run composer install to generate vendor folder
RUN composer install --no-dev --optimize-autoloader

# Generate App Key
RUN php artisan key:generate

# Create SQLite database file and run migrations
RUN mkdir -p /var/www/html/database && touch /var/www/html/database/database.sqlite
RUN php artisan migrate --force

# Set Apache document root to Laravel's public directory
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/conf-available/*.conf

# Set permissions for storage, database and bootstrap cache
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/database /var/www/html/bootstrap/cache \
    && chmod -R 775 /var/www/html/storage /var/www/html/database /var/www/html/bootstrap/cache

EXPOSE 80
