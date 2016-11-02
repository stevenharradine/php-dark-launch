<?php 
namespace Telus\Digital\Libraries\DarkLaunch\Implementations;

use Telus\Digital\Libraries\ConfigLoader\Interfaces\ConfigInterface;

class ApplicationConfig implements ConfigInterface {

  public function __construct() {
    $this->settings = [
      "local-development" => [
        "redis" => [
          "host" => "localhost",
          "port" => '6379'
        ],
        "mysql" => [
          "host" => "localhost",
          "port" => "3306",
          "userName" => "root",
          "password" => "root",
          "database" => "dark_launch",
          "unix_socket" => "/var/run/mysqld/mysqld.sock"
        ]
      ],
      "integrated-development" => [
        "redis" => [
          "host" => "localhost",
          "port" => '6379'
        ],
        "mysql" => [
          "host" => "localhost",
          "port" => "3306",
          "userName" => "root",
          "password" => "",
          "database" => "dark_launch"
        ]
      ],
      "staging" => [
        "redis" => [
          "host" => "data-cache.1jit0g.ng.0001.use1.cache.amazonaws.com",
          "port" => '6379'
        ],
        "mysql" => [
          "host" => "homesolutions-orders.chswslsbflqt.us-west-2.rds.amazonaws.com",
          "port" => "3306",
          "userName" => "commerce",
          "password" => "notwebchannel",
          "database" => "dark_launch"
        ]
      ],
      "production" => [
        "redis" => [
          "host" => "homesolutions-orders.chswslsbflqt.us-west-2.rds.amazonaws.com",
          "port" => '6379'
        ],
        "mysql" => [
          "host" => "homesolutions-orders.c8ukeyti5zy6.us-east-1.rds.amazonaws.com",
          "port" => "3306",
          "userName" => "commerce",
          "password" => "notwebchannel",
          "database" => "dark_launch"
        ]
      ]
    ];
  }

  public function getValue($key) {
    if(!isset($this->settings[$key])) {
      return null;
    }
    return $this->settings[$key];
  }

  public function setValue($key, $value) {
    $this->settings[$key] = $value;
    return $this;
  }

}