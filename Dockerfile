FROM php:7.3.1-fpm

RUN apt-get update && \
    apt-get install -y ssmtp gettext && \
    apt-get clean && \
    echo "sendmail_path = /usr/sbin/ssmtp -t" > /usr/local/etc/php/conf.d/mail.ini
ADD docker/php/ssmtp/revaliases.tmpl /etc/ssmtp/revaliases.tmpl
ADD docker/php/ssmtp/ssmtp.conf.tmpl /etc/ssmtp/ssmtp.conf.tmpl
RUN envsubst < /etc/ssmtp/revaliases.tmpl > /etc/ssmtp/revaliases
RUN envsubst < /etc/ssmtp/ssmtp.conf.tmpl > /etc/ssmtp/ssmtp.conf
RUN apt-get remove -y gettext && \
    apt-get clean

RUN apt-get update && \
    apt-get install -y wget zip unzip zlib1g-dev libzip-dev && \
    docker-php-ext-install zip

RUN wget -q -O /usr/local/bin/composer https://getcomposer.org/download/1.8.4/composer.phar && \
    chmod +x /usr/local/bin/composer && \
    apt-get remove -y wget zip zlib1g-dev libzip-dev && \
    apt-get clean

RUN docker-php-ext-install sockets && \
    docker-php-ext-install pdo_mysql

ADD composer.json /app/composer.json
ADD composer.lock /app/composer.lock
RUN composer install -d /app --prefer-dist --no-scripts --no-dev && \
    rm -rf /app/composer.lock /app/composer.json /usr/local/bin/composer /root/.composer

ADD app /app/app
ADD bin /app/bin
ADD src /app/src
ADD template /app/template
ADD web /app/web

WORKDIR /app

CMD ["php-fpm"]