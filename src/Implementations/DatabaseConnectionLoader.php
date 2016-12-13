<?php 
namespace Telus\Digital\Libraries\DarkLaunch\Implementations;

use Telus\Digital\Libraries\DarkLaunch\Interfaces\DatabaseConnectionLoaderInterface;
use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Database\Eloquent\Model as EloquentModel;
use Illuminate\Database\Schema\Blueprint as Blueprint;

class DatabaseConnectionLoader implements DatabaseConnectionLoaderInterface {

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

  public static function getMySqlConnection($host, $port, $database, $username, $password, $pathToUnixSocket=false) {
    $capsule = new Capsule;
    $config = [
      'driver'    => 'mysql',
      'host'      => $host,
      'port'      => $port,
      'database'  => $database,
      'username'  => $username,
      'password'  => $password,
      'charset'   => 'utf8',
      'collation' => 'utf8_unicode_ci',
      'prefix'    => '',
    ];

    if(!empty($pathToUnixSocket)) {
      $config["unix_socket"] = $pathToUnixSocket;
    }
    $capsule->addConnection($config, 'dark-launch');

    $capsule->setAsGlobal();
    $capsule->bootEloquent();

    return $capsule;
  }

}