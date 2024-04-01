FROM php:8.3.4-fpm-alpine3.19

RUN apk add --no-cache shadow $PHPIZE_DEPS \
    curl \
    gnupg \
    nano \
    bash \
    npm \
    curl-dev \
    php-intl \
    supervisor \
    libpng-dev \
    libzip-dev \
    icu-dev \
    oniguruma-dev \
    unixodbc-dev \
    freetype-dev \
    libjpeg-turbo-dev \
    util-linux

# Extensions
RUN docker-php-ext-install pdo pcntl pdo_mysql gd zip intl mbstring mysqli
RUN docker-php-ext-enable pdo_mysql intl mbstring

# Download swoole
RUN pecl install -D 'enable-sockets="no" enable-openssl="yes" enable-http2="yes" enable-mysqlnd="yes" enable-swoole-json="yes" enable-swoole-curl="yes"' swoole-5.1.1

# My ZSH
RUN sh -c "$(wget -O- https://github.com/deluan/zsh-in-docker/releases/download/v1.1.5/zsh-in-docker.sh)" -- -t robbyrussell

# Composer install
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Install redis
RUN pecl install -o -f redis && rm -rf /tmp/pear && docker-php-ext-enable redis

# Install swoole
RUN touch /usr/local/etc/php/conf.d/swoole.ini && echo 'extension=swoole.so' >/usr/local/etc/php/conf.d/swoole.ini

# Copy of supervisord
COPY ./docker/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# Defining work directory and copying project to container
WORKDIR /var/www

# Linking public folder to html
RUN rm -rf /var/www/html && ln -s public html

# Create supervisord log folder
RUN mkdir /var/log/supervisor

# Copying php.ini to container
RUN ln -s /usr/local/etc/php/php.ini /usr/local/etc/php/php.ini

# Copy the project to container
COPY . .

# Expose port and run entrypoint
EXPOSE 80

# Define the entrypoint to run
ENTRYPOINT [ "./docker/entrypoint.sh" ]
