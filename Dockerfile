FROM richarvey/nginx-php-fpm:latest

# Configuración de entorno para Render
ENV WEBROOT=/var/www/html/public
ENV PHP_ERRORS_STDERR=1
ENV RUN_SCRIPTS=1
ENV REAL_IP_HEADER=1
ENV COMPOSER_ALLOW_SUPERUSER=1

# Remover prestissimo si existe (incompatible con PHP 8.2 y Composer 2)
RUN composer global remove hirak/prestissimo --no-interaction 2>/dev/null || true

# Copiamos el código de la aplicación
COPY . /var/www/html

# Copiar configuración de nginx personalizada
COPY nginx-default.conf /etc/nginx/sites-available/default.conf

# Establecer directorio de trabajo
WORKDIR /var/www/html

# Instalamos dependencias de PHP (sin dev)
RUN composer install --no-dev --optimize-autoloader --no-interaction

# Crear directorios necesarios si no existen
RUN mkdir -p storage/framework/{sessions,views,cache} \
    && mkdir -p storage/logs \
    && mkdir -p bootstrap/cache

# Permisos correctos para Laravel
RUN chown -R nginx:nginx /var/www/html \
    && chmod -R 755 /var/www/html \
    && chmod -R 775 storage bootstrap/cache

# Generar cache de configuración (opcional, si APP_KEY está disponible)
RUN php artisan config:clear || true \
    && php artisan view:clear || true \
    && php artisan route:clear || true

# Exponer puerto para Render (usa el puerto por defecto de la imagen)
EXPOSE 80

# Comando de inicio
CMD ["/start.sh"]