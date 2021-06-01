#!/bin/bash
set -eu

ln -fs /usr/share/zoneinfo/UTC /etc/localtime && dpkg-reconfigure -f noninteractive tzdata

chown -R www-data:www-data /var/www
chmod +x /var/www/ccbip/bin/ws-loop.sh
chmod +x /var/www/ccbip/bin/ws-block-loop.sh

#beanstalkd -l 0.0.0.0 -p 11300 &

#/usr/local/sbin/php-fpm --nodaemonize
/usr/bin/supervisord -c /etc/supervisor/supervisord.conf
