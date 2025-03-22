FROM php:8.2-cli

# Install required PHP extensions
RUN apt-get update && apt-get install -y \
    git unzip zip curl libzip-dev libonig-dev libxml2-dev libpq-dev libcurl4-openssl-dev \
    libpng-dev libjpeg-dev libfreetype6-dev libicu-dev \
    && docker-php-ext-install pdo pdo_mysql zip intl gd

# Install Composer globally
COPY --from=composer:2.5 /usr/bin/composer /usr/bin/composer

# Symfony CLI for convenience (optional)
RUN curl -sS https://get.symfony.com/cli/installer | bash && \
    mv /root/.symfony*/bin/symfony /usr/local/bin/symfony

WORKDIR /var/www/html
EXPOSE 8000
CMD ["php", "-S", "0.0.0.0:8000", "-t", "public"]
