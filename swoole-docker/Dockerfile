FROM php:8.5-cli

RUN apt-get update && apt-get install -y \
    libpq-dev \
    libbrotli-dev \
    git \
    && docker-php-ext-install pdo_mysql sockets

# 从 GitHub 克隆 Swoole 源码并编译安装（最新 master 分支，支持 PHP 8.5）
RUN git clone https://github.com/swoole/swoole-src.git /tmp/swoole \
    && cd /tmp/swoole \
    && phpize \
    && ./configure --enable-brotli --enable-sockets --enable-openssl \
    && make -j$(nproc) \
    && make install \
    && docker-php-ext-enable swoole

# JIT 配置
RUN echo "opcache.enable=1" >> /usr/local/etc/php/conf.d/docker-php-ext-opcache.ini \
    && echo "opcache.enable_cli=1" >> /usr/local/etc/php/conf.d/docker-php-ext-opcache.ini \
    && echo "opcache.validate_timestamps=0" >> /usr/local/etc/php/conf.d/docker-php-ext-opcache.ini \
    && echo "opcache.memory_consumption=512" >> /usr/local/etc/php/conf.d/docker-php-ext-opcache.ini \
    && echo "opcache.jit=tracing" >> /usr/local/etc/php/conf.d/docker-php-ext-opcache.ini \
    && echo "opcache.jit_buffer_size=128M" >> /usr/local/etc/php/conf.d/docker-php-ext-opcache.ini

WORKDIR /app
COPY swoole_server.php /app/swoole_server.php

EXPOSE 9501
CMD ["php", "swoole_server.php"]
