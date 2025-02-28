FROM php:8.1-fpm

# Install dependencies for rdkafka
RUN apt-get update && apt-get install -y \
    librdkafka-dev \
    libssl-dev \
    && pecl install rdkafka \
    && docker-php-ext-enable rdkafka \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

WORKDIR /var/www/html/src
