<?php
$baseDir = dirname(dirname(dirname(dirname(__FILE__))));
require_once sprintf('%s/tests/helpers/init.php', $baseDir);
require_once sprintf('%s/libraries/models/BaseModel.php', $baseDir);
require_once sprintf('%s/libraries/models/PluginBase.php', $baseDir);

class PluginBaseTest extends PHPUnit_Framework_TestCase
{
  public function setUp()
  {
    $this->pluginBase = new PluginBase(array('plugin' => new FauxObject));
  }

  public function testDefineConf()
  {
    $res = $this->pluginBase->defineConf();
    $this->assertNull($res);
  }

  public function testGetConf()
  {
    $this->markTestIncomplete('This test has not been implemented yet.');
  }

  /**
   * @expectedException PHPUnit_Framework_Error
   */
  public function testOnActionNoParam()
  {
    $res = $this->pluginBase->onAction();
  }

  public function testOnActionSuccess()
  {
    $res = $this->pluginBase->onAction(array());
    $this->assertNull($res);
  }

  public function testOnBodyBeginNoParam()
  {
    $res = $this->pluginBase->onBodyBegin();
    $this->assertNull($res);
  }

  public function testOnBodyBeginSuccess()
  {
    $res = $this->pluginBase->onBodyBegin(array());
    $this->assertNull($res);
  }

  public function testOnBodyEndNoParam()
  {
    $res = $this->pluginBase->onBodyEnd();
    $this->assertNull($res);
  }

  public function testOnBodyEndSuccess()
  {
    $res = $this->pluginBase->onBodyEnd(array());
    $this->assertNull($res);
  }

  public function testOnHeadNoParam()
  {
    $res = $this->pluginBase->onHead();
    $this->assertNull($res);
  }

  public function testOnHeadSuccess()
  {
    $res = $this->pluginBase->onHead(array());
    $this->assertNull($res);
  }

  public function testOnLoadNoParam()
  {
    $res = $this->pluginBase->onLoad();
    $this->assertNull($res);
  }

  public function testOnLoadSuccess()
  {
    $res = $this->pluginBase->onLoad(array());
    $this->assertNull($res);
  }

  /**
   * @expectedException PHPUnit_Framework_Error
   */
  public function testOnViewNoParam()
  {
    $res = $this->pluginBase->onView();
  }

  public function testOnViewSuccess()
  {
    $res = $this->pluginBase->onView(array());
    $this->assertNull($res);
  }
}
