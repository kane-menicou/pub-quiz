FROM php:8.3-fpm

ENV APP_ENV=prod

RUN apt-get update && apt-get install -y \
    nginx \
    unzip \
    curl \
    git \
    libpng-dev \
    libjpeg62-turbo-dev \
    libfreetype6-dev \
    libicu-dev \
    libzip-dev \
    && docker-php-ext-configure zip \
    && docker-php-ext-install zip pdo pdo_mysql

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

WORKDIR /app

COPY . /app

RUN composer install --no-dev --optimize-autoloader

RUN bin/console asset-map:compile

RUN chown -R www-data:www-data /app/var
RUN chmod -R 775 /app/var

COPY nginx.conf /etc/nginx/sites-enabled/default

EXPOSE 8080

CMD /app/bin/console doctrine:migrations:migrate --no-interaction && service nginx start && php-fpm
