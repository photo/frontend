<?php
class UpgradeTest extends PHPUnit_Framework_TestCase
{
  protected $config;
  protected $scriptsDir;
  protected $upgrade;

  public function setUp()
  {
    if(class_exists('vfsStream'))
    {
      vfsStreamWrapper::register();
      vfsStreamWrapper::setRoot(new vfsStreamDirectory('scriptsDir'));
      $this->scriptsDir = vfsStream::url('scriptsDir');
      mkdir($upgradeDir = "{$this->scriptsDir}/upgrade");
      mkdir("{$this->scriptsDir}/upgrade/readme");
      mkdir("{$this->scriptsDir}/upgrade/base");
      mkdir("{$this->scriptsDir}/upgrade/db");
      mkdir("{$this->scriptsDir}/upgrade/db/mysql");
      mkdir("{$this->scriptsDir}/upgrade/fs");
      $this->assertTrue(is_dir($upgradeDir));
    }
    $this->config = new stdClass;
    $this->config->paths = new stdClass;
    $this->config->paths->configs = $this->scriptsDir;
    $this->config->defaults = new stdClass;
    $this->config->defaults->currentCodeVersion = '1.1.1';
    $this->config->defaults->lastCodeVersion = '2.2.2';
    $this->config->site = new stdClass;
    $this->config->site->lastCodeVersion = '3.3.3';

    $params = array('config' => $this->config);

    $this->upgrade = new Upgrade($params);
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

  public function testGetCurrentVersion()
  {
    $res = $this->upgrade->getCurrentVersion();
    $this->assertEquals('1.1.1', $res);
  }

  public function testGetLastVersion()
  {
    $res = $this->upgrade->getLastVersion();
    $this->assertEquals('3.3.3', $res);
  }

  public function testGetLastVersionWhenNotInSiteIni()
  {
    $config = $this->config;
    $config->site = null;
    $upgrade = new Upgrade(array('config' => $config));
    $res = $upgrade->getLastVersion();
    $this->assertEquals('2.2.2', $res);
  }

  /**
   * @depends testValidateVfsFunctionSuccess
   */
  public function testGetUpgradeVersionsNoReadme()
  {
    $db = $this->getMock('Db', array('identity', 'version'));
    $db->expects($this->any())
      ->method('identity')
      ->will($this->returnValue(array('mysql')));
    $db->expects($this->any())
      ->method('version')
      ->will($this->returnValue('0.0.0'));
    $this->upgrade->inject('db', $db);

    $res = $this->upgrade->getUpgradeVersions();
    $this->assertFalse($res);
  }

  /**
   * @depends testValidateVfsFunctionSuccess
   */
  public function testGetUpgradeVersionsWithReadme()
  {
    $db = $this->getMock('Db', array('identity', 'version'));
    $db->expects($this->any())
      ->method('identity')
      ->will($this->returnValue(array('mysql')));
    $db->expects($this->any())
      ->method('version')
      ->will($this->returnValue('0.0.0'));
    $this->upgrade->inject('db', $db);

    file_put_contents("{$this->scriptsDir}/upgrade/db/mysql/mysql-3.3.3.php", 'hello world');

    $res = $this->upgrade->getUpgradeVersions();
    $this->assertEquals('vfs://scriptsDir/upgrade/db/mysql/mysql-3.3.3.php', $res['db']['mysql']['3.3.3'][0]);
  }

  public function testIsCurrentNo()
  {
    $res = $this->upgrade->isCurrent();
    $this->assertFalse($res);
  }

  public function testIsCurrentYes()
  {
    $config = $this->config;
    $config->defaults->currentCodeVersion = '3.3.3';
    $upgrade = new Upgrade(array('config' => $config));
    $res = $upgrade->isCurrent();
    $this->assertTrue($res);
  }

  public function testPerformUpgrade()
  {
    $this->markTestIncomplete('This test has not been implemented yet.');
  }

  public function test401Regex()
  {
    $configStr = <<<STR
foo="bar"

[theme]
name="default"

[more]
something="else"
STR;
    $configStr2 = str_replace('name="default"', 'name = "default"', $configStr);
    $configStr3 = str_replace('name="default"', 'name = default', $configStr);
    $configStr4 = str_replace('name="default"', 'name=default', $configStr);

    // target string
    $configStrTgt = str_replace('name="default"', 'name="fabrizio1.0"', $configStr);

    $str1 = preg_replace('/^name ?\= ?"?.+"?$/m', 'name="fabrizio1.0"', $configStr);
    $this->assertEquals($configStrTgt, $str1, 'Failed with default string');

    $str1 = preg_replace('/^name ?\= ?"?.+"?$/m', 'name="fabrizio1.0"', $configStr2);
    $this->assertEquals($configStrTgt, $str1, 'failed with spaces around =');

    $str1 = preg_replace('/^name ?\= ?"?.+"?$/m', 'name="fabrizio1.0"', $configStr3);
    $this->assertEquals($configStrTgt, $str1, 'failed with spaces and no quotes');

    $str1 = preg_replace('/^name ?\= ?"?.+"?$/m', 'name="fabrizio1.0"', $configStr4);
    $this->assertEquals($configStrTgt, $str1, 'failed with no quotes');
  }
}

