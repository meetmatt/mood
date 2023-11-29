FROM php:7.4-fpm

RUN docker-php-ext-install pdo_mysql
RUN docker-php-ext-install sockets

RUN apt-get update && \
    apt-get install -y ssmtp gettext && \
    apt-get clean && \
    echo "sendmail_path = /usr/sbin/ssmtp -t" > /usr/local/etc/php/conf.d/mail.ini

COPY ssmtp/revaliases.tmpl /etc/ssmtp/revaliases.tmpl
COPY ssmtp/ssmtp.conf.tmpl /etc/ssmtp/ssmtp.conf.tmpl
COPY entrypoint.sh /entrypoint.sh

CMD ["/entrypoint.sh"]