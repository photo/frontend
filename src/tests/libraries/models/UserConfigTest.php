<?php
$paths = (array)explode(PATH_SEPARATOR, ini_get('include_path'));
foreach($paths as $path)
{
  if(file_exists("{$path}/vfsStream/vfsStream.php"))
    require_once 'vfsStream/vfsStream.php';
}
$baseDir = dirname(dirname(dirname(dirname(__FILE__))));
require_once sprintf('%s/tests/helpers/init.php', $baseDir);
require_once sprintf('%s/libraries/models/UserConfig.php', $baseDir);

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
    
    $params = array('utility' => new FauxObject);
    $_SERVER['HTTP_HOST'] = 'example.com';
    $this->userConfig = new UserConfigWrapper($params);
    $this->userConfig->inject('basePath', $this->userConfigDir);
  }

  public function testValidateVfsFunctionSuccess()
  {
    if(!class_exists('vfsStream'))
    {
      $this->fail('The vfsStream package was not found. Skipping tests in FileSysemLocalTest. Install using `sudo pear channel-discover pear.php-tools.net && sudo pear install pat/vfsStream-beta`');
      return false;
    }

    return true;
  }

  public function testGetSiteSettingsWithoutSections()
  {
    file_put_contents("{$this->userConfigDir}/userdata/configs/example.com.ini", "foo=bar");
    $res = $this->userConfig->getSiteSettings();
    $expected = array('foo' => 'bar');
    $this->assertEquals($expected, $res);
  }

  public function testGetSiteSettingsWithSections()
  {
    file_put_contents("{$this->userConfigDir}/userdata/configs/example.com.ini", "[stuff]\nfoo=bar");
    $res = $this->userConfig->getSiteSettings();
    $expected = array('stuff' => array('foo' => 'bar'));
    $this->assertEquals($expected, $res);
  }

  public function testGetSiteSettingsDNE()
  {
    $res = $this->userConfig->getSiteSettings();
    $this->assertFalse($res);
  }

  public function testWriteSiteSettingsDNE()
  {
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
    $utility = $this->getMock('User', array('generateIniString'));
    $utility->expects($this->any())
      ->method('generateIniString')
      ->will($this->returnValue('foobar'));
    $this->userConfig->inject('utility', $utility);

    $res = $this->userConfig->writeSiteSettings(array('foo'));
    $this->assertEquals(6, $res);
    $writtenFile = file_get_contents($configFile);
    $this->assertEquals('foobar', $writtenFile);
  }

  public function testLoad()
  {
    $this->markTestIncomplete('This test has not been implemented yet.');
  }
}
