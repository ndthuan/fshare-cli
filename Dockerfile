FROM php:5.6-alpine

MAINTAINER thuan@nguyens.xyz

ADD https://github.com/kelseyhightower/confd/releases/download/v0.12.0-alpha3/confd-0.12.0-alpha3-linux-amd64 /usr/bin/confd
RUN chmod +x /usr/bin/confd

ADD . /fshare-cli
ADD ./confd /etc/confd

WORKDIR /fshare-cli

RUN ln -s /fshare-cli/bin/fshare /usr/bin/

RUN echo 'http://nl.alpinelinux.org/alpine/edge/testing' >> /etc/apk/repositories
RUN apk update
RUN apk add autoconf build-base gearman-dev
RUN pecl install gearman
RUN echo 'extension=gearman.so' > /usr/local/etc/php/conf.d/gearman.ini
RUN php composer.phar install --no-dev

CMD confd -onetime -backend env && fshare daemon
