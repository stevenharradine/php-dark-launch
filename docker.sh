#!/bin/sh

export TERM=xterm
service redis-server start
cd /home/app/code
composer install
while true; do sleep 10; done