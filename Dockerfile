FROM php:8.4-apache

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    libzip-dev \
    default-mysql-client \
    mariadb-client \
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

# Install PHP dependencies
RUN composer install --no-interaction --optimize-autoloader --no-dev

# Configure Apache document root to Laravel's public folder
RUN sed -i 's!/var/www/html!/var/www/html/public!g' /etc/apache2/sites-available/000-default.conf

# Set permissions
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
RUN chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Set ServerName to suppress warning
RUN echo "ServerName localhost" >> /etc/apache2/apache2.conf

# Create startup script
RUN echo '#!/bin/bash\n\
\n\
# Wait for database to be ready (optional, uncomment if needed)\n\
# echo "Waiting for database connection..."\n\
# while ! mysqladmin ping -h"${DB_HOST}" --silent; do\n\
#     sleep 1\n\
# done\n\
\n\
# Check if .env file exists, if not create from example\n\
if [ ! -f .env ] && [ -f .env.example ]; then\n\
    cp .env.example .env\n\
    echo "Created .env file from .env.example"\n\
fi\n\
\n\
# Generate application key if not already set\n\
if ! grep -q "APP_KEY=" .env || grep -q "APP_KEY=$" .env || grep -q "APP_KEY= " .env; then\n\
    php artisan key:generate\n\
    echo "Generated application key"\n\
fi\n\
\n\
# Run database migrations and seeders\n\
if [ -n "$DB_HOST" ] && [ -n "$DB_DATABASE" ]; then\n\
    echo "Running database migrations..."\n\
    php artisan migrate --force\n\
    \n\
    # Run seeders if SEED_DB is set to true\n\
    if [ "$SEED_DB" = "true" ]; then\n\
        echo "Running database seeders..."\n\
        php artisan db:seed --force\n\
    fi\n\
    \n\
    # Run additional specific seeders if defined\n\
    if [ -n "$RUN_SEEDERS" ]; then\n\
        IFS=',' read -ra SEEDER_LIST <<< "$RUN_SEEDERS"\n\
        for seeder in "${SEEDER_LIST[@]}"; do\n\
            echo "Running seeder: $seeder"\n\
            php artisan db:seed --class="$seeder" --force\n\
        done\n\
    fi\n\
fi\n\
\n\
# Clear and cache configurations\n\
php artisan config:clear\n\
php artisan cache:clear\n\
php artisan view:clear\n\
\n\
# OPTIONAL: Cache for production (uncomment for better performance)\n\
# php artisan config:cache\n\
# php artisan route:cache\n\
# php artisan view:cache\n\
\n\
# Start Apache\n\
apache2-foreground\n\
' > /usr/local/bin/docker-entrypoint.sh

RUN chmod +x /usr/local/bin/docker-entrypoint.sh

EXPOSE 80

ENTRYPOINT ["/usr/local/bin/docker-entrypoint.sh"]
