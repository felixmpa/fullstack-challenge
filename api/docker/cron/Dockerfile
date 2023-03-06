FROM php:8.1-apache

RUN apt-get update

RUN apt-get install cron -y \
    pkg-config  \
    libzip-dev

COPY ../crontab /etc/cron.d/crontab

RUN chmod 0644 /etc/cron.d/crontab

RUN crontab /etc/cron.d/crontab

RUN touch /var/log/cron.log

CMD cron && tail -f /var/log/cron.log
