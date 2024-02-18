FROM php:8.1-cli

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer

# Install necessary packages
RUN apt-get update && apt-get install -y \
    zip \
    unzip \
    git \
    curl