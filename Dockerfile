# Verwende das offizielle WordPress-Image als Basis
FROM wordpress:php7.4-apache

# Setze Umgebungsvariablen für WordPress
ENV WORDPRESS_DB_HOST=${WORDPRESS_DB_HOST}
ENV WORDPRESS_DB_USER=${WORDPRESS_DB_USER}
ENV WORDPRESS_DB_PASSWORD=${WORDPRESS_DB_PASSWORD}
ENV WORDPRESS_DB_NAME=${WORDPRESS_DB_NAME}

# Kopiere den angepassten wp-content Ordner in das Image
COPY ./site/wp-content /var/www/html/wp-content

# Kopiere das Migrationsskript und mache es ausführbar
COPY ./init/migrate.sh /docker-entrypoint-initdb.d/migrate.sh
RUN chmod +x /docker-entrypoint-initdb.d/migrate.sh

# Setze die Berechtigungen für den wp-content Ordner
RUN chown -R www-data:www-data /var/www/html/wp-content

# Exponiere Port 80
EXPOSE 80

# Starte Apache
CMD ["apache2-foreground"]
