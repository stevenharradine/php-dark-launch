<?php
namespace Telus\Digital\LibrariesTests\DarkLaunch;

use Telus\Digital\Libraries\DarkLaunch\Implementations\DatabaseConnectionLoader;
use Telus\Digital\Libraries\DarkLaunch\Implementations\ApplicationConfig;

class DatabaseonnectionLoaderTest extends BaseTest {

  public function testContructInstance() {
    $applicationConfig = new ApplicationConfig();
    $developmentConfig = $applicationConfig->getValue("development");

    $redisHost = $developmentConfig['redis']['host'];
    $redisPort = $developmentConfig['redis']['port'];

    $redisConnection = DatabaseConnectionLoader::getRedisConnection($redisHost, $redisPort);
    $this->assertNotEmpty($redisConnection);
    $this->assertEquals($redisConnection->ping(), '+PONG');
  }
}