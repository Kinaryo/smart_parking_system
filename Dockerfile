FROM php:8.4-cli

ARG VITE_PUSHER_APP_KEY
ARG VITE_PUSHER_HOST
ARG VITE_PUSHER_PORT
ARG VITE_PUSHER_SCHEME
ARG VITE_PUSHER_APP_CLUSTER

ENV VITE_PUSHER_APP_KEY=${VITE_PUSHER_APP_KEY}
ENV VITE_PUSHER_HOST=${VITE_PUSHER_HOST}
ENV VITE_PUSHER_PORT=${VITE_PUSHER_PORT}
ENV VITE_PUSHER_SCHEME=${VITE_PUSHER_SCHEME}
ENV VITE_PUSHER_APP_CLUSTER=${VITE_PUSHER_APP_CLUSTER}

RUN apt-get update && apt-get install -y \
    git curl libpng-dev libonig-dev libxml2-dev libzip-dev zip unzip \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

RUN curl -fsSL https://deb.nodesource.com/setup_22.x | bash - \
    && apt-get install -y nodejs

RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www

COPY . .

RUN composer install --no-dev --optimize-autoloader --no-interaction

RUN npm install && npm run build

RUN mkdir -p storage/framework/sessions storage/framework/views storage/framework/cache
RUN chown -R www-data:www-data storage bootstrap/cache
RUN chmod -R 775 storage bootstrap/cache

EXPOSE 8080

CMD ["sh", "-c", "php artisan config:cache && php artisan route:cache && php artisan serve --host=0.0.0.0 --port=${PORT:-8080}"]