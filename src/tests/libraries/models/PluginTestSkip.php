<?php
class Fake1Plugin extends PluginBase
{
  public function defineConf()
  {
    return array('foo' => 'bar');
  }

  public function onLoad()
  {
    return 'foo';
  }
}
class Fake2Plugin extends PluginBase {}

class PluginTest extends PHPUnit_Framework_TestCase
{
  public function __construct()
  {
    if(class_exists('vfsStream'))
    {
      vfsStreamWrapper::register();
      vfsStreamWrapper::setRoot(new vfsStreamDirectory('pluginDir'));
      $this->pluginDir = vfsStream::url('testDir');
      file_put_contents(sprintf('%s/Fake2Plugin.php', $this->pluginDir), '<?php /* sample */ ?>');
      file_put_contents(sprintf('%s/Fake1Plugin.php', $this->pluginDir), '<?php /* sample */ ?>');
      $this->assertFalse(vfsStreamWrapper::getRoot()->hasChild(sprintf('%s/Fake2Plugin.php', $this->pluginDir)), 'Init validation that vfs file does not exist failed');
    }
    
    $this->vfsPath = $this->pluginDir;
    $config = array(
      'paths' => array('plugins' => $this->pluginDir, 'userdata' => $this->pluginDir),
      'plugins' => array('activePlugins' => 'Fake2,Fake1')
    );
    $params = array('config' => arrayToObject($config));
    $this->plugin = new Plugin($params);
  }

  public function testValidateVfsFunctionSuccess()
  {
    if(!class_exists('vfsStream'))
    {
      $this->fail('The vfsStream package was not found. Skipping tests in FileSysemLocalTest. Install using `sudo pear channel-discover pear.bovigo.org && sudo pear install bovigo/vfsStream-beta`');
      return false;
    }

    return true;
  }

  /**
   * @depends testValidateVfsFunctionSuccess
   */
  public function testGetActiveSuccess()
  {
    $this->plugin->load();
    $res = $this->plugin->getActive();
    $this->assertEquals(2, count($res));
    $this->assertEquals($res[0], 'Fake1', 'plugins are not alphabetized');
  }

  /**
   * @depends testValidateVfsFunctionSuccess
   */
  public function testGetActiveNoPluginsInConf()
  {
    $this->plugin->load();
    $this->plugin->inject('config', arrayToObject(array('paths' => null)));
    $res = $this->plugin->getActive();
    $this->assertEquals(array(), $res);
  }

  /**
   * @depends testValidateVfsFunctionSuccess
   */
  public function testGetAllNoPluginDir()
  {
    $this->plugin->load();
    $this->plugin->inject('pluginDir', null);
    $res = $this->plugin->getAll();
    $this->assertEquals(array(), $res);
  }

  /**
   * @depends testValidateVfsFunctionSuccess
   */
  public function testGetAllSuccess()
  {
    $this->plugin->load();
    $res = $this->plugin->getAll();
    $this->assertEquals(array('Fake1','Fake2'), $res);
  }

  /**
   * @depends testValidateVfsFunctionSuccess
   */
  public function testInvoke()
  {
    $this->plugin->load();
    ob_start();
    $this->plugin->invoke('onLoad');
    $res = ob_get_contents();
    ob_end_clean();
    $this->assertEquals('foo', $res);
  }

  /**
   * @depends testValidateVfsFunctionSuccess
   */
  public function testIsActiveYes()
  {
    $this->plugin->load();
    $res = $this->plugin->isActive('Fake1');
    $this->assertTrue($res);
  }

  /**
   * @depends testValidateVfsFunctionSuccess
   */
  public function testIsActiveNo()
  {
    $this->plugin->load();
    $res = $this->plugin->isActive('DNE');
    $this->assertFalse($res);
  }

  /**
   * @depends testValidateVfsFunctionSuccess
   */
  public function testLoadReturnsPluginInstance()
  {
    $res = $this->plugin->load();
    $this->assertTrue(($res instanceof Plugin));
  }

  /**
   * @depends testValidateVfsFunctionSuccess
   */
  public function testLoadConfNoInstance()
  {
    $this->plugin->load();
    $res = $this->plugin->loadConf('DNE');
    $this->assertNull($res);
  }

  /**
   * @depends testValidateVfsFunctionSuccess
   */
  public function testLoadConfNoConf()
  {
    $_SERVER['HTTP_HOST'] = 'foo';
    $this->plugin->load();
    $res = $this->plugin->loadConf('Fake2');
    $this->assertNull($res);
  }

  /**
   * @depends testValidateVfsFunctionSuccess
   */
  public function testLoadConfYesConf()
  {
    $_SERVER['HTTP_HOST'] = 'foo';
    mkdir(sprintf('%s/plugins', $this->pluginDir));
    $file = sprintf('%s/plugins/%s.%s.ini', $this->pluginDir, $_SERVER['HTTP_HOST'], 'Fake1');
    file_put_contents($file, 'foo=bar');
    $this->plugin->load();
    $res = $this->plugin->loadConf('Fake1');
    $this->assertEquals(array('foo' => 'bar'), $res);
  }

  /**
   * @depends testValidateVfsFunctionSuccess
   */
  public function testWriteConfCreateDirSuccess()
  {
    $_SERVER['HTTP_HOST'] = 'foo';
    @rmdir(sprintf('%s/plugins', $this->pluginDir));
    $this->plugin->load();
    $res = $this->plugin->writeConf('Fake1', 'foo');
    $this->assertTrue($res);
  }

  /**
   * @depends testValidateVfsFunctionSuccess
   */
  public function testWriteConfSuccess()
  {
    $_SERVER['HTTP_HOST'] = 'foo';
    @mkdir(sprintf('%s/plugins', $this->pluginDir));
    $this->plugin->load();
    $res = $this->plugin->writeConf('Fake1', 'foo');
    $this->assertTrue($res);
  }

  /**
   * @depends testValidateVfsFunctionSuccess
   */
  public function testWriteEmptyConf()
  {
    $_SERVER['HTTP_HOST'] = 'foo';
    @mkdir(sprintf('%s/plugins', $this->pluginDir));
    $this->plugin->load();
    $res = $this->plugin->writeConf('Fake1', '');
    $this->assertTrue($res);
  }

  /**
   * @depends testValidateVfsFunctionSuccess
   */
  public function testWriteConfFailure()
  {
    $_SERVER['HTTP_HOST'] = 'foo';
    $this->plugin->inject('config', arrayToObject(array('paths' => array('userdata' => 'foobar'))));
    $this->plugin->load();
    $res = $this->plugin->writeConf('Fake1', 'foo');
    $this->assertFalse($res);
  }
}
