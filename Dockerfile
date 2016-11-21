FROM php:5.6-cli

ENV MYSQL_VERSION 5.5

RUN groupadd -r mysql && useradd -r -g mysql mysql

#COPY environment/my.cnf /etc/mysql/
RUN echo "mysql-server mysql-server/root_password password root" | debconf-set-selections
RUN echo "mysql-server mysql-server/root_password_again password root" | debconf-set-selections

RUN apt-get update && \
  apt-get install -yq mysql-server-${MYSQL_VERSION} pwgen zip unzip && \
  apt-get install -yf mysql-client-${MYSQL_VERSION} wget curl sudo git && \
  curl -sS https://getcomposer.org/installer | php && mv composer.phar /usr/local/bin/composer && \
  apt-get install -y redis-server && \
  pecl install xdebug && docker-php-ext-enable xdebug && \
  pecl install -o -f redis \
  &&  rm -rf /tmp/pear \
  && curl -L -o /tmp/redis.tar.gz https://github.com/phpredis/phpredis/archive/2.2.8.tar.gz \
  && tar xfz /tmp/redis.tar.gz \
  && rm -r /tmp/redis.tar.gz \
  && mkdir /usr/src/php \
  && mkdir /usr/src/php/ext \
  && mkdir /usr/src/php/redis \
  && mv phpredis-2.2.8 /usr/src/php/ext/redis \
  && docker-php-ext-install redis \
  && docker-php-ext-install pdo_mysql

COPY environment/mysql_permissions.sql /
COPY environment/docker.sh /
RUN chmod 775 /docker.sh
RUN chmod 775 /mysql_permissions.sql

RUN useradd --user-group --create-home app
RUN echo "app ALL=(ALL) NOPASSWD:ALL" >> /etc/sudoers
RUN passwd -d app
RUN adduser app sudo
ENV HOME=/home/app
RUN mkdir /home/app/code
RUN mkdir /home/app/.ssh
RUN chown -R app:app /home/app/.ssh
RUN chmod 700 /home/app/.ssh
USER app

CMD [ "./docker.sh" ]