# Development Dockerfile – PHP 8.3, dev deps, intl for Filament
FROM php:8.3-fpm

# Install system dependencies (libicu-dev required for intl / Filament)
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    libicu-dev \
    unzip \
    nodejs \
    npm \
    default-mysql-client \
    && docker-php-ext-configure intl \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip opcache intl \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Redis extension
RUN pecl install redis && docker-php-ext-enable redis

# Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www

# Entrypoint installs deps on startup (with volume mount) then runs CMD
COPY docker/entrypoint.sh /usr/local/bin/entrypoint.sh
COPY docker/queue-entrypoint.sh /usr/local/bin/queue-entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh /usr/local/bin/queue-entrypoint.sh

# ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
# Deps (composer + npm) are installed at runtime by entrypoint when .:/var/www is mounted
# CMD ["php-fpm"]
