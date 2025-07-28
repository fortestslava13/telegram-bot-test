FROM php:8.3-fpm

# Install system dependencies
RUN apt-get update && \
    apt-get install -y \
    build-essential \
    libxml2-dev \
    zlib1g-dev \
    libpng-dev \
    libjpeg-dev \
    libzip-dev \
    libgmp-dev \
    libmcrypt-dev \
    libssl-dev \
    libsqlite3-dev \
    libbz2-dev \
    libcurl4-openssl-dev \
    libxslt1-dev \
    libicu-dev \
    supervisor \
    && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN docker-php-ext-install mysqli pdo pdo_mysql
RUN docker-php-ext-install exif
RUN docker-php-ext-enable mysqli

COPY ./worker.conf /etc/supervisor/conf.d/

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Set working directory
WORKDIR /var/www/html

# Expose ports
EXPOSE 9000
