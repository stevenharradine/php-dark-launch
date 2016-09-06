FROM php:5.6-cli
RUN apt-get update && \
  apt-get install -y wget curl git && \
  curl -sS https://getcomposer.org/installer | php && mv composer.phar /usr/local/bin/composer && \
  apt-get install -y redis-server && \
  pecl install xdebug && docker-php-ext-enable xdebug && \
  pecl install -o -f redis \
  &&  rm -rf /tmp/pear \
  &&  echo "extension=redis.so" > /usr/local/etc/php/conf.d/redis.ini

ADD docker.sh /

RUN chmod 775 /docker.sh

RUN useradd --user-group --create-home app

ENV HOME=/home/app

RUN mkdir /home/app/code

RUN mkdir /home/app/.ssh
RUN chown -R app:app /home/app/.ssh
RUN chmod 700 /home/app/.ssh
RUN echo "Host github.com\n\tStrictHostKeyChecking no\n" >> /home/app/.ssh/config


CMD [ "./docker.sh" ]