# Use the official PHP 8.2 image
ARG VARIANT=8.2
FROM mcr.microsoft.com/devcontainers/php:0-${VARIANT}

# Install system dependencies
RUN apt-get update && apt-get install -y \
    libzip-dev zip unzip git curl \
    libpng-dev libjpeg-dev libfreetype6-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo_mysql gd zip \
    && apt-get clean

# Install Node.js (version specified in devcontainer.json)
ARG NODE_VERSION=18
RUN curl -fsSL https://deb.nodesource.com/setup_${NODE_VERSION}.x | bash - \
    && apt-get install -y nodejs

# Install Composer globally
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Set up the Meilisearch binary
RUN curl -L https://github.com/meilisearch/meilisearch/releases/download/v1.9.0/meilisearch-linux-amd64 -o \
    /usr/local/bin/meilisearch && \
    chmod +x /usr/local/bin/meilisearch

# Set working directory
WORKDIR /workspace

# Copy project files
COPY . .

# Expose the necessary ports
EXPOSE 8000 7700 3306