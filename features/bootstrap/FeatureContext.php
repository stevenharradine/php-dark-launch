<?php

use Behat\Behat\Context\Context;
use Behat\Behat\Context\SnippetAcceptingContext;
use Behat\Gherkin\Node\PyStringNode;
use Behat\Gherkin\Node\TableNode;


require_once __DIR__ . '/../../vendor/autoload.php';
use TelusDigital\DarkLaunch;

/**
 * Defines application features from the specific context.
 */
class FeatureContext implements Context, SnippetAcceptingContext
{ 

  private $dark_launch;

  private $output;

  /**
   * Initializes context.
   *
   * Every scenario gets its own context instance.
   * You can also pass arbitrary arguments to the
   * context constructor through behat.yml.
   */
  public function __construct()
  {
    $redis = new Redis();
    $redis->connect('127.0.0.1');
    $config = ['feature-1' => ['type' => 'boolean', 'value' => TRUE]];
    $params = ['redis' => $redis, 'config' => $config];
    $this->dark_launch = new Dark_Launch($params);
  }

  // /**
  // * @BeforeSuite
  // */
  // public static function prepare()
  // {
  //   $redis = new Redis();
  //   $redis->connect('127.0.0.1');
  //   $config = ['feature-1' => ['type' => 'boolean', 'value' => TRUE]];
  //   $params = ['redis' => $redis, 'config' => $config];
  //   $this->dark_launch = new Dark_Launch($params);
  // }

  /**
  * @Given I call get_feature with :arg1
  */
  public function iCallGetFeatureWith($arg1)
  {
    $is_enabled = $this->dark_launch->feature_enabled($arg1);
    $this->output = $is_enabled;
  }

  /**
  * @Then I should get TRUE
  */
  public function iShouldGetTrue()
  {
    PHPUnit_Framework_Assert::assertEquals(TRUE, $this->output);
  }

  /**
  * @Then I should get FALSE
  */
  public function iShouldGetFalse()
  {
    PHPUnit_Framework_Assert::assertEquals(FALSE, $this->output);
  }
}
