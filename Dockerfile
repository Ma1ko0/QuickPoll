# QuickPoll — PHP + Apache image
FROM php:8.3-apache

# Install the SQLite PDO driver
RUN apt-get update \
    && apt-get install -y --no-install-recommends libsqlite3-dev \
    && docker-php-ext-install pdo pdo_sqlite \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Enable URL rewriting (used by public/.htaccess)
RUN a2enmod rewrite

# Serve the application from the public/ directory only
ENV APACHE_DOCUMENT_ROOT=/var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf \
    && sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# Allow the bundled .htaccess to take effect
COPY docker/apache-override.conf /etc/apache2/conf-available/quickpoll.conf
RUN a2enconf quickpoll

# Sensible production PHP settings
RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"

WORKDIR /var/www/html
COPY . /var/www/html

# The SQLite database lives in data/ and must be writable by the web server
RUN mkdir -p /var/www/html/data \
    && chown -R www-data:www-data /var/www/html/data

EXPOSE 80
