<?php
namespace Telus\Digital\Libraries\DarkLaunch\Interfaces;

interface RedisConnectionLoaderInterface {

  /**
   * Returns a Redis connection
   * @param string $host The redis host
   * @param string $port The the port on which to connect with redis
   * @return PHP Redis connection, throws exception otherwise
   */
  public static function getRedisConnection($host, $port);
}