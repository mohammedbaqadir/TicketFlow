FROM mcr.microsoft.com/devcontainers/php:0-8.2 as base

# Install system dependencies
RUN apt-get update && apt-get install -y \
    libzip-dev zip unzip git curl \
    libpng-dev libjpeg-dev libfreetype6-dev \
    libicu-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo_mysql gd zip intl exif bcmath opcache \
    && apt-get clean

# Install Node.js
RUN curl -fsSL https://deb.nodesource.com/setup_18.x | bash - \
    && apt-get install -y nodejs

# Install Composer globally
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

FROM base as dev

# Set working directory
WORKDIR /workspace

# Copy project files
COPY . .

# Set up Laravel environment
RUN cp .env.example .env \
    && sed -i 's/APP_NAME=Laravel/APP_NAME=TicketFlow/' .env \
    && sed -i 's/APP_ENV=local/APP_ENV=development/' .env \
    && sed -i 's/APP_DEBUG=true/APP_DEBUG=true/' .env \
    && sed -i 's/DB_HOST=127.0.0.1/DB_HOST=db/' .env \
    && sed -i 's/DB_DATABASE=laravel/DB_DATABASE=TicketFlow/' .env \
    && sed -i 's/DB_USERNAME=root/DB_USERNAME=user/' .env \
    && sed -i 's/DB_PASSWORD=/DB_PASSWORD=password/' .env

# Set permissions
RUN chown -R www-data:www-data /workspace \
    && chmod -R 755 /workspace/storage