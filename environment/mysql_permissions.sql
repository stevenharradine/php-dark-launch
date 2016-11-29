DELETE FROM mysql.user ;
CREATE USER 'root'@'%' IDENTIFIED BY '' ;
GRANT ALL ON *.* TO 'root'@'%' WITH GRANT OPTION ;
DROP DATABASE IF EXISTS test ;
FLUSH PRIVILEGES ;
grant all on *.* to 'root'@'%';
create database dark_launch;