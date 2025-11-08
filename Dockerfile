FROM php:8.2-cli

# install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    unzip \
    zip \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    libsodium-dev \
    libpq-dev \
    # REMOVIDO: default-mysql-client \
    # REMOVIDO: default-libmysqlclient-dev \
    libfreetype6-dev \
    libjpeg62-turbo-dev \
    # Adicionando o client do PostgreSQL (libpq-dev é suficiente, mas vamos manter o estilo)
    postgresql-client \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo_pgsql mbstring exif pcntl bcmath gd zip sodium
    # REMOVIDO: pdo_mysql

# Get Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# REMOVIDO: Seção "Install Node.js and npm"
# RUN curl -sl https://deb.nodesource.com/setup_18.x / bash && \
#    apt-get update && apty-get install -y nodejs

# Set working directory
WORKDIR /var/www/html

# Copy application files
COPY . .

# Expose port used by 'php artisan serve'
EXPOSE 8000

# Install PHOP dependencies
RUN composer install
# REMOVIDO: RUN np install

# Run Laravel migrations and start server
CMD php artisan migrate --force && php artisan serve --host=0.0.0.0 --port=8000