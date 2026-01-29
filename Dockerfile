FROM php:8.1-apache

# Instalar dependencias del sistema y extensiones PHP requeridas
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libzip-dev \
    libxml2-dev \
    libxslt-dev \
    libicu-dev \
    unzip \
    git \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) \
    gd \
    pdo_mysql \
    mysqli \
    soap \
    zip \
    xsl \
    intl \
    bcmath

# Habilitar mod_rewrite de Apache para rutas limpias
RUN a2enmod rewrite

# Configurar DocumentRoot a /var/www/html/public
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# Instalar Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Configurar ServerName para evitar advertencia
RUN echo "ServerName localhost" >> /etc/apache2/apache2.conf

# Copiar el código fuente de la aplicación
COPY . /var/www/html

# Directorio de trabajo
WORKDIR /var/www/html

# Ajustar permisos
# Aseguramos que los directorios existan y que www-data sea dueño
RUN mkdir -p /var/www/html/storage/certs \
    && mkdir -p /var/www/html/storage/xml \
    && mkdir -p /var/www/html/storage/logs \
    && chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html \
    && chmod -R 777 /var/www/html/storage
