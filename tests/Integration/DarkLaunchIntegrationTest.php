<?php
namespace Telus\Digital\LibrariesTests\DarkLaunch\Integration;

use Telus\Digital\LibrariesTests\DarkLaunch\BaseTest;
use Telus\Digital\Libraries\DarkLaunch\Implementations\DatabaseConnectionLoader;
use Telus\Digital\Libraries\DarkLaunch\Implementations\ApplicationConfig;
use Telus\Digital\Libraries\DarkLaunch\Implementations\DarkLaunchConfigAccessor;

class DarkLaunchIntegrationTest extends BaseTest {

  protected function getRedisConnection() {
    $applicationConfig = new ApplicationConfig();
    $developmentConfig = $applicationConfig->getValue("local-development");

    $redisHost = $developmentConfig['redis']['host'];
    $redisPort = $developmentConfig['redis']['port'];

    $redisConnection = DatabaseConnectionLoader::getRedisConnection($redisHost, $redisPort);
    return $redisConnection;
  }

  protected function getMySqlConnection() {
    $applicationConfig = new ApplicationConfig();
    $developmentConfig = $applicationConfig->getValue("local-development");

    $host = $developmentConfig['mysql']['host'];
    $port = $developmentConfig['mysql']['port'];
    $username = $developmentConfig['mysql']['userName'];
    $password = $developmentConfig['mysql']['password'];
    $database = $developmentConfig['mysql']['database'];
    $pathToUnixSocker = $developmentConfig['mysql']['unix_socket'];

    $mysqlConnection = DatabaseConnectionLoader::getMySqlConnection($host, $port, $database, $username, $password, $pathToUnixSocker);
    return $mysqlConnection;
  }

  protected function setUp() {
    $this->redisConnection = $this->getRedisConnection();
    $this->mysqlConnection = $this->getMySqlConnection();
  }

  protected function tearDown() {
    $this->redisConnection->flushDb();
    $this->mysqlConnection->table('keys_to_values')->truncate();
  }

  public function testContructInstance() {
    $redisConnection = $this->getRedisConnection();
    $this->assertNotEmpty($redisConnection);
    $this->assertEquals($redisConnection->ping(), '+PONG');

    $applicationConfig = new ApplicationConfig();
    $developmentConfig = $applicationConfig->getValue("local-development");

    $mysqlConnection = $this->getMySqlConnection();

    $darkLaunchLibrary = new DarkLaunchConfigAccessor($this->redisConnection, $this->mysqlConnection);
  }

  public function testLazyLoadFeature() {
    $testValue = [
      'type' => 'string',
      'value' => 'asdf'
    ];
    $initialConfig = [
      'test' => $testValue  
    ];

    $darkLaunchLibrary = new DarkLaunchConfigAccessor($this->redisConnection, $this->mysqlConnection, $initialConfig);
    $result = $darkLaunchLibrary->featureEnabled('test');
    $expectedResult = 'asdf';
    $this->assertEquals($expectedResult, $result);
  }

  public function testEnableFeatureFunction() {
    $testValue = [
      'type' => 'string',
      'value' => 'asdf'
    ];
    $initialConfig = [];
    $darkLaunchLibrary = new DarkLaunchConfigAccessor($this->redisConnection, $this->mysqlConnection, $initialConfig, 'commerce', 'pkandathil');
    $darkLaunchLibrary->enableFeature('test', $testValue);
    $this->assertEquals($testValue, $darkLaunchLibrary->feature('test'));
  }

  public function testEnableFeatureFunctionDuplicate() {
    $testValue = [
        'type' => 'string',
        'value' => 'asdf'
    ];
    $testValue2 = [
        'type' => 'string',
        'value' => 'asdf2'
    ];
    $initialConfig = [];
    $darkLaunchLibrary = new DarkLaunchConfigAccessor($this->redisConnection, $this->mysqlConnection, $initialConfig, 'commerce', 'pkandathil');
    $darkLaunchLibrary->enableFeature('test', $testValue);
    $darkLaunchLibrary->enableFeature('test', $testValue2);
    $this->assertEquals($testValue2, $darkLaunchLibrary->feature('test'));
  }

