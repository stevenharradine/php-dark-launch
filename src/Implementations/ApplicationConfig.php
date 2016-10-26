<?php 
namespace Telus\Digital\Libraries\DarkLaunch\Implementations;

use Telus\Digital\Libraries\ConfigLoader\Interfaces\ConfigInterface;

class ApplicationConfig implements ConfigInterface {

  public function __construct() {
    $this->settings = [
      "development" => [
        "redis" => [
          "host" => "localhost",
          "port" => '6379'
        ],
        "mysql" => [
          "host" => "",
          "port" => "",
          "userName" => "",
          "password" => "",
          "database" => "",
        ]
      ],
      "staging" => [
        "redis" => [
          "host" => "data-cache.1jit0g.ng.0001.use1.cache.amazonaws.com",
          "port" => '6379'
        ],
        "mysql" => [
          "host" => "",
          "port" => "",
          "userName" => "",
          "password" => "",
          "database" => "",
        ]
      ],
      "production" => [
        "redis" => [
          "host" => "data-cache.1jit0g.ng.0001.use1.cache.amazonaws.com",
          "port" => '6379'
        ],
        "mysql" => [
          "host" => "",
          "port" => "",
          "userName" => "",
          "password" => "",
          "database" => "",
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