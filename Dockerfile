FROM php:8.4-apache

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    nano \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    libzip-dev \
    nodejs \
    npm \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip

# Enable Apache modules
RUN a2enmod rewrite

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www/html

# Copy application files
COPY . .

# Build arguments for database configuration
ARG DB_CONNECTION=mysql
ARG DB_HOST=mysql_db
ARG DB_PORT=3306
ARG DB_DATABASE=GoodWorld
ARG DB_USERNAME=root
ARG DB_PASSWORD=password
ARG APP_ENV=local
ARG APP_DEBUG=false
ARG APP_URL=http://localhost

# Install PHP dependencies FIRST (before trying to use artisan)
RUN composer install --no-interaction --optimize-autoloader --no-dev

# Create .env file from .env.example or directly (after composer install)
RUN if [ -f .env.example ]; then \
        cp .env.example .env; \
    else \
        echo "Creating .env file from scratch"; \
        touch .env; \
    fi && \
    # Update database configuration using | as delimiter
    sed -i "s|DB_CONNECTION=.*|DB_CONNECTION=${DB_CONNECTION}|" .env && \
    sed -i "s|DB_HOST=.*|DB_HOST=${DB_HOST}|" .env && \
    sed -i "s|DB_PORT=.*|DB_PORT=${DB_PORT}|" .env && \
    sed -i "s|DB_DATABASE=.*|DB_DATABASE=${DB_DATABASE}|" .env && \
    sed -i "s|DB_USERNAME=.*|DB_USERNAME=${DB_USERNAME}|" .env && \
    sed -i "s|DB_PASSWORD=.*|DB_PASSWORD=${DB_PASSWORD}|" .env && \
    sed -i "s|APP_ENV=.*|APP_ENV=${APP_ENV}|" .env && \
    sed -i "s|APP_DEBUG=.*|APP_DEBUG=${APP_DEBUG}|" .env && \
    sed -i "s|APP_URL=.*|APP_URL=${APP_URL}|" .env

# Generate app key (after .env is created and vendor files exist)
RUN php artisan key:generate

# Install Node dependencies and build assets
RUN if [ -f package.json ]; then \
        npm install && \
        npm run production || npm run build; \
    fi

# Run migrations (with safety flag to avoid failures in production)
# Note: This will fail if database isn't available during build
# Consider removing this line or using --force flag
RUN php artisan migrate --force || true

# Cache configurations for production
RUN php artisan config:cache && \
    php artisan route:cache && \
    php artisan view:cache

# Configure Apache document root to Laravel's public folder
RUN sed -i 's!/var/www/html!/var/www/html/public!g' /etc/apache2/sites-available/000-default.conf

# Set permissions
RUN chown -R www-data:www-data /var/www/html && \
    chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Set ServerName to suppress warning
RUN echo "ServerName localhost" >> /etc/apache2/apache2.conf

EXPOSE 80

CMD ["apache2-foreground"]
