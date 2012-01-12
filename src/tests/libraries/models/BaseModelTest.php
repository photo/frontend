<?php
$baseDir = dirname(dirname(dirname(dirname(__FILE__))));
require_once sprintf('%s/tests/helpers/init.php', $baseDir);
//require_once sprintf('%s/libraries/models/BaseModel.php', $baseDir);
/*
 * This test is commented out because the BaseModel is required by other models and already included
 *
 */
/*class BaseModelTest extends PHPUnit_Framework_TestCase
{
  public function setUp()
  {
    $this->base = new BaseModel;
  }

  public function testCountMembers()
  {
    $counter = 0;
    foreach($this->base as $k => $v)
      $counter++;
    $this->assertEquals(5, $counter, 'There should only be 5 members in the base model instance');
  }
}*/
