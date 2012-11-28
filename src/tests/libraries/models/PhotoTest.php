<?php
class PhotoTest extends PHPUnit_Framework_TestCase
{
  public function setUp()
  {
    $_SERVER['HTTP_HOST'] = 'foobar';
    $params = array('user' => new FauxObject, 'utility' => new stdClass, 'url' => new stdClass, 'image' => new FauxObject);
    $this->photo = new Photo($params);;
    $config = new stdClass;
    $config->site = new stdClass;
    $config->site->allowOriginalDownload = 1;
    $secrets = new stdClass;
    $secrets->secret = 'secret';
    $config->secrets = $secrets;
    $this->photo->inject('config', $config);

    $this->photoData = array(
      'id'=>'a','title'=>'title','host'=>'host','width'=>1100,'height'=>2000,
      'pathOriginal'=>'/path/original','pathBase'=>'/path/base','path10x10'=>'/path/foo10x10');
  }

  public function testAddApiUrls()
  {
    $url = $this->getMock('Url', array('photoView'));
    $url->expects($this->any())
      ->method('photoView')
      ->will($this->returnValue('/url'));
    $utility = $this->getMock('Utility', array('getProtocol'));
    $utility->expects($this->any())
      ->method('getProtocol')
      ->will($this->returnValue('http'));
    $this->photo->inject('url', $url);
    $this->photo->inject('utility', $utility);

    // api for already existing photos
    $res = $this->photo->addApiUrls($this->photoData, array('10x10'));
    $this->assertEquals('http://host/path/foo10x10', $res['path10x10'], 'The path10x10 is not correct');
    $this->assertEquals('http://host/path/foo10x10', $res['photo10x10'][0], 'The path is not correct in the photo array');
    $this->assertEquals(5, $res['photo10x10'][1], 'The width is not correct in the photo array');
    $this->assertEquals(10, $res['photo10x10'][2], 'The height is not correct in the photo array');
    $this->assertEquals('http://foobar/url', $res['url'], 'Url is not correct');

    // create api for non cropped
    $res = $this->photo->addApiUrls($this->photoData, array('10x15'));
    $this->assertEquals('http://foobar/photo/a/create/c77a8/10x15.jpg', $res['path10x15'], 'The path10x10 is not correct');
    $this->assertEquals('http://foobar/photo/a/create/c77a8/10x15.jpg', $res['photo10x15'][0], 'The path is not correct in the photo array');
    $this->assertEquals(8, $res['photo10x15'][1], 'The width is not correct in the photo array');
    $this->assertEquals(15, $res['photo10x15'][2], 'The height is not correct in the photo array');

    // create api for cropped
    $res = $this->photo->addApiUrls($this->photoData, array('10x10xCR'));
    $this->assertEquals('http://foobar/photo/a/create/4b950/10x10xCR.jpg', $res['path10x10xCR'], 'The path10x10 is not correct');
    $this->assertEquals('http://foobar/photo/a/create/4b950/10x10xCR.jpg', $res['photo10x10xCR'][0], 'The path is not correct in the photo array');
    $this->assertEquals(10, $res['photo10x10xCR'][1], 'The width is not correct in the photo array');
    $this->assertEquals(10, $res['photo10x10xCR'][2], 'The height is not correct in the photo array');
  }

  public function testAddApiUrlsOriginalAsOwner()
  {
    $user = $this->getMock('User', array('isOwner'));
    $user->expects($this->any())
      ->method('isOwner')
      ->will($this->returnValue(true));
    $url = $this->getMock('Url', array('photoView'));
    $url->expects($this->any())
      ->method('photoView')
      ->will($this->returnValue('/url'));
    $utility = $this->getMock('Utility', array('getProtocol'));
    $utility->expects($this->any())
      ->method('getProtocol')
      ->will($this->returnValue('http'));
    $config = $this->photo->config;
    $config->site->allowOriginalDownload = 0;
    $this->photo->inject('user', $user);
    $this->photo->inject('url', $url);
    $this->photo->inject('utility', $utility);
    $this->photo->inject('config', $config);

    $res = $this->photo->addApiUrls($this->photoData, array('10x10'));
    $this->assertTrue(isset($res['pathOriginal']));
    $this->assertTrue(isset($res['pathDownload']));
  }

