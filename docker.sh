#!/bin/sh

export TERM=xterm
RUN echo "Host github.com\n\tStrictHostKeyChecking no\n" >> /home/app/.ssh/config
service redis-server start
su app
cd /home/app/code
composer install
while true; do sleep 10; done