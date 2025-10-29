FROM alpine:3.8

RUN apk add --no-cache \
    apache2 \
    php5-apache2 \
    php5 \
    php5-cli \
    php5-json \
    php5-pdo \
    php5-pdo_mysql \
    php5-mysqli \
    php5-gd \
    php5-phar \
    php5-openssl \
    php5-mysql \
    php5-iconv \
    php5-calendar \
    php5-bcmath \
    curl \
    git \
    && ln -s /usr/bin/php5 /usr/bin/php

# Настройка поддержки коротких тегов в PHP
RUN sed -i 's#short_open_tag = Off#short_open_tag = On#g' /etc/php5/php.ini

ENV APACHE_DOCUMENT_ROOT=/var/www/html
RUN sed -i 's/#LoadModule rewrite_module/LoadModule rewrite_module/' /etc/apache2/httpd.conf && \
    sed -i 's/#LoadModule deflate_module/LoadModule deflate_module/' /etc/apache2/httpd.conf && \
    sed -i 's#^DocumentRoot ".*#DocumentRoot "${APACHE_DOCUMENT_ROOT}"#g' /etc/apache2/httpd.conf && \
    sed -i 's#AllowOverride none#AllowOverride All#g' /etc/apache2/httpd.conf && \
    sed -i 's#AllowOverride None#AllowOverride All#g' /etc/apache2/httpd.conf && \
    sed -i 's#Directory "/var/www/localhost/htdocs"#Directory "${APACHE_DOCUMENT_ROOT}"#g' /etc/apache2/httpd.conf && \
    sed -i 's#DirectoryIndex index.html#DirectoryIndex index.php index.html#g' /etc/apache2/httpd.conf && \
    mkdir -p /run/apache2

RUN curl -sS https://getcomposer.org/installer | php5 -- --install-dir=/usr/local/bin --filename=composer

WORKDIR /var/www/html

EXPOSE 80

CMD ["httpd", "-D", "FOREGROUND"]

