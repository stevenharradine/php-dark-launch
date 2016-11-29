<?php
return [
  'paths' => [
    'migrations' => 'migrations'
  ],
  'migration_base_class' => '\Telus\Digital\Libraries\DarkLaunch\Implementations\Migration',
  'environments' => [
    'default_migration_table' => 'phinxlog',
    'default_database' => 'local-development',
    'local-development' => [
      'adapter' => 'mysql',
      'host' => "localhost",
      'name' => "dark_launch",
      'user' => "root",
      'pass' => "",
      'port' => "3306",
      "unix_socket" => "/var/run/mysqld/mysqld.sock"
    ],
    'integrated-development' => [
      'adapter' => 'mysql',
      "host" => "localhost",
      "port" => "3306",
      "user" => "root",
      "pass" => "",
      "name" => "dark_launch"
    ],
    'staging' => [
      'adapter' => 'mysql',
      "host" => "homesolutions-orders.chswslsbflqt.us-west-2.rds.amazonaws.com",
      "port" => "3306",
      "user" => "commerce",
      "pass" => "notwebchannel",
      "name" => "dark_launch"
    ],
    'production' => [
      'adapter' => 'mysql',
      "host" => "homesolutions-orders.c8ukeyti5zy6.us-east-1.rds.amazonaws.com",
      "port" => "3306",
      "user" => "commerce",
      "pass" => "notwebchannel",
      "name" => "dark_launch"
    ]
  ]
];