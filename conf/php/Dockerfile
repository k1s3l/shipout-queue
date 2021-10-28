FROM php:8.0-fpm

ARG UID

RUN apt update && apt install -y libfreetype6-dev sendmail libpng-dev wget g++ git libzip-dev libjpeg-dev libjpeg62-turbo-dev libicu-dev libmagick++-dev gcc musl-dev make pear-channels supervisor cron libpq-dev ccze

RUN docker-php-ext-configure gd

RUN docker-php-ext-install \
        gd \
        pdo \
        pgsql \
        pdo_pgsql \
        tokenizer \
        opcache \
        exif \
        intl \
        zip

RUN pecl install imagick sync redis
RUN docker-php-ext-enable imagick sync redis

RUN docker-php-ext-install opcache

ADD php.ini /usr/local/etc/
ADD supervisord.conf /etc/supervisor/conf.d/supervisord.conf

RUN wget https://getcomposer.org/installer -O - -q | php -- --install-dir=/bin --filename=composer --quiet

RUN usermod -u ${UID} www-data && groupmod -g ${UID} www-data

USER ${UID}:${UID}