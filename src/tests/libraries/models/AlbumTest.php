<?php
class AlbumTest extends PHPUnit_Framework_TestCase
{
  public function setUp()
  {
    $this->album = new Album();
  }

  public function testAddElementSuccess()
  {
    $db = $this->getMock('Db', array('postAlbumAdd'));
    $db->expects($this->any())
      ->method('postAlbumAdd')
      ->will($this->returnValue(true));
    $this->album->inject('db', $db);
    $res = $this->album->addElement('a','b','c');
    $this->assertTrue($res);
  }

  /**
  * @expectedException PHPUnit_Framework_Error_Warning
  */
  public function testAddElementTooFewParametersFailure()
  {
    $db = $this->getMock('Db', array('postAlbumAdd'));
    $db->expects($this->any())
      ->method('postAlbumAdd')
      ->will($this->returnValue(true));
    $this->album->inject('db', $db);
    $res = $this->album->addElement('a');
    $this->assertTrue($res);
  }

  public function testCreateSuccess()
  {
    $user = $this->getMock('User', array('getNextId'));
    $user->expects($this->any())
      ->method('getNextId')
      ->will($this->returnValue('a'));
    $db = $this->getMock('Db', array('putAlbum'));
    $db->expects($this->any())
      ->method('putAlbum')
      ->will($this->returnValue(true));
    $this->album->inject('db', $db);
    $this->album->inject('user', $user);
    $res = $this->album->create(array('foo' => 'bar'));
    $this->assertEquals('a', $res, "Next album id not returned from call to create()");
  }

  public function testCreateNextIdFailure()
  {
    $user = $this->getMock('User', array('getNextId'));
    $user->expects($this->any())
      ->method('getNextId')
      ->will($this->returnValue(false));
    $this->album->inject('user', $user);
    $res = $this->album->create(array('foo' => 'bar'));
    $this->assertFalse($res, "If nextId call fails create() should return false");
  }

  public function testCreatePostFailure()
  {
    $user = $this->getMock('User', array('getNextId'));
    $user->expects($this->any())
      ->method('getNextId')
      ->will($this->returnValue('a'));
    $db = $this->getMock('Db', array('putAlbum'));
    $db->expects($this->any())
      ->method('putAlbum')
      ->will($this->returnValue(false));
    $this->album->inject('db', $db);
    $this->album->inject('user', $user);
    $res = $this->album->create(array('foo' => 'bar'));
    $this->assertFalse($res, "If putAlbum fails then create() should return false");
  }

  public function testDeleteSuccess()
  {
    $db = $this->getMock('Db', array('deleteAlbum'));
    $db->expects($this->any())
      ->method('deleteAlbum')
      ->will($this->returnValue('some_random_value'));
    $this->album->inject('db', $db);
    $res = $this->album->delete('abc');
    $this->assertEquals('some_random_value', $res, "delete() should return whatever deleteAlbum() returns");
  }

  /**
  * @expectedException PHPUnit_Framework_Error_Warning
  */
  public function testDeleteTooFewParametersFailure()
  {
    $res = $this->album->delete();
    $this->assertEquals('some_random_value', $res, "delete() should return whatever deleteAlbum() returns");

  }

  public function testGetAlbumSuccess()
  {
    $expected = array('id' => 'foo', 'cover' => '123');
    $user = $this->getMock('User', array('getEmailAddress','isOwner'));
    $user->expects($this->any())
      ->method('getEmailAddress')
      ->will($this->returnValue('user@example.com'));
    $user->expects($this->any())
      ->method('isOwner')
      ->will($this->returnValue(true));
    $db = $this->getMock('Db', array('getAlbum'));
    $db->expects($this->any())
      ->method('getAlbum')
      ->will($this->returnValue($expected));
    $this->album->inject('user', $user);
    $this->album->inject('db', $db);
    $res = $this->album->getAlbum('abc', false);
    $this->assertEquals($expected, $res, "getAlbum should return an array album object");
  }

  public function testGetAlbumCoverNotVisibleSuccess()
  {
    $expected = array('id' => 'foo', 'cover' => array('id' => '123'));
    $user = $this->getMock('User', array('getEmailAddress','isOwner'));
    $user->expects($this->any())
      ->method('getEmailAddress')
      ->will($this->returnValue('user@example.com'));
    $user->expects($this->any())
      ->method('isOwner')
      ->will($this->returnValue(false));
    $db = $this->getMock('Db', array('getAlbum'));
    $db->expects($this->any())
      ->method('getAlbum')
      ->will($this->returnValue($expected));
    $api = $this->getMock('Api', array('invoke'));
    $api->expects($this->any())
      ->method('invoke')
      ->will($this->returnValue(array('code' => 403)));
    $this->album->inject('user', $user);
    $this->album->inject('db', $db);
    $this->album->inject('api', $api);
    $res = $this->album->getAlbum('abc', false);
    // cover should be removed
    $this->assertNull($res['cover'], "Covers which are private should not be returned with insufficient privileges");
  }

  public function testGetAlbumCoverNotVisiblePhotoApi500Success()
  {
    $expected = array('id' => 'foo', 'cover' => array('id' => '123'));
    $user = $this->getMock('User', array('getEmailAddress','isOwner'));
    $user->expects($this->any())
      ->method('getEmailAddress')
      ->will($this->returnValue('user@example.com'));
    $user->expects($this->any())
      ->method('isOwner')
      ->will($this->returnValue(false));
    $db = $this->getMock('Db', array('getAlbum'));
    $db->expects($this->any())
      ->method('getAlbum')
      ->will($this->returnValue($expected));
    $api = $this->getMock('Api', array('invoke'));
    $api->expects($this->any())
      ->method('invoke')
      ->will($this->returnValue(array('code' => 500)));
    $this->album->inject('user', $user);
    $this->album->inject('db', $db);
    $this->album->inject('api', $api);
    $res = $this->album->getAlbum('abc', false);
    // cover should be removed
    $this->assertNull($res['cover'], "Covers which are private should not be returned with insufficient privileges");
  }

