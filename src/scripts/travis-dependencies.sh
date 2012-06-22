#!/bin/bash

sudo apt-get install -y build-essential libpcre3-dev php-pear php5-curl php5-imagick
sudo pecl install oauth
sudo pear channel-discover pear.bovigo.org
sudo pear install bovigo/vfsStream-beta
