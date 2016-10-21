<?php 
namespace Telus\Digital\Libraries\DarkLaunch\Implementations;

use Telus\Digital\Libraries\DarkLaunch\Interfaces\RedisConnectionLoaderInterface;

class RedisConnectionLoader implements RedisConnectionLoaderInterface {

  private function __construct(){}

  public static function getRedisConnection($host, $port) {
    $redis = new \Redis();
    ini_set("default_socket_timeout", 1);
    $result = $redis->pconnect($host, $port);
    if($result === false){
      throw \Exception('Unable to connect to Redis');
    }
    return $redis;
  }

}