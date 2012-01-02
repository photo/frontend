<?php
$baseDir = dirname(dirname(dirname(dirname(__FILE__))));
require_once sprintf('%s/tests/helpers/init.php', $baseDir);
require_once sprintf('%s/libraries/models/Utility.php', $baseDir);
require_once sprintf('%s/libraries/models/Url.php', $baseDir);

class UrlTest extends PHPUnit_Framework_TestCase
{
  public function setUp()
  {
    ob_start();
    $this->id = uniqid();
  }

  public function testActionCreate()
  {
    $url = Url::actionCreate($this->id, 'photo', false);
    $this->assertEquals("/action/{$this->id}/photo/create", $url, 'Urls do not match');
    Url::actionCreate($this->id, 'photo');
    $url = ob_get_contents();
    ob_clean();
    $this->assertEquals("/action/{$this->id}/photo/create", $url, 'Urls do not match');
    Url::actionCreate($this->id, 'photo', true);
    $url = ob_get_contents();
    ob_clean();
    $this->assertEquals("/action/{$this->id}/photo/create", $url, 'Urls do not match');
  }

  public function testActionDelete()
  {
    $url = Url::actionDelete($this->id, false);
    $this->assertEquals("/action/{$this->id}/delete", $url, 'Urls do not match');
    Url::actionDelete($this->id);
    $url = ob_get_contents();
    ob_clean();
    $this->assertEquals("/action/{$this->id}/delete", $url, 'Urls do not match');
    Url::actionDelete($this->id, true);
    $url = ob_get_contents();
    ob_clean();
    $this->assertEquals("/action/{$this->id}/delete", $url, 'Urls do not match');
  }

  public function testPhotoDelete()
  {
    $url = Url::photoDelete($this->id, false);
    $this->assertEquals("/photo/{$this->id}/delete", $url, 'Urls do not match');
    Url::photoDelete($this->id);
    $url = ob_get_contents();
    ob_clean();
    $this->assertEquals("/photo/{$this->id}/delete", $url, 'Urls do not match');
    Url::photoDelete($this->id, true);
    $url = ob_get_contents();
    ob_clean();
    $this->assertEquals("/photo/{$this->id}/delete", $url, 'Urls do not match');
  }

  public function testPhotoEdit()
  {
    $url = Url::photoEdit($this->id, false);
    $this->assertEquals("/photo/{$this->id}/edit", $url, 'Urls do not match');
    Url::photoEdit($this->id);
    $url = ob_get_contents();
    ob_clean();
    $this->assertEquals("/photo/{$this->id}/edit", $url, 'Urls do not match');
    Url::photoEdit($this->id, true);
    $url = ob_get_contents();
    ob_clean();
    $this->assertEquals("/photo/{$this->id}/edit", $url, 'Urls do not match');
  }

  public function testPhotoUpdate()
  {
    $url = Url::photoUpdate($this->id, false);
    $this->assertEquals("/photo/{$this->id}/update", $url, 'Urls do not match');
    Url::photoUpdate($this->id);
    $url = ob_get_contents();
    ob_clean();
    $this->assertEquals("/photo/{$this->id}/update", $url, 'Urls do not match');
    Url::photoUpdate($this->id, true);
    $url = ob_get_contents();
    ob_clean();
    $this->assertEquals("/photo/{$this->id}/update", $url, 'Urls do not match');
  }

  public function testPhotoUrl()
  {
    $key = '30x30';
    $val = uniqid();
    $photo = array("path{$key}" => $val);
    $url = Url::photoUrl($photo, $key, false);
    $this->assertEquals($val, $url, 'Urls do not match');
    Url::photoUrl($photo, $key);
    $url = ob_get_contents();
    ob_clean();
    $this->assertEquals($val, $url, 'Urls do not match');
    Url::photoUrl($photo, $key, true);
    $url = ob_get_contents();
    ob_clean();
    $this->assertEquals($val, $url, 'Urls do not match');
  }

  public function testPhotoView()
  {
    $url = Url::photoView($this->id, null, false);
    $this->assertEquals("/photo/{$this->id}/view", $url, 'Urls do not match');
    Url::photoView($this->id);
    $url = ob_get_contents();
    ob_clean();
    $this->assertEquals("/photo/{$this->id}/view", $url, 'Urls do not match');
    Url::photoView($this->id, null, true);
    $url = ob_get_contents();
    ob_clean();
    $this->assertEquals("/photo/{$this->id}/view", $url, 'Urls do not match');
  }
}
