<?php
namespace Telus\Digital\LibrariesTests\DarkLaunch;

use Telus\Digital\LibrariesTests\DarkLaunch\BaseTest;
use Telus\Digital\Libraries\DarkLaunch\Implementations\DarkLaunchConfigAccessor;
use Telus\Digital\Libraries\ConfigLoader\Implementations\ConfigLoaderFactory;
use Telus\Digital\Libraries\ConfigLoader\Interfaces\ConfigInterface;
use Telus\Digital\Libraries\DarkLaunch\Implementations\StagingConfigLoader;
use Telus\Digital\Libraries\DarkLaunch\Implementations\RedisConnectionLoader;

class DarkLaunchTest extends BaseTest {

  public function testContructInstance() {
    $darkLaunchLibrary = new DarkLaunchConfigAccessor(new \Redis());
  }

  /**
   * @dataProvider darkLaunchValueDataProvider
   */
  public function testFeatures($darkLaunchValue, $expectedResult) {
    $stub = $this->createMock(\Redis::class);
    $stub->method('hgetall')
         ->willReturn($darkLaunchValue);

    $this->assertEquals($darkLaunchValue, $stub->hgetall());
    
    $darkLaunchLibrary = new DarkLaunchConfigAccessor($stub);
    $result = $darkLaunchLibrary->featureEnabled('test');
    $this->assertEquals($expectedResult, $result);
  }

  public function darkLaunchValueDataProvider() {
    return [
        [
          [
            'type' => 'boolean',
            'value' => TRUE], 
          TRUE
        ],
        [
          [
            'type' => 'boolean',
            'value' => FALSE
          ], 
          FALSE
        ],
        [
          [
            'type' => 'percentage',
            'value' => 100
          ],
          TRUE
        ],
        [
          [
            'type' => 'percentage',
            'value' => 0
          ],
          FALSE
        ],
        [
          [
            'type' => 'time',
            'start' => 0,
            'stop' => 1
          ],
          FALSE
        ],
        [
          [
            'type' => 'time',
            'start' => 0,
            'stop' => 4611265200
          ],
          TRUE
        ],
        [
          [
            'type' => 'int',
            'value' => 100
          ],
          100
        ],
        [
          [
            'type' => 'int',
            'value' => -1
          ],
          -1
        ],
        [
          [
            'type' => 'string',
            'value' => 'test'
          ],
          'test'
        ],
        [
          [
            'type' => 'time_string',
            'start' => 0,
            'stop' => 4611265200,
            'value' => 'asdf'
          ],
          'asdf'
        ]
    ];
  }

  public function testFeatureTrafficSource() {
    $stub = $this->createMock(\Redis::class);
    $darkLaunchValue = [
      'type' => 'traffic_source',
      'value' => 'external'
    ];
    $expectedResult = TRUE;
    $stub->method('hgetall')
         ->willReturn($darkLaunchValue);

    $_SERVER['is-external'] = true;
    $this->assertEquals($darkLaunchValue, $stub->hgetall());
    $darkLaunchLibrary = new DarkLaunchConfigAccessor($stub);
    $result = $darkLaunchLibrary->featureEnabled('test');
    $this->assertEquals($expectedResult, $result);

    $_SERVER['is-external'] = false;
    $expectedResult = FALSE;
    $result = $darkLaunchLibrary->featureEnabled('test');
    $this->assertEquals($expectedResult, $result);

    unset($_SERVER['is-external']);
    $expectedResult = FALSE;
    $result = $darkLaunchLibrary->featureEnabled('test');
    $this->assertEquals($expectedResult, $result);

    $darkLaunchValue = [
      'type' => 'traffic_source',
      'value' => 'internal'
    ];

    $expectedResult = FALSE;
    $stub = $this->createMock(\Redis::class);
    $stub->method('hgetall')
         ->willReturn($darkLaunchValue);
    $_SERVER['is-external'] = true;
    $this->assertEquals($darkLaunchValue, $stub->hgetall());
    $darkLaunchLibrary = new DarkLaunchConfigAccessor($stub);
    $result = $darkLaunchLibrary->featureEnabled('test');
    $this->assertEquals($expectedResult, $result);
  }

  public function testFeatureCookie() {
    $stub = $this->createMock(\Redis::class);
    $darkLaunchValue = [
      'type' => 'cookie',
      'value' => 'cookieName'
    ];
    $expectedResult = TRUE;
    $stub->method('hgetall')
         ->willReturn($darkLaunchValue);

    $_COOKIE['cookieName'] = 'true';
    $darkLaunchLibrary = new DarkLaunchConfigAccessor($stub);
    $result = $darkLaunchLibrary->featureEnabled('test');
    $this->assertEquals($expectedResult, $result);

    unset($_COOKIE['cookieName']);
    $expectedResult = FALSE;
    $result = $darkLaunchLibrary->featureEnabled('test');
    $this->assertEquals($expectedResult, $result);
  }

  public function testLazyLoadFeature() {
    $testValue = [
        'type' => 'string',
        'value' => 'asdf'
    ];
    $initialConfig = [
      'test' => $testValue  
    ];
    $stub = $this->createMock(\Redis::class);
    $stub->method('hgetall')
         ->will($this->onConsecutiveCalls(null, $testValue));

    $stub->method('multi')
         ->willReturn($stub);

    $darkLaunchLibrary = new DarkLaunchConfigAccessor($stub, $initialConfig);
    $result = $darkLaunchLibrary->featureEnabled('test');
    $expectedResult = 'asdf';
    $this->assertEquals($expectedResult, $result);
  }

  public function testProjectsFunction(){
    $stub = $this->createMock(\Redis::class);
    $stub->method('smembers')
         ->willReturn(['commerce']);
    $darkLaunchLibrary = new DarkLaunchConfigAccessor($stub, $initialConfig, 'commerce');
    $this->assertEquals(['commerce'], $darkLaunchLibrary->projects());
  }

  public function testUsersFunction(){
    $stub = $this->createMock(\Redis::class);
    $stub->method('smembers')
         ->willReturn(['pkandathil']);
    $darkLaunchLibrary = new DarkLaunchConfigAccessor($stub, $initialConfig, 'commerce', 'pkandathil');
    $this->assertEquals(['pkandathil'], $darkLaunchLibrary->users());
  }

  public function testFeaturesFunction() {
    $testValue = [
        'type' => 'string',
        'value' => 'asdf'
    ];
    $initialConfig = [
      'test' => $testValue  
    ];

    $stub = $this->createMock(\Redis::class);
    $map = [
            ['dark-launch:project:commerce:user:pkandathil:features', ['test']]
          ];
    $stub->method('smembers')
         ->will($this->returnValueMap($map));
    $stub->method('multi')
         ->will($this->returnSelf());
    $stub->method('exec')
         ->willReturn($initialConfig);

    $darkLaunchLibrary = new DarkLaunchConfigAccessor($stub, $initialConfig, 'commerce', 'pkandathil');
    $this->assertEquals($initialConfig, $darkLaunchLibrary->features());
  }

  public function testEnableFeatureFunction() {
    $stub = $this->createMock(\Redis::class);
    $map = [
            ['dark-launch:project:commerce:user:pkandathil:features', ['test']]
          ];
  }

}
