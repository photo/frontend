<?php
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

  public function testOnViewSuccess()
  {
    $res = $this->pluginBase->onView(array());
    $this->assertNull($res);
  }

  public function testOnPhotoUploadSuccess()
  {
    $res = $this->pluginBase->onPhotoUpload(array());
    $this->assertNull($res);
  }

  public function testOnPhotoUploadedSuccess()
  {
    $res = $this->pluginBase->onPhotoUploaded(array());
    $this->assertNull($res);
  }

  public function testRenderHeadSuccess()
  {
    $res = $this->pluginBase->renderHead(array());
    $this->assertNull($res);
  }

  public function testRenderBodySuccess()
  {
    $res = $this->pluginBase->renderBody(array());
    $this->assertNull($res);
  }

  public function testRenderPhotoDetailSuccess()
  {
    $res = $this->pluginBase->renderPhotoDetail(array());
    $this->assertNull($res);
  }

  public function testRenderPhotoUploadedSuccess()
  {
    $res = $this->pluginBase->renderPhotoUploaded(array());
    $this->assertNull($res);
  }

  public function testRenderFooterSuccess()
  {
    $res = $this->pluginBase->renderFooter(array());
    $this->assertNull($res);
  }
}
