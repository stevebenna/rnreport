# Dockerfile for rnreport (CodeIgniter 4)
# Uses php-fpm + composer and prepares the workspace for nginx.

# Build stage: install PHP dependencies with composer
FROM composer:2 AS vendor
WORKDIR /app

ARG COMPOSER_NO_DEV=false
ARG COMPOSER_FLAGS="--no-interaction --prefer-dist --optimize-autoloader"

# Copy composer files separately for better caching
COPY composer.json composer.lock ./

# Ensure required PHP extensions are available for Composer dependency resolution
RUN set -e; \
    apk add --no-cache icu-dev; \
    docker-php-ext-install intl

# Install PHP dependencies (including dev for local development)
RUN set -e; \
    composer install $COMPOSER_FLAGS $( [ "$COMPOSER_NO_DEV" = "true" ] && echo "--no-dev" )

# Runtime stage
FROM php:8.2-fpm

# Install system dependencies needed for common PHP extensions
RUN apt-get update \
    && apt-get install -y --no-install-recommends \
        libzip-dev \
        unzip \
        libonig-dev \
        libicu-dev \
        libpng-dev \
        libjpeg-dev \
        libfreetype6-dev \
        git \
        ca-certificates \
    && docker-php-ext-install -j$(nproc) \
        intl \
        pdo_mysql \
        mysqli \
        mbstring \
        zip \
        pcntl \
        bcmath \
    && docker-php-ext-configure gd --with-jpeg --with-freetype \
    && docker-php-ext-install -j$(nproc) gd \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Copy php-fpm configuration (optional)
# COPY ./docker/php-fpm.conf /usr/local/etc/php-fpm.d/zz-docker.conf

# Copy project
WORKDIR /var/www/html
COPY . /var/www/html

# Copy composer binary from vendor stage (to run composer at runtime if needed)
COPY --from=vendor /usr/bin/composer /usr/bin/composer

# Copy vendor dependencies from the vendor stage into the final image
COPY --from=vendor /app/vendor /var/www/html/vendor

# Ensure writable dirs exist
RUN mkdir -p writable && chown -R www-data:www-data /var/www/html/writable

# Entrypoint script ensures dependencies are installed and permissions are correct
COPY docker-entrypoint.sh /usr/local/bin/docker-entrypoint.sh
RUN chmod +x /usr/local/bin/docker-entrypoint.sh

EXPOSE 9000
ENTRYPOINT ["/usr/local/bin/docker-entrypoint.sh"]
CMD ["php-fpm"]
