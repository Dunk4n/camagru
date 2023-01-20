FROM php:7.4-fpm

RUN apt-get update && apt-get install -y msmtp
COPY msmtprc /etc/msmtprc
COPY msmtprc /root/.msmtprc
ARG EMAIL_PASSWORD
ARG EMAIL_USER
ARG EMAIL_PROVIDER
RUN sed -i "s/PASSWORD/$EMAIL_PASSWORD/g" /etc/msmtprc /root/.msmtprc
RUN sed -i "s/USER/$EMAIL_USER/g" /etc/msmtprc /root/.msmtprc
RUN sed -i "s/PROVIDER/$EMAIL_PROVIDER/g" /etc/msmtprc /root/.msmtprc
RUN chmod 600 /etc/msmtprc && chmod 600 /root/.msmtprc
RUN chown www-data:www-data /etc/msmtprc &&  touch /var/log/msmtp.log && chown www-data:www-data /var/log/msmtp.log
RUN echo 'sendmail_path = "/usr/bin/msmtp -C /etc/msmtprc --logfile /var/log/msmtp.log -a gmail -t"' >> /usr/local/etc/php/php.ini

RUN docker-php-ext-install mysqli pdo pdo_mysql && docker-php-ext-enable pdo_mysql

RUN apt-get install -y bash

RUN echo "localhost localhost.localdomain" >> /etc/hosts

RUN sed -i '/#!\/bin\/sh/aservice msmtp restart' /usr/local/bin/docker-php-entrypoint
RUN sed -i '/#!\/bin\/sh/aecho "$(hostname -i)\t$(hostname) $(hostname).localhost" >> /etc/hosts' /usr/local/bin/docker-php-entrypoint

RUN chmod 777 /tmp
RUN chmod 777 /var/www/html/images

RUN rm -rf /var/lib/apt/lists/*
