#!/bin/sh

export TERM=xterm
sudo service redis-server start
sudo service mysql start
sudo mysql -u root -proot< /mysql_permissions.sql
cd /home/app/code
composer install
while true; do sleep 10; done