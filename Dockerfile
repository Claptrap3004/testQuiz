# Basis-Image für Apache und PHP 8.2
FROM php:8.2-apache
ENV DEBIAN_FRONTEND=noninteractive
# Installation von notwendigen Paketen, PHP-Erweiterungen und Composer
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libonig-dev \
    libxml2-dev \
    default-mysql-client \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
        && docker-php-ext-install pdo pdo_mysql mysqli gd opcache \
        && apt-get clean \
        && rm -rf /var/lib/apt/lists/* \
    && curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

RUN echo "ServerName localhost" >> /etc/apache2/apache2.conf



# SSL-Zertifikat erstellen und kopieren
RUN openssl req -x509 -nodes -days 365 -newkey rsa:2048 \
    -keyout /etc/ssl/private/apache-selfsigned.key \
    -out /etc/ssl/certs/apache-selfsigned.crt \
    -subj "/C=DE/ST=BER/L=Berlin/O=MyCompany/OU=IT/CN=localhost" \
    -addext "subjectAltName=DNS:localhost" \
    -addext "basicConstraints=CA:FALSE"


COPY apache-site.conf /etc/apache2/sites-available/000-default.conf

RUN apt-get update && apt-get install -y ssl-cert

# Erstelle ein selbstsigniertes SSL-Zertifikat
RUN make-ssl-cert generate-default-snakeoil --force-overwrite
RUN a2enmod rewrite

# Konfiguration kopieren

# Aktivieren der Site
RUN a2ensite default-ssl

# SSL-Modul aktivieren
RUN a2enmod ssl


# Kopieren des aktuellen Verzeichnisses in den Container
COPY . /var/www/html
RUN curl -fsSL https://deb.nodesource.com/setup_lts.x | bash - \
    && apt-get install -y nodejs

# Installation von Twig über Composer
RUN composer install
#RUN npm install

RUN chmod -R 755 /var/www/html
RUN chown -R www-data:www-data /var/www/html

# Setzen des Arbeitsverzeichnisses
WORKDIR /var/www/html/public
# Exponieren der Ports
EXPOSE 80 443