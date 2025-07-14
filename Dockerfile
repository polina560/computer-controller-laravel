# Stage 1: Node.js builder
FROM node:22-alpine as node-builder
WORKDIR /build
COPY package.json ./
RUN npm install
COPY resources ./resources
COPY eslint.config.js \
     tsconfig.json \
     vite.config.ts \
     ./
RUN npm run build

# Stage 2: Composer builder
FROM composer:2 as composer-builder
RUN composer config -g repos.packagist composer https://packagist.org.ru
WORKDIR /app
COPY composer.json composer.lock ./
RUN composer install --no-dev --ignore-platform-reqs --no-scripts

# Stage 3: Final image
FROM dunglas/frankenphp
WORKDIR /app
RUN install-php-extensions \
    pcntl \
    zip \
    pdo_mysql \
    mbstring \
    opcache \
    exif \
    fileinfo \
    ctype \
    xml \
    tokenizer

# Copy application files
COPY . .

# Copy PHP dependencies from composer-builder
COPY --from=composer-builder /app/vendor ./vendor

# Copy built assets from node-builder
COPY --from=node-builder /build/public/build ./public/build

# Optimize Laravel
RUN php artisan package:discover --ansi && \
    php artisan storage:link

ENTRYPOINT ["php", "artisan", "octane:frankenphp"]
