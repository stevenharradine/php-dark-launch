<?php 
namespace Telus\Digital\Libraries\DarkLaunch\Implementations;

use Telus\Digital\Libraries\ConfigLoader\Interfaces\ConfigInterface;

class StagingConfigLoader implements ConfigInterface {

  public function __construct() {
    $this->settings = [
      "host" => "data-cache.1jit0g.ng.0001.use1.cache.amazonaws.com",
      "port" => '6379'
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