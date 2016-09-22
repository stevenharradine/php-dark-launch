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
  public function testIfABooleanFeature($darkLaunchValue, $expectedResult) {
    $stub = $this->createMock(\Redis::class);
    $stub->method('hgetall')
         ->willReturn($darkLaunchValue);

    $this->assertEquals($darkLaunchValue, $stub->hgetall());
    
    $darkLaunchLibrary = new DarkLaunchConfigAccessor($stub);
    $result = $darkLaunchLibrary->featureEnabled('test');
    $this->assertEquals($expectedResult, $result);
  }

  public function darkLaunchValueDataProvider(){
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
        ]
    ];
  }

}
