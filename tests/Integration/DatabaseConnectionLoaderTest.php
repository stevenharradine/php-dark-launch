<?php
namespace Telus\Digital\LibrariesTests\DarkLaunch\Integration;

use Telus\Digital\LibrariesTests\DarkLaunch\BaseTest;
use Telus\Digital\Libraries\DarkLaunch\Implementations\DatabaseConnectionLoader;
use Telus\Digital\Libraries\DarkLaunch\Implementations\ApplicationConfig;
use Illuminate\Database\Capsule\Manager as Capsule;

class DatabaseonnectionLoaderTest extends BaseTest {

  protected function setUp()
    {
        $this->stack = [];
    }

  public function testContructRedisInstance() {
    $applicationConfig = new ApplicationConfig();
    $developmentConfig = $applicationConfig->getValue("local-development");

    $redisHost = $developmentConfig['redis']['host'];
    $redisPort = $developmentConfig['redis']['port'];

    $redisConnection = DatabaseConnectionLoader::getRedisConnection($redisHost, $redisPort);
    $this->assertNotEmpty($redisConnection);
    $this->assertEquals($redisConnection->ping(), '+PONG');
  }

  public function testConstructDatabaseInstance() {
    $applicationConfig = new ApplicationConfig();
    $developmentConfig = $applicationConfig->getValue("local-development");

    $host = $developmentConfig['mysql']['host'];
    $port = $developmentConfig['mysql']['port'];
    $username = $developmentConfig['mysql']['userName'];
    $password = $developmentConfig['mysql']['password'];
    $database = $developmentConfig['mysql']['database'];

    $mysqlConnection = DatabaseConnectionLoader::getMySqlConnection($host, $port, $database, $username, $password);
    $result = $mysqlConnection->table('keys_to_values')->count();
    $this->assertGreaterThanOrEqual(0, $result, 'Zero or more values in table');
  }
}