  public function testAddApiUrlsOriginalAsNonOwner()
  {
    $user = $this->getMock('User', array('isOwner'));
    $user->expects($this->any())
      ->method('isOwner')
      ->will($this->returnValue(false));
    $url = $this->getMock('Url', array('photoView'));
    $url->expects($this->any())
      ->method('photoView')
      ->will($this->returnValue('/url'));
    $utility = $this->getMock('Utility', array('getProtocol'));
    $utility->expects($this->any())
      ->method('getProtocol')
      ->will($this->returnValue('http'));
    $config = $this->photo->config;
    $config->site->allowOriginalDownload = 1;
    $this->photo->inject('user', $user);
    $this->photo->inject('url', $url);
    $this->photo->inject('utility', $utility);
    $this->photo->inject('config', $config);

    $res = $this->photo->addApiUrls($this->photoData, array('10x10'));
    $this->assertTrue(isset($res['pathOriginal']));
    $this->assertTrue(isset($res['pathDownload']));
  }

  public function testAddApiUrlsOriginalNotAllowed()
  {
    $user = $this->getMock('User', array('isOwner'));
    $user->expects($this->any())
      ->method('isOwner')
      ->will($this->returnValue(false));
    $url = $this->getMock('Url', array('photoView'));
    $url->expects($this->any())
      ->method('photoView')
      ->will($this->returnValue('/url'));
    $utility = $this->getMock('Utility', array('getProtocol'));
    $utility->expects($this->any())
      ->method('getProtocol')
      ->will($this->returnValue('http'));
    $config = $this->photo->config;
    $config->site->allowOriginalDownload = 0;
    $this->photo->inject('user', $user);
    $this->photo->inject('url', $url);
    $this->photo->inject('utility', $utility);
    $this->photo->inject('config', $config);

    $res = $this->photo->addApiUrls($this->photoData, array('10x10'));
    $this->assertFalse(isset($res['pathOriginal']));
    $this->assertFalse(isset($res['pathDownload']));
  }

  public function testDeleteCouldNotGetPhoto()
  {
    $db = $this->getMock('db', array('getPhoto'));
    $db->expects($this->any())
      ->method('getPhoto')
      ->will($this->returnValue(false));
    $this->photo->inject('db', $db);

    $res = $this->photo->delete('foo');
    $this->assertFalse($res, 'delete should return FALSE if it could not get the photo from the db');
  }

  public function testDeleteCouldNotDeleteFile()
  {
    $db = $this->getMock('db', array('getPhoto','deletePhoto'));
    $db->expects($this->any())
      ->method('getPhoto')
      ->will($this->returnValue(array('id'=>'foo')));
    $db->expects($this->any())
      ->method('deletePhoto')
      ->will($this->returnValue(false));
    $db = $this->getMock('db', array('getPhoto'));
    $fs = $this->getMock('fs', array('deletePhoto'));
    $fs->expects($this->any())
      ->method('deletePhoto')
      ->will($this->returnValue(false));
    $this->photo->inject('fs', $fs);
    $this->photo->inject('db', $db);

    $res = $this->photo->delete('foo');
    $this->assertFalse($res, 'delete should return FALSE if it could not delete from file system');
  }

  public function testDeleteCouldNotDeleteFromDb()
  {
    $db = $this->getMock('db', array('getPhoto','deletePhoto'));
    $db->expects($this->any())
      ->method('getPhoto')
      ->will($this->returnValue(array('id'=>'foo')));
    $db->expects($this->any())
      ->method('deletePhoto')
      ->will($this->returnValue(false));
    $fs = $this->getMock('fs', array('deletePhoto'));
    $fs->expects($this->any())
      ->method('deletePhoto')
      ->will($this->returnValue(true));
    $this->photo->inject('fs', $fs);
    $this->photo->inject('db', $db);

    $res = $this->photo->delete('foo');
    $this->assertFalse($res, 'delete should return FALSE if it could not delete from database');
  }

