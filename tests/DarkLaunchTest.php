<?php
namespace Telus\Digital\LibrariesTests\DarkLaunch;

use Telus\Digital\LibrariesTests\DarkLaunch\BaseTest;
use Telus\Digital\Libraries\DarkLaunch\Implementations\DarkLaunchLibrary;
use Telus\Digital\Libraries\DarkLaunch\Implementations\DarkLaunchConfigAccessor;

class DarkLaunchTest extends BaseTest {

  public function testContructInstance() {
    $darkLaunchLibrary = new DarkLaunchConfigAccessor();
  }
}