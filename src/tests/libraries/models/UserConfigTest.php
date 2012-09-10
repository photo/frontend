<?php
class UserConfigWrapper extends UserConfig
{
  public function __construct($params = null)
  {
    parent::__construct($params); 
  }

  public function inject($key, $val)
  {
    $this->$key = $val;
  }
}

class UserConfigTest extends PHPUnit_Framework_TestCase
{
  /**
   * @var UserConfigWrapper
   */
  protected $userConfig;

  /**
   * @var string
   */
  protected $userConfigDir;

  public function setUp()
  {
    if(class_exists('vfsStream'))
    {
      vfsStreamWrapper::register();
      vfsStreamWrapper::setRoot(new vfsStreamDirectory('userConfigDir'));
      $this->userConfigDir = vfsStream::url('userConfigDir');
      mkdir("{$this->userConfigDir}/userdata");
      mkdir("{$this->userConfigDir}/userdata/configs");
    }
    
    $params = array('utility' => new FauxObject, 'config' => new FauxObject);
    $_SERVER['HTTP_HOST'] = 'example.com';
    $this->userConfig = new UserConfigWrapper($params);
    $this->userConfig->inject('basePath', $this->userConfigDir);
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

  public function testGetSiteSettingsWithoutSections()
  {
    $config = $this->getMock('config', array('getString','exists'));
    $config->expects($this->any())
      ->method('getString')
      ->will($this->returnValue('foo=bar'));
    $config->expects($this->any())
      ->method('exists')
      ->will($this->returnValue(true));
    $this->userConfig->inject('config', $config);

    $res = $this->userConfig->getSiteSettings();
    $expected = array('foo' => 'bar');
    $this->assertEquals($expected, $res);
  }

  public function testGetSiteSettingsWithSections()
  {
    $config = $this->getMock('config', array('getString','exists'));
    $config->expects($this->any())
      ->method('getString')
      ->will($this->returnValue("[stuff]\nfoo=bar"));
    $config->expects($this->any())
      ->method('exists')
      ->will($this->returnValue(true));
    $this->userConfig->inject('config', $config);

    $res = $this->userConfig->getSiteSettings();
    $expected = array('stuff' => array('foo' => 'bar'));
    $this->assertEquals($expected, $res);
  }

  public function testGetSiteSettingsDNE()
  {
    $config = $this->getMock('config', array('exists'));
    $config->expects($this->any())
      ->method('exists')
      ->will($this->returnValue(false));
    $this->userConfig->inject('config', $config);

    $res = $this->userConfig->getSiteSettings();
    $this->assertFalse($res);
  }

  public function testWriteSiteSettingsDNE()
  {
    $config = $this->getMock('config', array('exists'));
    $config->expects($this->any())
      ->method('exists')
      ->will($this->returnValue(false));
    $this->userConfig->inject('config', $config);

    $res = $this->userConfig->writeSiteSettings(array('foo'));
    $this->assertFalse($res);
  }

  public function testWriteSiteSettingInvalidIniString()
  {
    file_put_contents("{$this->userConfigDir}/userdata/configs/example.com.ini", "[stuff]\nfoo=bar");
    $utility = $this->getMock('User', array('generateIniString'));
    $utility->expects($this->any())
      ->method('generateIniString')
      ->will($this->returnValue(''));
    $this->userConfig->inject('utility', $utility);

    $res = $this->userConfig->writeSiteSettings(array('foo'));
    $this->assertFalse($res);
  }

  public function testWriteSiteSettingSuccess()
  {
    file_put_contents($configFile = "{$this->userConfigDir}/userdata/configs/example.com.ini", "[stuff]\nfoo=bar");
    $utility = $this->getMock('utility', array('generateIniString'));
    $utility->expects($this->any())
      ->method('generateIniString')
      ->will($this->returnValue('foobar'));
    $config = $this->getMock('config', array('write','exists'));
    $config->expects($this->any())
      ->method('write')
      ->will($this->returnValue(true));
    $config->expects($this->any())
      ->method('exists')
      ->will($this->returnValue(true));
    $this->userConfig->inject('config', $config);
    $this->userConfig->inject('utility', $utility);

    $res = $this->userConfig->writeSiteSettings(array('foo'));
    $this->assertEquals(true, $res);
  }

  public function testLoad()
  {
    $this->markTestIncomplete('This test has not been implemented yet.');
  }
}
