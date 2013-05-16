<?php
class TutorialTest extends PHPUnit_Framework_TestCase
{
  public function setUp()
  {
    // to test the write methods
    $this->tutorial = new Tutorial;
    if(class_exists('vfsStream'))
    {
      vfsStreamWrapper::register();
      vfsStreamWrapper::setRoot(new vfsStreamDirectory('themeDir'));
      vfsStreamWrapper::setRoot(new vfsStreamDirectory('themeConfigDir'));
      $this->themeDir = vfsStream::url('themeDir');
      $this->themeConfigDir = vfsStream::url('themeConfigDir');
      mkdir("{$this->themeDir}/html");
      mkdir("{$this->themeDir}/html/assets");
      mkdir($themeDir = "{$this->themeDir}/html/assets/themes");
      mkdir("{$this->themeDir}/html/assets/themes/fabrizio1.0");
    }

    $utility = $this->getMock('Utility', array('isActiveTab'));
    $utility->expects($this->any())
      ->method('isActiveTab')
      ->will($this->returnValue(true));
    $this->tutorial->inject('utility', $utility);

    $theme = $this->getMock('Theme', array('getThemeName'), array(), '', false);
    $theme->expects($this->any())
      ->method('getThemeName')
      ->will($this->returnValue('fabrizio1.0'));
    $this->tutorial->inject('theme', $theme);

    $this->templateIni = <<<STR
[tutorialAll]
1='{"selector":".navbar .navbar-inner ul.nav a.showBatchForm","intro":"Want to create an album? This link makes it easy to start.","width":"200"}'
2='{"selector":".navbar .navbar-inner ul.nav a.showBatchForm","intro":"Want to create an album? This link makes it easy to start.","width":"200"}'

[tutorialAlbums]
1='{"selector":".navbar .navbar-inner ul.nav li.batch .selectAll","intro":"<p>Select a photo by clicking the pushpin or alt+clicking the photo.</p><img src=\"/assets/themes/fabrizio1.0/images/highlight-pushpin.jpg\" style=\"width:200px; height:142px;\"><p>To select a range shift+click the first and last photo in the range.</p>","width":"200"}'
2='{"selector":".navbar .navbar-inner ul.nav li.batch.dropdown a:first","intro":"Edit multiple photos using your Batch Queue.","width":"200"}'
3='{"selector":".container .userbadge h4.username span","intro":"This is the name of your site. Change it by clicking.","width":"200"}'
4='{"selector":".container .userbadge .details","intro":"This is your account by the numbers."}'
STR;
    $config = (object)parse_ini_string($this->templateIni, true);
    $config->paths = new stdClass;
    $config->paths->themes = $themeDir;
    $this->tutorial->inject('config', $config);

  }

  public function testGetUnseenNotAdmin()
  {
    $this->markTestSkipped('Skipping because of VFS failure on Travis CI');
    return;
    /*$user = $this->getMock('User', array('isAdmin'));
    $user->expects($this->any())
      ->method('isAdmin')
      ->will($this->returnValue(false));
    $this->tutorial->inject('user', $user);
    $unseen = $this->tutorial->getUnseen();
    $this->assertFalse($unseen);*/
  }

  public function testGetUnseenFirstTime()
  {
    $this->markTestSkipped('Skipping because of VFS failure on Travis CI');
    return;
    /*$user = $this->getMock('User', array('isAdmin','getAttribute'));
    $user->expects($this->any())
      ->method('isAdmin')
      ->will($this->returnValue(true));
    $user->expects($this->any()) // first call is to all
      ->method('getAttribute')
      ->with($this->equalTo('tutorialAll'))
      ->will($this->returnValue(false));
    $this->tutorial->inject('user', $user);

    file_put_contents(sprintf('%s/template.ini', $this->themeConfigDir), $this->templateIni);
    $unseen = $this->tutorial->getUnseen();
    $this->assertEquals('tutorialAll', $unseen[0]['section']);*/
  }

  public function testGetUnseenWithAll()
  {
    $this->markTestSkipped('Skipping because of VFS failure on Travis CI');
    return;
    /*$user = $this->getMock('User', array('isAdmin','getAttribute'));
    $user->expects($this->any())
      ->method('isAdmin')
      ->will($this->returnValue(true));
    $user->expects($this->any())
      ->method('getAttribute')
      ->will($this->returnValue('1'));
    $this->tutorial->inject('user', $user);
    file_put_contents(sprintf('%s/template.ini', $this->themeConfigDir), $this->templateIni);
    $unseen = $this->tutorial->getUnseen();
    $this->assertEquals('tutorialAll', $unseen[0]['section']);*/
  }

  public function testGetUnseenWithoutAllWithAlbums()
  {
    $this->markTestSkipped('Skipping because of VFS failure on Travis CI');
    return;
    /*$user = $this->getMock('User', array('isAdmin','getAttribute'));
    $user->expects($this->any())
      ->method('isAdmin')
      ->will($this->returnValue(true));
    $user->expects($this->at(1)) // first call is to "all"
      ->method('getAttribute')
      ->will($this->returnValue('4'));
    $user->expects($this->at(2)) // second call is to "albums"
      ->method('getAttribute')
      ->will($this->returnValue(false));
    $this->tutorial->inject('user', $user);
    file_put_contents(sprintf('%s/template.ini', $this->themeConfigDir), $this->templateIni);
    $unseen = $this->tutorial->getUnseen();
    $this->assertEquals('tutorialAlbums', $unseen[0]['section']);*/
  }

  public function testGetUnseenWithoutAny()
  {
    $this->markTestSkipped('Skipping because of VFS failure on Travis CI');
    return;
    /*$user = $this->getMock('User', array('isAdmin','getAttribute'));
    $user->expects($this->any())
      ->method('isAdmin')
      ->will($this->returnValue(true));
    $user->expects($this->at(1)) // first call is to "all"
      ->method('getAttribute')
      ->will($this->returnValue('4'));
    $user->expects($this->at(2)) // second call is to "albums"
      ->method('getAttribute')
      ->will($this->returnValue('4'));
    $this->tutorial->inject('user', $user);
    file_put_contents(sprintf('%s/template.ini', $this->themeConfigDir), $this->templateIni);
    $unseen = $this->tutorial->getUnseen();
    $this->assertTrue(is_array($unseen) && count($unseen) === 0);*/
  }
}
