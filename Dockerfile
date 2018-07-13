FROM alpine:3.8

LABEL maintainer="pad" \
      version="$CI_COMMIT_REF_NAME"

RUN apk --update --no-cache add \
        ca-certificates \
        curl \
        nginx \
        php7 \
        php7-fpm \
        php7-pdo_mysql \
        php7-mysqli \
        php7-curl \
        php7-gd \
        php7-json \
        php7-oauth \
        php7-session \
        php7-ctype \
        php7-exif \
        php7-mcrypt \
        php7-apcu \
        php7-imagick \
        s6

COPY src/configs/docker/ /
COPY src/               /var/www/src

RUN mkdir -p /var/www/src/userdata \
 && mkdir -p /var/www/src/html/photos \
 && mkdir -p /var/www/src/html/assets/cache \
 && chown nginx /var/www/src/userdata \
                /var/www/src/html/photos \
                /var/www/src/html/assets/cache

RUN ln -sf /dev/stderr /var/log/fpm-php.www.log \
 && ln -sf /dev/stdout /var/log/nginx/access.log \
 && ln -sf /dev/stderr /var/log/nginx/error.log

VOLUME /var/www/src/html/photos
EXPOSE 80

ENTRYPOINT ["/bin/s6-svscan", "/etc/services.d"]
HEALTHCHECK CMD curl --fail http://localhost/ || exit 1
