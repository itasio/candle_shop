FROM php:8.2-apache
RUN docker-php-ext-install mysqli && docker-php-ext-enable mysqli
RUN apt-get update && apt-get install -y \
    zlib1g-dev \
    libzip-dev \
    unzip
RUN docker-php-ext-install zip

RUN apt-get install -y autoconf pkg-config libssl-dev
RUN pecl install mongodb
RUN echo "extension=mongodb.so" >> /usr/local/etc/php/conf.d/mongodb.ini

COPY --from=composer/composer:2-bin /composer /usr/bin/composer
ENV COMPOSER_ALLOW_SUPERUSER=1
RUN composer require mongodb/mongodb

RUN apt-get install -y librdkafka-dev
RUN pecl install rdkafka && docker-php-ext-enable rdkafka
RUN echo "extension=rdkafka.so" >> /usr/local/etc/php/php.ini-development

RUN apt-get install -y cron

# Copy your PHP script and crontab file
COPY ./myshop/test_cron.php /var/www/html/
COPY crontab /etc/cron.d/my-cronjob

RUN touch /var/www/html/test_cron.log
# Give execution rights to the cron job
RUN chmod 0644 /etc/cron.d/my-cronjob

# Apply cron job
#RUN crontab /etc/cron.d/my-cronjob

#CMD cron && tail -f /var/www/html/test_cron.log

COPY entrypoint.sh /usr/local/bin/
RUN chmod +x /usr/local/bin/entrypoint.sh

ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
CMD ["apache2-foreground"]