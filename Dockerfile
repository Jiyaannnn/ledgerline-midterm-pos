FROM php:8.3-apache

RUN apt-get update \
    && apt-get install -y --no-install-recommends ca-certificates libicu-dev libonig-dev libpng-dev libjpeg62-turbo-dev unzip \
    && docker-php-ext-configure gd --with-jpeg \
    && docker-php-ext-install intl mbstring mysqli gd \
    && a2enmod rewrite headers \
    && rm -rf /var/lib/apt/lists/*

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
WORKDIR /var/www/html
COPY composer.json composer.lock ./
RUN composer install --no-dev --no-interaction --no-scripts --no-progress --prefer-dist --optimize-autoloader
COPY . .
COPY docker/apache-site.conf.template /etc/apache2/sites-available/000-default.conf.template
COPY docker/render-start.sh /usr/local/bin/render-start
RUN chmod +x /usr/local/bin/render-start \
    && mkdir -p writable/cache writable/logs writable/session writable/uploads public/uploads/avatars public/uploads/products \
    && chown -R www-data:www-data writable public/uploads \
    && chmod -R 775 writable public/uploads \
    && cp "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini" \
    && printf '\ndisplay_errors=Off\nlog_errors=On\nexpose_php=Off\n' > "$PHP_INI_DIR/conf.d/ledgerline-production.ini"

ENV PORT=10000
EXPOSE 10000
STOPSIGNAL SIGWINCH
ENTRYPOINT ["/usr/local/bin/render-start"]
CMD ["apache2-foreground"]
