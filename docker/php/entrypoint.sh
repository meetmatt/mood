#!/bin/sh

envsubst < /etc/ssmtp/revaliases.tmpl > /etc/ssmtp/revaliases
envsubst < /etc/ssmtp/ssmtp.conf.tmpl > /etc/ssmtp/ssmtp.conf

php-fpm