  public function testDeleteSuccess()
  {
    $db = $this->getMock('db', array('getPhoto','deletePhoto'));
    $db->expects($this->any())
      ->method('getPhoto')
      ->will($this->returnValue(array('id'=>'foo')));
    $db->expects($this->any())
      ->method('deletePhoto')
      ->will($this->returnValue(true));
    $fs = $this->getMock('fs', array('deletePhoto'));
    $fs->expects($this->any())
      ->method('deletePhoto')
      ->will($this->returnValue(true));
    $this->photo->inject('fs', $fs);
    $this->photo->inject('db', $db);

    $res = $this->photo->delete('foo');
    $this->assertTrue($res, 'delete should return TRUE on success');
  }

  public function testDeleteSourceSuccess()
  {
    $db = $this->getMock('db', array('getPhoto', 'deletePhotoVersions'));
    $db->expects($this->any())
      ->method('getPhoto')
      ->will($this->returnValue(true));
    $db->expects($this->any())
      ->method('deletePhotoVersions')
      ->will($this->returnValue(true));
    $fs = $this->getMock('fs', array('deletePhoto'));
    $fs->expects($this->any())
      ->method('deletePhoto')
      ->will($this->returnValue(true));
    $this->photo->inject('db', $db);
    $this->photo->inject('fs', $fs);

    $res = $this->photo->deleteSourceFiles('foo');
    $this->assertTrue($res);
  }

  public function testDeleteSourceCouldNotGetPhoto()
  {
    $db = $this->getMock('db', array('getPhoto'));
    $db->expects($this->any())
      ->method('getPhoto')
      ->will($this->returnValue(false));
    $this->photo->inject('db', $db);

    $res = $this->photo->deleteSourceFiles('foo');
    $this->assertFalse($res);
  }

  public function testDeleteSourceCouldNotDeletePhoto()
  {
    $db = $this->getMock('db', array('getPhoto', 'deletePhotoVersions'));
    $db->expects($this->any())
      ->method('getPhoto')
      ->will($this->returnValue(true));
    $db->expects($this->any())
      ->method('deletePhotoVersions')
      ->will($this->returnValue(false));
    $fs = $this->getMock('fs', array('deletePhoto'));
    $fs->expects($this->any())
      ->method('deletePhoto')
      ->will($this->returnValue(false));
    $this->photo->inject('db', $db);
    $this->photo->inject('fs', $fs);

    $res = $this->photo->deleteSourceFiles('foo');
    $this->assertFalse($res);
  }

  public function testDeleteSourceCouldNotDeletePhotoVersionsDb()
  {
    $db = $this->getMock('db', array('getPhoto', 'deletePhotoVersions'));
    $db->expects($this->any())
      ->method('getPhoto')
      ->will($this->returnValue(true));
    $db->expects($this->any())
      ->method('deletePhotoVersions')
      ->will($this->returnValue(false));
    $fs = $this->getMock('fs', array('deletePhoto'));
    $fs->expects($this->any())
      ->method('deletePhoto')
      ->will($this->returnValue(false));
    $this->photo->inject('db', $db);
    $this->photo->inject('fs', $fs);

    $res = $this->photo->deleteSourceFiles('foo');
    $this->assertFalse($res);
  }

  public function testGenerateCustomKey()
  {
    $res = $this->photo->generateCustomKey(10, 15, 'CR');
    $this->assertEquals('path10x15xCR', $res);

    $res = $this->photo->generateCustomKey(10, 15);
    $this->assertEquals('path10x15', $res);
  }

  public function testGenerateFragment()
  {
    $res = $this->photo->generateFragment(10, 15, 'CR');
    $this->assertEquals('10x15xCR', $res);

    $res = $this->photo->generateFragment(10, 15);
    $this->assertEquals('10x15', $res);
  }

  public function testGenerateFailsHashValidation()
  {
    $res = $this->photo->generate('id', 'hash', 10, 15);
    $this->assertFalse($res);
  }

  public function testGenerateCouldNotGetPhotoFromDb()
  {
    $db = $this->getMock('db', array('getPhoto'));
    $db->expects($this->any())
      ->method('getPhoto')
      ->will($this->returnValue(false));
    $this->photo->inject('db', $db);
    $res = $this->photo->generate('id', '4fd8b', 10, 15);
    $this->assertFalse($res);
  }

  public function testGenerateCouldNotGetPhotoFromFs()
  {
    $db = $this->getMock('db', array('getPhoto'));
    $db->expects($this->any())
      ->method('getPhoto')
      ->will($this->returnValue(array('pathBase' => 'foo')));
    $fs = $this->getMock('fs', array('getPhoto'));
    $fs->expects($this->any())
      ->method('getPhoto')
      ->will($this->returnValue(false));
    $this->photo->inject('db', $db);
    $this->photo->inject('fs', $fs);
    $res = $this->photo->generate('id', '4fd8b', 10, 15);
    $this->assertFalse($res);
  }

  public function testGenerateCouldNotPutPhotoToFs()
  {
    $db = $this->getMock('db', array('getPhoto','postPhoto'));
    $db->expects($this->any())
      ->method('getPhoto')
      ->will($this->returnValue(array('pathBase' => 'foo', 'dateTaken' => 1234)));
    $db->expects($this->any())
      ->method('postPhoto')
      ->will($this->returnValue(true));
    $fs = $this->getMock('fs', array('getPhoto','putPhoto'));
    $fs->expects($this->any())
      ->method('getPhoto')
      ->will($this->returnValue('somefilename'));
    $fs->expects($this->any())
      ->method('putPhoto')
      ->will($this->returnValue(false));
    $this->photo->inject('db', $db);
    $this->photo->inject('fs', $fs);
    $res = $this->photo->generate('id', '4fd8b', 10, 15);
    $this->assertFalse($res);
  }

  public function testGenerateCouldNotPutPhotoToDb()
  {
    $db = $this->getMock('db', array('getPhoto','postPhoto'));
    $db->expects($this->any())
      ->method('getPhoto')
      ->will($this->returnValue(array('pathBase' => 'foo', 'dateTaken' => 1234)));
    $db->expects($this->any())
      ->method('postPhoto')
      ->will($this->returnValue(false));
    $fs = $this->getMock('fs', array('getPhoto','putPhoto'));
    $fs->expects($this->any())
      ->method('getPhoto')
      ->will($this->returnValue('somefilename'));
    $fs->expects($this->any())
      ->method('putPhoto')
      ->will($this->returnValue(true));
    $this->photo->inject('db', $db);
    $this->photo->inject('fs', $fs);
    $res = $this->photo->generate('id', '4fd8b', 10, 15);
    $this->assertFalse($res);
  }

  public function testGenerateSuccess()
  {
    $db = $this->getMock('db', array('getPhoto','postPhoto'));
    $db->expects($this->any())
      ->method('getPhoto')
      ->will($this->returnValue(array('pathBase' => 'foo', 'dateTaken' => 1234)));
    $db->expects($this->any())
      ->method('postPhoto')
      ->will($this->returnValue(true));
    $fs = $this->getMock('fs', array('getPhoto','putPhoto'));
    $fs->expects($this->any())
      ->method('getPhoto')
      ->will($this->returnValue('somefilename'));
    $fs->expects($this->any())
      ->method('putPhoto')
      ->will($this->returnValue(true));
    $this->photo->inject('db', $db);
    $this->photo->inject('fs', $fs);
    $res = $this->photo->generate('id', '4fd8b', 10, 15);
    $this->assertEquals('somefilename', $res);
  }

  public function testGenerateFragmentReverse()
  {
    $res = $this->photo->generateFragmentReverse('10x15');
    $this->assertEquals(10, $res['width'], 'width not correct');
    $this->assertEquals(15, $res['height'], 'height not correct');
    $this->assertEquals('', $res['options'], 'height not correct');
  }

  public function testGenerateFragmentReverseWithOption()
  {
    $res = $this->photo->generateFragmentReverse('10x15xCR');
    $this->assertEquals(10, $res['width'], 'width not correct');
    $this->assertEquals(15, $res['height'], 'height not correct');
    $this->assertEquals('CR', $res['options'], 'height not correct');
  }

