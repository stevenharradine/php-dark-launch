<?php 
namespace Telus\Digital\Libraries\DarkLaunch\Implementations;

use Telus\Digital\Libraries\ConfigLoader\Interfaces\ConfigInterface;

class StagingConfigLoader implements ConfigInterface {

  public function __construct() {
    $this->settings = [
      "redis" => [
        "Host" => "data-cache.1jit0g.ng.0001.use1.cache.amazonaws.com",
        "Port" => '6379'
      ],
      "mysql" => [
        "Host" => ""
        "Port" => ""
        "userName" => ""
        "password" => ""
        "database" => ""
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