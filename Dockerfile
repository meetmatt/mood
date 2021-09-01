FROM php:7-fpm-alpine

# prepare apk
RUN rm -rf /var/cache/apk/* \
 && rm -rf /tmp/*

# install composer
RUN apk update \
 && apk add --update --no-cache --virtual .composer-deps \
    wget \
    unzip \
    zlib-dev \
    libzip-dev \
 && docker-php-ext-configure zip --with-libzip \
 && docker-php-ext-install zip \
 && wget -q -O /usr/local/bin/composer https://getcomposer.org/download/1.8.4/composer.phar \
 && chmod +x /usr/local/bin/composer \
 && COMPOSER_ALLOW_SUPERUSER=1 \
    composer global require hirak/prestissimo

# install ssmtp and envsubst
RUN apk update \
 && apk add --update --no-cache ssmtp gettext \
 && echo "sendmail_path = /usr/sbin/ssmtp -t" > /usr/local/etc/php/conf.d/mail.ini

# copy ssmtp configs
ADD docker/php/ssmtp/revaliases.tmpl /etc/ssmtp/revaliases.tmpl
ADD docker/php/ssmtp/ssmtp.conf.tmpl /etc/ssmtp/ssmtp.conf.tmpl

# copy entrypoint
ADD docker/php/entrypoint.sh /entrypoint.sh

# install php extensions
RUN docker-php-ext-install pdo_mysql \
 && docker-php-ext-install sockets

# copy composer dependencies
ADD composer.json /app/composer.json
ADD composer.lock /app/composer.lock

# install composer dependencies
RUN COMPOSER_ALLOW_SUPERUSER=1 \
    composer install -d /app --prefer-dist --no-scripts --no-dev --no-ansi --no-interaction --no-autoloader

# copy application source code
ADD app /app/app
ADD bin /app/bin
ADD src /app/src
ADD template /app/template
ADD web /app/web

# dump autoloader and cleanup
RUN composer dump-autoload -d /app --no-scripts --no-dev --no-ansi --no-interaction --optimize \
  && apk del .composer-deps \
  && rm -rf \
     /var/cache/apk/* \
     /tmp/* \
     /root/.composer \
     /app/composer.lock \
     /app/composer.json \
     /usr/local/bin/composer

WORKDIR /app

CMD ["/entrypoint.sh"]