  public function testGenerateHash()
  {
    $res = $this->photo->generateHash();
    $this->assertEquals(false, $res, 'hash for one two three is incorrect');

    $res = $this->photo->generateHash('one');
    $this->assertEquals('65586', $res, 'hash for one two three is incorrect');

    $res = $this->photo->generateHash('one','two','three');
    $this->assertEquals('52fd8', $res, 'hash for one two three is incorrect');
  }

  public function testGeneratePaths()
  {
    // This *should* work
    $now = time();
    $ym = date('Ym', strtotime('1/1/2000'));
    $res = $this->photo->generatePaths('foobar.jpg', strtotime('1/1/2000'));
    $this->assertNotEquals("/original/{$ym}/{$now}-foobar.jpg", $res['pathOriginal'], 'original path not correct, if it is a timestamp mismatch - ignore');
    $this->assertTrue(preg_match("#/original/{$ym}/foobar-[a-z0-9]{13}\.jpg#", $res['pathOriginal']) == 1, 'original path not correct, if it is a timestamp mismatch - ignore');
    $this->assertTrue(preg_match("#/base/{$ym}/foobar-[a-z0-9]{6}\.jpg#", $res['pathBase']) == 1, 'base path not correct, if it is a timestamp mismatch - ignore');
  }

  public function testGenerateUrlBase()
  {
    $user = $this->getMock('Url', array('isOwner'));
    $user->expects($this->any())
      ->method('isOwner')
      ->will($this->returnValue(true));
    $this->photo->inject('user', $user);

    $res = $this->photo->generateUrlBaseOrOriginal($this->photoData, 'base');
    $this->assertEquals('http://host/path/base', $res);
    $res = $this->photo->generateUrlBaseOrOriginal($this->photoData, 'base', 'https');
    $this->assertEquals('https://host/path/base', $res);
  }

  public function testGenerateUrlOriginal()
  {
    $user = $this->getMock('Url', array('isOwner'));
    $user->expects($this->any())
      ->method('isOwner')
      ->will($this->returnValue(true));
    $this->photo->inject('user', $user);

    $res = $this->photo->generateUrlBaseOrOriginal($this->photoData, 'original');
    $this->assertEquals('http://host/path/original', $res);
    $res = $this->photo->generateUrlBaseOrOriginal($this->photoData, 'original', 'https');
    $this->assertEquals('https://host/path/original', $res);
  }

  public function testGenerateUrlPublicWhenStaticAssetExists()
  {
    $res = $this->photo->generateUrlPublic($this->photoData, 10, 10);
    $this->assertEquals('http://host/path/foo10x10', $res);
  }

  public function testGenerateUrlPublicWhenStaticAssetDoesNotExist()
  {
    $res = $this->photo->generateUrlPublic($this->photoData, 10, 100);
    $this->assertEquals('http://foobar/photo/a/create/1e34d/10x100.jpg', $res);
  }

  public function testGenerateUrlInternal()
  {
    $res = $this->photo->generateUrlInternal('id', 10, 10);
    $this->assertEquals('/photo/id/create/ae66f/10x10.jpg', $res);

    $res = $this->photo->generateUrlInternal('id', 10, 10, 'CR');
    $this->assertEquals('/photo/id/create/43eb8/10x10xCR.jpg', $res);
  }

  public function testGetRealDimensionsSquareWhenLandscape()
  {
    $res = $this->photo->getRealDimensions(200, 100, 50, 50);
    $this->assertEquals($res, array('width'=>50, 'height'=>25));
  }

  public function testGetRealDimensionsSquareWhenHorizontal()
  {
    $res = $this->photo->getRealDimensions(100, 200, 50, 50);
    $this->assertEquals($res, array('width'=>25, 'height'=>50));
  }

  public function testGetRealDimensionsSquareWhenSquare()
  {
    $res = $this->photo->getRealDimensions(100, 100, 50, 50);
    $this->assertEquals($res, array('width'=>50, 'height'=>50));
  }

  public function testGetRealDimensionsSquareWhenLarger()
  {
    $res = $this->photo->getRealDimensions(100, 100, 200, 500);
    $this->assertEquals($res, array('width'=>200, 'height'=>200));
  }
}
