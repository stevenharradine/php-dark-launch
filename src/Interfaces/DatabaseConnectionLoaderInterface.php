<?php
namespace Telus\Digital\Libraries\DarkLaunch\Interfaces;

interface DatabaseConnectionLoaderInterface {

  /**
   * Returns a Redis connection
   * @param string $host The redis host
   * @param string $port The the port on which to connect with redis
   * @return PHP Redis connection, throws exception otherwise
   */
  public static function getRedisConnection($host, $port);

  /**
   * Get a mysql connection
   * @param string $host The mysql host
   * @param string $port The mysql port
   * @param string $database The mysql database
   * @param string $username The mysql username
   * @param string $password The mysql password
   * @return
   */
  public static function getMySqlConnection($host, $port, $database, $username, $password);
}