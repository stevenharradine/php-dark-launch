<?php
namespace Telus\Digital\LibrariesTests\DarkLaunch;
use PHPUnit\Framework\TestCase;

class BaseTest extends TestCase {
  /**
   * Returns a ref to a method of the class
   *
   * @param string Name of the class
   * @param string Name of the method in the class
   * @return method
   */
  protected static function getMethod($className, $methodName) {
    $class = new \ReflectionClass($className);
    $method = $class->getMethod($methodName);
    $method->setAccessible(true);
    return $method;
  }
  public function testBase() {
    $this->assertTrue(true);
  }

}
