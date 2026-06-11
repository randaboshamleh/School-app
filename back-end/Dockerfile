FROM php:8.3-fpm

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    unzip \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    libzip-dev \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www

# انسخ كل المشروع (وليس فقط composer.json)
COPY . .

# شغل Composer
RUN composer install --no-dev --optimize-autoloader --no-interaction

# أعط صلاحيات للتخزين و الكاش
RUN chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache
