<?php
class UrlTest extends PHPUnit_Framework_TestCase
{
  public function setUp()
  {
    // to test the write methods
    ob_start();
    $this->url = new Url;
    $this->id = uniqid();
  }

  public function testActionCreate()
  {
    $url = $this->url->actionCreate($this->id, 'photo', false);
    $this->assertEquals("/action/{$this->id}/photo/create", $url, 'Urls do not match');
    $this->url->actionCreate($this->id, 'photo');
    $url = ob_get_contents();
    ob_clean();
    $this->assertEquals("/action/{$this->id}/photo/create", $url, 'Urls do not match');
    $this->url->actionCreate($this->id, 'photo', true);
    $url = ob_get_contents();
    ob_clean();
    $this->assertEquals("/action/{$this->id}/photo/create", $url, 'Urls do not match');
  }

  public function testActionDelete()
  {
    $url = $this->url->actionDelete($this->id, false);
    $this->assertEquals("/action/{$this->id}/delete", $url, 'Urls do not match');
    $this->url->actionDelete($this->id);
    $url = ob_get_contents();
    ob_clean();
    $this->assertEquals("/action/{$this->id}/delete", $url, 'Urls do not match');
    $this->url->actionDelete($this->id, true);
    $url = ob_get_contents();
    ob_clean();
    $this->assertEquals("/action/{$this->id}/delete", $url, 'Urls do not match');
  }

  public function testPhotoDelete()
  {
    $url = $this->url->photoDelete($this->id, false);
    $this->assertEquals("/photo/{$this->id}/delete", $url, 'Urls do not match');
    $this->url->photoDelete($this->id);
    $url = ob_get_contents();
    ob_clean();
    $this->assertEquals("/photo/{$this->id}/delete", $url, 'Urls do not match');
    $this->url->photoDelete($this->id, true);
    $url = ob_get_contents();
    ob_clean();
    $this->assertEquals("/photo/{$this->id}/delete", $url, 'Urls do not match');
  }

  public function testPhotoEdit()
  {
    $url = $this->url->photoEdit($this->id, false);
    $this->assertEquals("/photo/{$this->id}/edit", $url, 'Urls do not match');
    $this->url->photoEdit($this->id);
    $url = ob_get_contents();
    ob_clean();
    $this->assertEquals("/photo/{$this->id}/edit", $url, 'Urls do not match');
    $this->url->photoEdit($this->id, true);
    $url = ob_get_contents();
    ob_clean();
    $this->assertEquals("/photo/{$this->id}/edit", $url, 'Urls do not match');
  }

  public function testPhotoUpdate()
  {
    $url = $this->url->photoUpdate($this->id, false);
    $this->assertEquals("/photo/{$this->id}/update", $url, 'Urls do not match');
    $this->url->photoUpdate($this->id);
    $url = ob_get_contents();
    ob_clean();
    $this->assertEquals("/photo/{$this->id}/update", $url, 'Urls do not match');
    $this->url->photoUpdate($this->id, true);
    $url = ob_get_contents();
    ob_clean();
    $this->assertEquals("/photo/{$this->id}/update", $url, 'Urls do not match');
  }

  public function testPhotoUrl()
  {
    $key = '30x30';
    $val = uniqid();
    $photo = array("path{$key}" => $val);
    $url = $this->url->photoUrl($photo, $key, false);
    $this->assertEquals($val, $url, 'Urls do not match');
    $this->url->photoUrl($photo, $key);
    $url = ob_get_contents();
    ob_clean();
    $this->assertEquals($val, $url, 'Urls do not match');
    $this->url->photoUrl($photo, $key, true);
    $url = ob_get_contents();
    ob_clean();
    $this->assertEquals($val, $url, 'Urls do not match');
  }

  public function testPhotoView()
  {
    $url = $this->url->photoView($this->id, null, false);
    $this->assertEquals("/p/{$this->id}", $url, 'Urls do not match');
    $this->url->photoView($this->id);
    $url = ob_get_contents();
    ob_clean();
    $this->assertEquals("/p/{$this->id}", $url, 'Urls do not match');
    $this->url->photoView($this->id, null, true);
    $url = ob_get_contents();
    ob_clean();
    $this->assertEquals("/p/{$this->id}", $url, 'Urls do not match');
  }

  public function testPhotoViewShort()
  {
    $_SERVER['REDIRECT_URL'] = "/p/{$this->id}";
    $url = $this->url->photoView($this->id, null, false);
    $this->assertEquals("/p/{$this->id}", $url, 'Urls do not match');
    $this->url->photoView($this->id);
    $url = ob_get_contents();
    ob_clean();
    $this->assertEquals("/p/{$this->id}", $url, 'Urls do not match');
    $this->url->photoView($this->id, null, true);
    $url = ob_get_contents();
    ob_clean();
    $this->assertEquals("/p/{$this->id}", $url, 'Urls do not match');
  }
}
