FROM php:8.5-cli

RUN apt-get update && apt-get install -y libpq-dev libzip-dev unzip \
    && docker-php-ext-install pdo_mysql sockets zip

RUN echo "opcache.enable=1" >> /usr/local/etc/php/conf.d/docker-php-ext-opcache.ini \
    && echo "opcache.enable_cli=1" >> /usr/local/etc/php/conf.d/docker-php-ext-opcache.ini \
    && echo "opcache.validate_timestamps=0" >> /usr/local/etc/php/conf.d/docker-php-ext-opcache.ini \
    && echo "opcache.memory_consumption=512" >> /usr/local/etc/php/conf.d/docker-php-ext-opcache.ini \
    && echo "opcache.jit=tracing" >> /usr/local/etc/php/conf.d/docker-php-ext-opcache.ini \
    && echo "opcache.jit_buffer_size=128M" >> /usr/local/etc/php/conf.d/docker-php-ext-opcache.ini

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /app
COPY . /app

# 关键：安装 spiral/roadrunner-http 包
RUN composer require spiral/roadrunner-cli spiral/roadrunner-http nyholm/psr7 -n --ignore-platform-req=ext-sockets \
    && ./vendor/bin/rr get-binary

EXPOSE 8081
CMD ["./rr", "serve", "-c", ".rr.yaml"]
