FROM php:8.2-fpm

# Set working directory
WORKDIR /var/www

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libjpeg-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    libzip-dev \
    default-mysql-client \
    nginx \
    supervisor

# Install PHP extensions
RUN docker-php-ext-install pdo pdo_mysql mbstring zip exif pcntl bcmath gd

# Install Composer (from official Composer image)
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copy project files to container
COPY . /var/www

# Install PHP dependencies
RUN composer install --no-dev --optimize-autoloader

# Set correct permissions
RUN chown -R www-data:www-data /var/www

# Use non-root user
USER www-data

# Expose the Laravel development server port
EXPOSE 8000

# Run migrations and start Laravel server
CMD php artisan migrate --force && php artisan serve --host=0.0.0.0 --port=$PORT