  public function testEnableFeatureBadFeatureValue() {
    $testValue = null;
    $initialConfig = [];
    $darkLaunchLibrary = new DarkLaunchConfigAccessor($this->redisConnection, $this->mysqlConnection, $initialConfig, 'commerce', 'pkandathil');
    $this->expectException(\Exception::class);
    $darkLaunchLibrary->enableFeature('test', $testValue);
  }

  public function testDisableFeature() {
    $testValue = [
        'type' => 'string',
        'value' => 'asdf'
    ];
    $initialConfig = [];
    $darkLaunchLibrary = new DarkLaunchConfigAccessor($this->redisConnection, $this->mysqlConnection, $initialConfig, 'commerce', 'pkandathil');
    $darkLaunchLibrary->enableFeature('test', $testValue);
    $darkLaunchLibrary->disableFeature('test', $testValue);
    $this->assertEquals(False, $darkLaunchLibrary->feature('test'));
  }

  public function testEnableFeatureRaw() {
    $testValue = [
        'type' => 'string',
        'value' => 'asdf'
    ];
    $initialConfig = [];
    $darkLaunchLibrary = new DarkLaunchConfigAccessor($this->redisConnection, $this->mysqlConnection, $initialConfig, 'commerce', 'pkandathil');
    $darkLaunchLibrary->enableFeature('test', $testValue);
    $result = json_decode($this->mysqlConnection->table('keys_to_values')->get()->first()->value, true);
    $this->assertEquals($testValue['value'], $result['value']);
  }

  public function testDeleteValueFromRedisAndLoadFromDb() {
    $testValue = [
      'type' => 'string',
      'value' => 'asdf'
    ];
    $testValue2 = [
      'type' => 'string',
      'value' => 'qwerty'
    ];
    $initialConfig = [
      'test' => $testValue  
    ];

    $darkLaunchLibrary = new DarkLaunchConfigAccessor($this->redisConnection, $this->mysqlConnection, $initialConfig);
    $darkLaunchLibrary->enableFeature('test', $testValue);

    $key = "dark-launch:project:global:user:global:feature:test";
    $this->mysqlConnection->table("keys_to_values")->where(["key" => $key])->update(["value" => json_encode($testValue2)]);
    $this->redisConnection->del($key);

    $result = $darkLaunchLibrary->featureEnabled('test');
    $expectedResult = "qwerty";

    $this->assertEquals($expectedResult, $result);
  }

  public function testPercentageDarkLaunchIsOff() {
    $testValue = [
      'type' => 'percentage',
      'value' => 0
    ];
    $initialConfig = [];
    $darkLaunchLibrary = new DarkLaunchConfigAccessor($this->redisConnection, $this->mysqlConnection, $initialConfig, 'commerce', 'pkandathil');
    $darkLaunchLibrary->enableFeature('test', $testValue);
    for($i = 0; $i < 100; $i++) {
      $this->assertEquals(false, $darkLaunchLibrary->featureEnabled('test'));
    }
  }

  public function testPercentageDarkLaunchIsOn() {
    $testValue = [
      'type' => 'percentage',
      'value' => 100
    ];
    $initialConfig = [];
    $darkLaunchLibrary = new DarkLaunchConfigAccessor($this->redisConnection, $this->mysqlConnection, $initialConfig, 'commerce', 'pkandathil');
    $darkLaunchLibrary->enableFeature('test', $testValue);
    for($i = 0; $i < 100; $i++) {
      $this->assertEquals(true, $darkLaunchLibrary->featureEnabled('test'));
    }
  }

  public function testPercentageDarkLaunchIsOnAndOff() {
    $testValue = [
      'type' => 'percentage',
      'value' => 30
    ];
    $initialConfig = [];
    $darkLaunchLibrary = new DarkLaunchConfigAccessor($this->redisConnection, $this->mysqlConnection, $initialConfig, 'commerce', 'pkandathil');
    $darkLaunchLibrary->enableFeature('test', $testValue);
    $gotTrue = null;
    $gotFalse = null;
    for($i = 0; $i < 100; $i++) {
      $result = $darkLaunchLibrary->featureEnabled('test');
      if($result === true){
        $gotTrue = $result;
      }
      else {
        $gotFalse = $result;
      }
    }
    $this->assertEquals(true, $gotTrue);
    $this->assertEquals(false, $gotFalse);
  }

}