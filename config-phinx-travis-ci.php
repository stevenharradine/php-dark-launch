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
    ]
  ]
];