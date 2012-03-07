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

  public function testOnActionNoParam()
  {
    $res = $this->pluginBase->onAction();
    $this->assertNull($res);
  }

  public function testOnActionSuccess()
  {
    $res = $this->pluginBase->onAction(array());
    $this->assertNull($res);
  }

  public function testRenderHeadNoParam()
  {
    $res = $this->pluginBase->renderHead();
    $this->assertNull($res);
  }

  public function testRenderHeadSuccess()
  {
    $res = $this->pluginBase->RenderHead(array());
    $this->assertNull($res);
  }

  public function testOnViewSuccess()
  {
    $res = $this->pluginBase->onView(array());
    $this->assertNull($res);
  }
}