  public function testGetAlbumFailure()
  {
    $user = $this->getMock('User', array('getEmailAddress','isOwner'));
    $user->expects($this->any())
      ->method('getEmailAddress')
      ->will($this->returnValue('user@example.com'));
    $user->expects($this->any())
      ->method('isOwner')
      ->will($this->returnValue(false));
    $db = $this->getMock('Db', array('getAlbum'));
    $db->expects($this->any())
      ->method('getAlbum')
      ->will($this->returnValue(false));
    $this->album->inject('user', $user);
    $this->album->inject('db', $db);
    $res = $this->album->getAlbum('abc', false);
    $this->assertFalse($res, "When db::getAlbum returns false model should too");
  }

  public function testGetAlbumElementsSuccess()
  {
    $expected = array('id' => 'foo', 'cover' => '123');
    $user = $this->getMock('User', array('getEmailAddress','isOwner'));
    $user->expects($this->any())
      ->method('getEmailAddress')
      ->will($this->returnValue('user@example.com'));
    $user->expects($this->any())
      ->method('isOwner')
      ->will($this->returnValue(true));
    $db = $this->getMock('Db', array('getAlbum'));
    $db->expects($this->any())
      ->method('getAlbum')
      ->will($this->returnValue($expected));
    $this->album->inject('user', $user);
    $this->album->inject('db', $db);
    $res = $this->album->getAlbum('abc', false);
    $this->assertEquals($expected, $res, "getAlbum should return an array album object");
  }

  public function testGetAlbumsSuccess()
  {
    $expected = array(
      array('id' => 'foo', 'cover' => '123'),
      array('id' => 'bar', 'cover' => '456')
    );
    $user = $this->getMock('User', array('getEmailAddress','isOwner'));
    $user->expects($this->any())
      ->method('getEmailAddress')
      ->will($this->returnValue('user@example.com'));
    $user->expects($this->any())
      ->method('isOwner')
      ->will($this->returnValue(true));
    $db = $this->getMock('Db', array('getAlbums'));
    $db->expects($this->any())
      ->method('getAlbums')
      ->will($this->returnValue($expected));
    $this->album->inject('user', $user);
    $this->album->inject('db', $db);
    $res = $this->album->getAlbums('abc', false);
    $this->assertEquals($expected, $res);
  }

  public function testGetAlbumsCoverNotVisibleSuccess()
  {
    $expected = array(
      array('id' => 'foo', 'cover' => array('id' => '123')),
      array('id' => 'bar', 'cover' => array('id' => '456'))
    );
    $user = $this->getMock('User', array('getEmailAddress','isOwner'));
    $user->expects($this->any())
      ->method('getEmailAddress')
      ->will($this->returnValue('user@example.com'));
    $user->expects($this->any())
      ->method('isOwner')
      ->will($this->returnValue(false));
    $db = $this->getMock('Db', array('getAlbums'));
    $db->expects($this->any())
      ->method('getAlbums')
      ->will($this->returnValue($expected));
    $api = $this->getMock('Api', array('invoke'));
    $api->expects($this->any())
      ->method('invoke')
      ->will($this->returnValue(array('code' => 403)));
    $this->album->inject('user', $user);
    $this->album->inject('db', $db);
    $this->album->inject('api', $api);
    $res = $this->album->getAlbums('abc', false);
    $this->assertNull($res[0]['cover'], "Cover should be null for all album objects");
    $this->assertNull($res[1]['cover'], "Cover should be null for all album objects");
  }

  public function testGetAlbumsFailure()
  {
    $user = $this->getMock('User', array('getEmailAddress','isOwner'));
    $user->expects($this->any())
      ->method('getEmailAddress')
      ->will($this->returnValue('user@example.com'));
    $user->expects($this->any())
      ->method('isOwner')
      ->will($this->returnValue(false));
    $db = $this->getMock('Db', array('getAlbums'));
    $db->expects($this->any())
      ->method('getAlbums')
      ->will($this->returnValue(false));
    $this->album->inject('user', $user);
    $this->album->inject('db', $db);
    $res = $this->album->getAlbums('abc', false);
    $this->assertFalse($res, "When db::getAlbums returns false model should too");
  }

  public function testRemoveElement()
  {
    $db = $this->getMock('Db', array('postAlbumRemove'));
    $db->expects($this->any())
      ->method('postAlbumRemove')
      ->will($this->returnValue('some_random_value'));
    $this->album->inject('db', $db);
    $res = $this->album->removeElement(1,2,3);
    $this->assertEquals('some_random_value', $res);
  }

  /**
  * @expectedException PHPUnit_Framework_Error_Warning
  */
  public function testUpdateInvalidParamsFailure()
  {
    $db = $this->getMock('Db', array('postAlbum'));
    $db->expects($this->any())
      ->method('postAlbum')
      ->will($this->returnValue('some_random_value'));
    $this->album->inject('db', $db);
    $res = $this->album->update(1,2);
  }

  public function testUpdate()
  {
    $db = $this->getMock('Db', array('postAlbum'));
    $db->expects($this->any())
      ->method('postAlbum')
      ->will($this->returnValue('some_random_value'));
    $this->album->inject('db', $db);
    $res = $this->album->update(1,array('foo' => 'bar'));
    $this->assertEquals('some_random_value', $res);
  }
}
