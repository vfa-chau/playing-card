FROM php:8.1-fpm

RUN usermod -u 1000 www-data

RUN apt-get update \
  && apt-get install -y \
    libmcrypt-dev \
    zlib1g-dev \
    default-mysql-client \
    netcat \
    locales \
    libxml2-dev \
    libzip-dev \
    zip \
    curl \
  && pecl install mcrypt && \
    docker-php-ext-enable mcrypt \
  && docker-php-ext-install -j$(nproc) \
    pdo_mysql \
    zip \
    soap \
  && curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
RUN pecl install xdebug \
  && rm -rf /tmp/pear \
  && echo "zend_extension=/usr/local/lib/php/extensions/no-debug-non-zts-20210902/xdebug.so" > /usr/local/etc/php/conf.d/xdebug.ini

RUN curl -sL https://deb.nodesource.com/setup_14.x | bash - \
  && apt-get install -y nodejs

RUN php -v
RUN composer --version
RUN node -v

RUN echo en_US.UTF-8 UTF-8 > /etc/locale.gen
RUN locale-gen en_US.UTF-8
RUN update-locale LANG=en_US.UTF-8
ENV LANG en_US.UTF-8
ENV LANGUAGE en_US:en
ENV LC_ALL en_US.UTF-8

WORKDIR /server
