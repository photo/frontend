<?php
$baseDir = dirname(dirname(dirname(dirname(__FILE__))));
class DatabaseMySqlOverride extends DatabaseMySql
{
  public function __construct($config = null, $params = null)
  {
    parent::__construct($config, $params);
  }

  public function getBatchRequest()
  {
    return null;
  }

  public function getAlbumsFromGroups($groups)
  {
    return parent::getAlbumsFromGroups($groups);
  }

  protected function getActor()
  {
    return 'test@example.com';
  }

  protected function setActiveFieldForAlbumsFromElement($id, $value)
  {
    return true;
  }

  protected function setActiveFieldForTagsFromElement($id, $value)
  {
    return true;
  }
}

class DatabaseMySqlTest extends PHPUnit_Framework_TestCase
{
  public function setUp()
  {
    $config = array(
      'mysql' => array('mySqlDb' => 'foo', 'mySqlHost' => 'bar', 'mySqlUser' => 'foo', 'mySqlPassword' => 'bar'),
      'user' => array('email' => 'test@example.com'),
      'application' => array('appId' => 'fooId')
    );
    $config = arrayToObject($config);
    $params = array('db' => true);
    $this->db = new DatabaseMySqlOverride($config, $params);
    $this->db->inject('isAdmin', true);
  }

  public function testDeleteActionSuccess()
  {
    $db = $this->getMock('MySqlMock', array('execute'));
    $db->expects($this->any())
      ->method('execute')
      ->will($this->returnValue(true));
    $this->db->inject('db', $db);

    $res = $this->db->deleteAction('foo');
    $this->assertTrue($res, 'The MySql adapter did not return TRUE for deleteAction');
  }

  public function testDeleteActionFailure()
  {
    $db = $this->getMock('MySqlMock', array('execute'));
    $db->expects($this->any())
      ->method('execute')
      ->will($this->returnValue(false));
    $this->db->inject('db', $db);

    $res = $this->db->deleteAction('foo');
    $this->assertFalse($res, 'The MySql adapter did not return FALSE for deleteAction');
  }

  public function testDeleteAlbumSuccess()
  {
    $db = $this->getMock('MySqlMock', array('execute'));
    $db->expects($this->any())
      ->method('execute')
      ->will($this->returnValue(true));
    $this->db->inject('db', $db);

    $res = $this->db->deleteAlbum('foo');
    $this->assertTrue($res, 'The MySql adapter did not return TRUE for deleteAlbum');
  }

  public function testDeleteAlbumFailure()
  {
    $db = $this->getMock('MySqlMock', array('execute'));
    $db->expects($this->any())
      ->method('execute')
      ->will($this->returnValue(false));
    $this->db->inject('db', $db);

    $res = $this->db->deleteAlbum('foo');
    $this->assertFalse($res, 'The MySql adapter did not return FALSE for deleteAlbum');
  }


  public function testDeletePhotoSuccess()
  {
    $db = $this->getMock('MySqlMock', array('execute'));
    $db->expects($this->any())
      ->method('execute')
      ->will($this->returnValue(true));
    $this->db->inject('db', $db);

    $res = $this->db->deletePhoto(array('id' => 1));
    $this->assertTrue($res, 'The MySql adapter did not return TRUE for deletePhoto');
  }

  public function testDeletePhotoFailure()
  {
    $db = $this->getMock('MySqlMock', array('execute'));
    $db->expects($this->any())
      ->method('execute')
      ->will($this->returnValue(false));
    $this->db->inject('db', $db);

    $res = $this->db->deletePhoto(array());
    $this->assertFalse($res, 'The MySql adapter did not return FALSE for deletePhoto');
  }

  public function testDeletePhotoFailureWhenNoId()
  {
    $res = $this->db->deletePhoto(array());
    $this->assertFalse($res, 'The MySql adapter did not return TRUE for deletePhoto when no id attribute exiss');
  }

  public function testGetAlbumSuccess()
  {
    $db = $this->getMock('MySqlMock', array('one'));
    $db->expects($this->any())
      ->method('one')
      ->will($this->returnValue(array('name' => 'unittest', 'countPublic' => '1')));
    $this->db->inject('db', $db);

    $res = $this->db->getAlbum('foo', 'test@example.com');
    $this->assertEquals($res['name'], 'unittest', 'The MySql adapter did not return the album name for getAlbum');
  }

  public function testGetAlbumWithExtraSuccess()
  {
    $db = $this->getMock('MySqlMock', array('one'));
    $db->expects($this->any())
      ->method('one')
      ->will($this->returnValue(array('name' => 'unittest', 'countPublic' => '1', 'extra' => json_encode(array('cover' => 'foo')))));
    $this->db->inject('db', $db);

    $res = $this->db->getAlbum('foo', 'test@example.com');
    $this->assertEquals($res['cover'], 'foo', 'The MySql adapter did not return the album name for getAlbum with extra');
  }

  public function testGetAlbumPrivateOnlyFailure()
  {
    $db = $this->getMock('MySqlMock', array('one'));
    $db->expects($this->any())
      ->method('one')
      ->will($this->returnValue(array('name' => 'unittest', 'countPublic' => '0')));
    $this->db->inject('db', $db);
    $this->db->inject('isAdmin', false);

    $res = $this->db->getAlbum('foo', 'email');
    $this->assertFalse($res, 'The MySql adapter should return false when countPublic is 0 and non admin');
  }

  public function testGetAlbumFailure()
  {
    $db = $this->getMock('MySqlMock', array('one'));
    $db->expects($this->any())
      ->method('one')
      ->will($this->returnValue(false));
    $this->db->inject('db', $db);

    $res = $this->db->getAlbum('foo', 'email');
    $this->assertFalse($res, 'The MySql adapter did not return FALSE for getAlbum');
  }

  public function testGetAlbumElementsSuccess()
  {
    $db = $this->getMock('MySqlMock', array('all'));
    $db->expects($this->any())
      ->method('all')
      ->will($this->onConsecutiveCalls(
        MySqlMockHelper::getPhotos(2),
        array(array('key' => 'id', 'path' => 'bar')), // getPhotoVersions
        array(array('key' => 'id', 'path' => 'bar')) // getPhotoVersions
      ));
    $this->db->inject('db', $db);

    $res = $this->db->getAlbumElements('id');
    $this->assertEquals(2, count($res));
    $this->assertEquals($res[0]['id'], 'bar');
  }

  public function testGetAlbumElementsFailure()
  {
    $db = $this->getMock('MySqlMock', array('all'));
    $db->expects($this->any())
      ->method('all')
      ->will($this->returnValue(false));
    $this->db->inject('db', $db);

    $res = $this->db->getAlbumElements('id');
    $this->assertFalse($res);
  }

  public function testGetAlbumByNameSuccess()
  {
    $db = $this->getMock('MySqlMock', array('one'));
    $db->expects($this->any())
      ->method('one')
      ->will($this->returnValue(array('name' => 'unittest', 'countPublic' => '1')));
    $this->db->inject('db', $db);

    $res = $this->db->getAlbum('foo', 'test@example.com');
    $this->assertEquals($res['name'], 'unittest', 'The MySql adapter did not return the album name for getAlbum');
  }

  public function testGetAlbumsSuccess()
  {
    $db = $this->getMock('MySqlMock', array('all','one'));
    $db->expects($this->any())
      ->method('all')
      ->will($this->returnValue(array(array('name' => 'unittest'))));
    $db->expects($this->any())
      ->method('one')
      ->will($this->returnValue(array('COUNT(*)' => 2)));
    $this->db->inject('db', $db);

    $res = $this->db->getAlbums('email');
    $this->assertEquals($res[0]['name'], 'unittest');
    $this->assertEquals($res[0]['totalRows'], 2);
  }

  public function testGetAlbumsWithExtrasSuccess()
  {
    $db = $this->getMock('MySqlMock', array('all','one'));
    $db->expects($this->any())
      ->method('all')
      ->will($this->returnValue(array(
        array('name' => 'unittest', 'extra' => json_encode(array('cover' => 'foo'))),
        array('name' => 'unittest', 'extra' => json_encode(array('cover' => 'foo'))),
    )));
    $db->expects($this->any())
      ->method('one')
      ->will($this->returnValue(array('COUNT(*)' => 2)));
    $this->db->inject('db', $db);

    $res = $this->db->getAlbums('email');
    $this->assertEquals($res[0]['name'], 'unittest');
    $this->assertEquals($res[0]['cover'], 'foo', 'The MySql adapter did not return the album name for getAlbum with extra');
    $this->assertEquals($res[0]['totalRows'], 2);
  }

  public function testGetCredentialSuccess()
  {
    $db = $this->getMock('MySqlMock', array('one'));
    $db->expects($this->any())
      ->method('one')
      ->will($this->returnValue(array('name' => 'unittest')));
    $this->db->inject('db', $db);

    $res = $this->db->getCredential('foo');
    $this->assertEquals($res['name'], 'unittest', 'The MySql adapter did not return the credential name for getCredential');
  }

  public function testGetCredentialFailure()
  {
    $db = $this->getMock('MySqlMock', array('one'));
    $db->expects($this->any())
      ->method('one')
      ->will($this->returnValue(false));
    $this->db->inject('db', $db);

    $res = $this->db->getCredential('foo');
    $this->assertFalse($res, 'The MySql adapter did not return FALSE for getCredential');
  }

  public function testGetGroupsSuccess()
  {
    $db = $this->getMock('MySqlMock', array('all'));
    $db->expects($this->any())
      ->method('all')
      ->will($this->returnValue(array(
        array('id' => 'one', 'name' => 'two', 'owner' => 'owner', 'permission' => '0'),
        array('id' => 'two', 'name' => 'two', 'owner' => 'owner', 'permission' => '0')
      )));
    $this->db->inject('db', $db);

    $res = $this->db->getGroups('foo');
    $this->assertEquals(count($res), 2, 'The MySql adapter did not return exactly two groups for getGroups');
  }

  public function testGetGroupsFailure()
  {
    $db = $this->getMock('MySqlMock', array('all'));
    $db->expects($this->any())
      ->method('all')
      ->will($this->returnValue(false));
    $this->db->inject('db', $db);

    $res = $this->db->getGroups('foo');
    $this->assertFalse($res, 'The MySql adapter did not return FALSE for getGroups');
  }

  public function testGetPhotoSuccess()
  {
    $db = $this->getMock('MySqlMock', array('one','all'));
    $db->expects($this->any())
      ->method('one')
      ->will($this->returnValue(MySqlMockHelper::getPhoto()));
    // photo versions
    $db->expects($this->any())
      ->method('all')
      ->will($this->returnValue(array(array('key' => 'id', 'path' => 'foo')))); // getPhotoVersions
    $this->db->inject('db', $db);

    $res = $this->db->getPhoto('foo');
    $this->assertEquals($res['id'], 'foo', 'The MySql adapter did not return "foo" for getPhoto');
  }

  public function testGetPhotoFailure()
  {
    $db = $this->getMock('MySqlMock', array('one'));
    $db->expects($this->any())
      ->method('one')
      ->will($this->returnValue(false));
    $this->db->inject('db', $db);

    $res = $this->db->getPhoto('foo');
    $this->assertFalse($res, 'The MySql adapter did not return FALSE for getPhoto');
  }

  // TODO getPhoto with empty exif/extra/tags/groups

  // TODO
  public function testGetPhotoNextPreviousSuccess()
  {
    // This is too difficult to test.
    // Not worth the time.
  }

  public function testGetPhotoNextPreviousFailure()
  {
    $db = $this->getMock('MySqlMock', array('one'));
    $db->expects($this->any())
      ->method('all')
      ->will($this->returnValue(false));
    $this->db->inject('db', $db);

    $res = $this->db->getPhotoNextPrevious('foo');
    $this->assertFalse($res, 'The MySql adapter did not return FALSE for getPhotoNextPrevious');
  }

  // TODO next/previous makes 3 queries we should test failure against each

  // TODO
  public function testGetPhotosSuccess()
  {
    $db = $this->getMock('MySqlMock', array('all','one'));
    $db->expects($this->at(0))
      ->method('all')
      ->will($this->returnValue(MySqlMockHelper::getPhotos(2)));
    $db->expects($this->any())
      ->method('all')
      ->will($this->returnValue(array()));
    $db->expects($this->any())
      ->method('one')
      ->will($this->returnValue(array('COUNT(*)'=>2)));
    $this->db->inject('db', $db);

    $res = $this->db->getPhotos();
    $this->assertEquals(count($res), 2, 'The MySql adapter did not return 2 photos for getPhotos');
  }

  public function testGetShareTokenSuccess()
  {
    $db = $this->getMock('MySqlMock', array('one'));
    $db->expects($this->any())
      ->method('one')
      ->will($this->returnValue(MySqlMockHelper::getShareToken()));
    $this->db->inject('db', $db);

    $res = $this->db->getShareToken('foo');
    $this->assertTrue($res !== false);
    $this->assertEquals($res['id'], 'foo');
  }

  public function testGetShareTokenNotExistsFailure()
  {
    $db = $this->getMock('MySqlMock', array('one'));
    $db->expects($this->any())
      ->method('one')
      ->will($this->returnValue(false));
    $this->db->inject('db', $db);

    $res = $this->db->getShareToken('foo');
    $this->assertFalse($res);
  }

  public function testGetShareTokenNotExpiredSuccess()
  {
    $db = $this->getMock('MySqlMock', array('one'));
    $db->expects($this->any())
      ->method('one')
      ->will($this->returnValue(MySqlMockHelper::getShareToken(array('dateExpires' => time()+100))));
    $this->db->inject('db', $db);

    $res = $this->db->getShareToken('foo');
    $this->assertTrue($res !== false);
  }

  public function testGetShareTokenExpiredFailure()
  {
    $db = $this->getMock('MySqlMock', array('one'));
    $db->expects($this->any())
      ->method('one')
      ->will($this->returnValue(MySqlMockHelper::getShareToken(array('dateExpires' => 1))));
    $this->db->inject('db', $db);

    $res = $this->db->getShareToken('foo');
    $this->assertFalse($res);
  }

  public function testGetTagsSuccess()
  {
    $db = $this->getMock('MySqlMock', array('all'));
    $db->expects($this->any())
      ->method('all')
      ->will($this->returnValue(MySqlMockHelper::getTags(2)));
    $this->db->inject('db', $db);

    $res = $this->db->getTags();
    $this->assertEquals(count($res), 2, 'The MySql adapter did not return exactly 2 tags for getTags');
    $this->assertEquals($res[0]['id'], 'foo', 'The MySql adapter did not return "foo0" as the first tag for getTags');
    $this->assertEquals($res[1]['id'], 'foo', 'The MySql adapter did not return "foo1" as the second tag for getTags');
  }

  public function testGetTagsFailure()
  {
    $db = $this->getMock('MySqlMock', array('all'));
    $db->expects($this->any())
      ->method('all')
      ->will($this->returnValue(false));
    $this->db->inject('db', $db);

    $res = $this->db->getTags();
    $this->assertFalse($res, 'The MySql adapter did not return FALSE for getTags');
  }

  public function testGetUserSuccess()
  {
    $db = $this->getMock('MySqlMock', array('one'));
    $db->expects($this->any())
      ->method('one')
      ->will($this->returnValue(MySqlMockHelper::getUser()));
    $this->db->inject('db', $db);

    $res = $this->db->getUser();
    $this->assertEquals($res['id'], 'foo', 'The MySql adapter did not return "foo" as the id for getUser');
    $this->assertEquals($res['lastPhotoId'], 'abc', 'The MySql adapter did not return "abc" as the lastPhotoId for getUser');
  }

  public function testGetUserCacheSuccess()
  {
    $db = $this->getMock('MySqlMock', array('one'));
    $db->expects($this->once())
      ->method('one')
      ->will($this->returnValue(MySqlMockHelper::getUser()));
    $user2 = MySqlMockHelper::getUser();
    $user2['id'] = 'user2';
    $db->expects($this->once())
      ->method('one')
      ->will($this->returnValue($user2));

    $this->db->inject('db', $db);

    $res = $this->db->getUser();
    $this->assertEquals($res['id'], 'foo', 'The MySql adapter did not return "foo" as the id for getUser');
    
    // 2nd call should return first value
    $res = $this->db->getUser();
    $this->assertEquals($res['id'], 'foo', 'The MySql adapter did not return "foo" as the id for getUser in cached call');
  }

  public function testGetUserCacheClearSuccess()
  {
    $db = $this->getMock('MySqlMock', array('one','execute'));
    $db->expects($this->any())
      ->method('execute')
      ->will($this->returnValue(1));
    $db->expects($this->at(0))
      ->method('one')
      ->will($this->returnValue(MySqlMockHelper::getUser()));
    $user2 = MySqlMockHelper::getUser();
    $user2['lastPhotoId'] = 'xyz';
    // here we set the index to 2 because postUser calls execute on this mock
    $db->expects($this->at(2))
      ->method('one')
      ->will($this->returnValue($user2));

    $this->db->inject('db', $db);

    $res = $this->db->getUser();
    $this->assertEquals($res['lastPhotoId'], 'abc', 'The MySql adapter did not return "abc" as the id for lastPhotoId');

    // this should clear the cache
    $this->db->postUser(array('id' => 'bar'));
    
    // 2nd call should return second value
    $res = $this->db->getUser();
    $this->assertEquals($res['lastPhotoId'], 'xyz', 'The MySql adapter did not return "xyz" as the lastPhotoId since cache should be  cleared');
  }

  public function testGetUserFailure()
  {
    $db = $this->getMock('MySqlMock', array('one'));
    $db->expects($this->any())
      ->method('one')
      ->will($this->returnValue(false));
    $this->db->inject('db', $db);

    $res = $this->db->getUser();
    $this->assertTrue(is_null($res), 'The MySql adapter did not return NULL for getUser');
  }

  // TEST for false return on error

  public function testInitializeSuccess()
  {
    $db = $this->getMock('MySqlMock', array('one'));
    $db->expects($this->at(0))
      ->method('one')
      ->will($this->returnValue(array('value' => '1.0.0')));
    $db->expects($this->at(1))
      ->method('one')
      ->will($this->returnValue(null));
    $this->db->inject('db', $db);

    $res = $this->db->initialize(false);
    $this->assertTrue($res, 'The MySql adapter did not return TRUE for initialize when seeded with existing domains');
  }

  public function testInitializeFailure()
  {
    $db = $this->getMock('MySqlMock', array('one'));
    $db->expects($this->at(0))
      ->method('one')
      ->will($this->returnValue(array('value' => '1.0.0')));
    $db->expects($this->at(1))
      ->method('one')
      ->will($this->returnValue(MySqlMockHelper::getUser()));
    $this->db->inject('db', $db);

    $res = $this->db->initialize(false);
    $this->assertFalse($res, 'The MySql adapter did not return FALSE when user already exists');
  }

  public function testPostAlbumSuccess()
  {
    $db = $this->getMock('MySqlMock', array('execute'));
    $db->expects($this->any())
      ->method('execute')
      ->will($this->returnValue(true));
    $this->db->inject('db', $db);

    $res = $this->db->postAlbum('foo', array());
    $this->assertTrue($res, 'The MySql adapter did not return TRUE for postAlbum');
  }

  public function testPostAlbumFailure()
  {
    $db = $this->getMock('MySqlMock', array('execute'));
    $db->expects($this->any())
      ->method('execute')
      ->will($this->returnValue(false));
    $this->db->inject('db', $db);

    $res = $this->db->postAlbum('foo', array());
    $this->assertFalse($res, 'The MySql adapter did not return FALSE for postAlbum');
  }

  public function testPostAlbumAddSuccess()
  {
    $db = $this->getMock('MySqlMock', array('execute'));
    $db->expects($this->any())
      ->method('execute')
      ->will($this->returnValue(true));
    $this->db->inject('db', $db);

    $res = $this->db->postAlbumAdd('id', 'photo', array('foo','bar'));
    $this->assertTrue($res);
  }

  public function testPostAlbumAddFailure()
  {
    $db = $this->getMock('MySqlMock', array('execute'));
    $db->expects($this->any())
      ->method('execute')
      ->will($this->returnValue(0));
    $this->db->inject('db', $db);

    $res = $this->db->postAlbumAdd('id', 'photo', array('foo','bar'));
    $this->assertFalse($res);
  }

  public function testPostAlbumRemoveSuccess()
  {
    $db = $this->getMock('MySqlMock', array('execute'));
    $db->expects($this->any())
      ->method('execute')
      ->will($this->returnValue(true));
    $this->db->inject('db', $db);

    $res = $this->db->postAlbumRemove('id', 'photo', array('foo','bar'));
    $this->assertTrue($res);
  }

  public function testPostAlbumRemoveFailure()
  {
    $db = $this->getMock('MySqlMock', array('execute'));
    $db->expects($this->any())
      ->method('execute')
      ->will($this->returnValue(false));
    $this->db->inject('db', $db);

    $res = $this->db->postAlbumRemove('id', 'photo', array('foo','bar'));
    $this->assertFalse($res);
  }

  public function testPostAlbumsIncrementerSuccess()
  {
    $db = $this->getMock('MySqlMock', array('execute'));
    $db->expects($this->any())
      ->method('execute')
      ->will($this->returnValue(true));
    $this->db->inject('db', $db);

    $res = $this->db->postAlbumsIncrementer(array('id'), 1);
    $this->assertTrue($res);
  }

  public function testPostAlbumsIncrementerFailure()
  {
    $db = $this->getMock('MySqlMock', array('execute'));
    $db->expects($this->any())
      ->method('execute')
      ->will($this->onConsecutiveCalls(
        true,
        false       
    ));
    $this->db->inject('db', $db);

    $res = $this->db->postAlbumsIncrementer(array('id1','id2'), 1);
    $this->assertFalse($res);
  }

  public function testPostCredentialSuccess()
  {
    $db = $this->getMock('MySqlMock', array('execute'));
    $db->expects($this->any())
      ->method('execute')
      ->will($this->returnValue(true));
    $this->db->inject('db', $db);

    $res = $this->db->postCredential('foo', array());
    $this->assertTrue($res, 'The MySql adapter did not return TRUE for postCredential');
  }

  public function testPostCredentialFailure()
  {
    $db = $this->getMock('MySqlMock', array('execute'));
    $db->expects($this->any())
      ->method('execute')
      ->will($this->returnValue(false));
    $this->db->inject('db', $db);

    $res = $this->db->postCredential('foo', array());
    $this->assertFalse($res, 'The MySql adapter did not return FALSE for postCredential');
  }

  public function testPostGroupSuccess()
  {
    $db = $this->getMock('MySqlMock', array('execute'));
    $db->expects($this->any())
      ->method('execute')
      ->will($this->returnValue(true));
    $this->db->inject('db', $db);

    $res = $this->db->postGroup('foo', array());
    $this->assertTrue($res, 'The MySql adapter did not return TRUE for postGroup');
  }

  public function testPostGroupFailure()
  {
    $db = $this->getMock('MySqlMock', array('execute'));
    $db->expects($this->any())
      ->method('execute')
      ->will($this->returnValue(false));
    $this->db->inject('db', $db);

    $res = $this->db->postGroup('foo', array());
    $this->assertFalse($res, 'The MySql adapter did not return FALSE for postGroup');
  }

  public function testPostPhotoSuccess()
  {
    $db = $this->getMock('MySqlMock', array('execute'));
    $db->expects($this->any())
      ->method('execute')
      ->will($this->returnValue(true));
    $this->db->inject('db', $db);

    $res = $this->db->postPhoto('foo', array('bar'=>'huh'));
    $this->assertTrue($res, 'The MySql adapter did not return TRUE for postPhoto');
  }

  public function testPostPhotoSuccessNoParams()
  {
    $db = $this->getMock('MySqlMock', array('execute'));
    $db->expects($this->any())
      ->method('execute')
      ->will($this->returnValue(true));
    $this->db->inject('db', $db);

    $res = $this->db->postPhoto('foo', array());
    $this->assertTrue($res, 'The MySql adapter did not return TRUE for postPhoto');
  }

  public function testPostPhotoFailure()
  {
    $db = $this->getMock('MySqlMock', array('execute'));
    $db->expects($this->any())
      ->method('execute')
      ->will($this->returnValue(false));
    $this->db->inject('db', $db);

    $res = $this->db->postPhoto('foo', array('bar'=>'huh'));
    $this->assertFalse($res, 'The MySql adapter did not return FALSE for postPhoto');
  }

  public function testPostTagSuccess()
  {
    $db = $this->getMock('MySqlMock', array('execute'));
    $db->expects($this->any())
      ->method('execute')
      ->will($this->returnValue(true));
    $this->db->inject('db', $db);

    $res = $this->db->postTag('foo', array());
    $this->assertTrue($res, 'The MySql adapter did not return TRUE for postTag');
  }

  public function testPostTagFailure()
  {
    $db = $this->getMock('MySqlMock', array('execute'));
    $db->expects($this->any())
      ->method('execute')
      ->will($this->returnValue(false));
    $this->db->inject('db', $db);

    $res = $this->db->postTag('foo', array());
    $this->assertFalse($res, 'The MySql adapter did not return FALSE for postTag');
  }

  public function testPostTagsSuccess()
  {
    $db = $this->getMock('MySqlMock', array('execute'));
    $db->expects($this->any())
      ->method('execute')
      ->will($this->returnValue(true));
    $this->db->inject('db', $db);
    
    $res = $this->db->postTags(array(array('id' => 'foo')));
    $this->assertTrue($res, 'The MySql adapter did not return TRUE for postTags');
  }

  public function testPostTagsFailure()
  {
    $db = $this->getMock('MySqlMock', array('execute'));
    $db->expects($this->any())
      ->method('execute')
      ->will($this->returnValue(false));
    $this->db->inject('db', $db);

    $res = $this->db->postTags(array(array('id' => 'foo')));
    $this->assertFalse($res, 'The MySql adapter did not return FALSE for postTags');
  }

  public function testPostUserSuccess()
  {
    $db = $this->getMock('MySqlMock', array('execute'));
    $db->expects($this->any())
      ->method('execute')
      ->will($this->returnValue(true));
    $this->db->inject('db', $db);

    $res = $this->db->postUser(array());
    $this->assertTrue($res, 'The MySql adapter did not return TRUE for postUser');
  }

  public function testPostUserFailure()
  {
    $db = $this->getMock('MySqlMock', array('execute'));
    $db->expects($this->any())
      ->method('execute')
      ->will($this->returnValue(false));
    $this->db->inject('db', $db);

    $res = $this->db->postUser(array());
    $this->assertFalse($res, 'The MySql adapter did not return FALSE for postUser');
  }

  public function testPutActionSuccess()
  {
    $db = $this->getMock('MySqlMock', array('execute'));
    $db->expects($this->any())
      ->method('execute')
      ->will($this->returnValue(true));
    $this->db->inject('db', $db);

    $res = $this->db->putAction('foo', array());
    $this->assertTrue($res, 'The MySql adapter did not return TRUE for putAction');
  }

  public function testPutActionFailure()
  {
    $db = $this->getMock('MySqlMock', array('execute'));
    $db->expects($this->any())
      ->method('execute')
      ->will($this->returnValue(false));
    $this->db->inject('db', $db);

    $res = $this->db->putAction('foo', array());
    $this->assertFalse($res, 'The MySql adapter did not return FALSE for putAction');
  }

  public function testPutAlbumSuccess()
  {
    $db = $this->getMock('MySqlMock', array('execute'));
    $db->expects($this->any())
      ->method('execute')
      ->will($this->returnValue(true));
    $this->db->inject('db', $db);

    $res = $this->db->putAlbum('foo', array());
    $this->assertTrue($res);
  }

  public function testPutAlbumFailure()
  {
    $db = $this->getMock('MySqlMock', array('execute'));
    $db->expects($this->any())
      ->method('execute')
      ->will($this->returnValue(false));
    $this->db->inject('db', $db);

    $res = $this->db->putAlbum('foo', array());
    $this->assertFalse($res);
  }

  public function testPutCredentialSuccess()
  {
    $db = $this->getMock('MySqlMock', array('execute'));
    $db->expects($this->any())
      ->method('execute')
      ->will($this->returnValue(true));
    $this->db->inject('db', $db);

    $res = $this->db->putCredential('foo', array());
    $this->assertTrue($res, 'The MySql adapter did not return TRUE for putCredential');
  }

  public function testPutCredentialFailure()
  {
    $db = $this->getMock('MySqlMock', array('execute'));
    $db->expects($this->any())
      ->method('execute')
      ->will($this->returnValue(false));
    $this->db->inject('db', $db);

    $res = $this->db->putCredential('foo', array());
    $this->assertFalse($res, 'The MySql adapter did not return FALSE for putCredential');
  }

  public function testPutGroupSuccess()
  {
    $db = $this->getMock('MySqlMock', array('execute'));
    $db->expects($this->any())
      ->method('execute')
      ->will($this->returnValue(true));
    $this->db->inject('db', $db);

    $res = $this->db->putGroup('foo', array());
    $this->assertTrue($res, 'The MySql adapter did not return TRUE for putGroup');
  }

  public function testPutGroupFailure()
  {
    $db = $this->getMock('MySqlMock', array('execute'));
    $db->expects($this->any())
      ->method('execute')
      ->will($this->returnValue(false));
    $this->db->inject('db', $db);

    $res = $this->db->putGroup('foo', array());
    $this->assertFalse($res, 'The MySql adapter did not return FALSE for putGroup');
  }

  public function testPutPhotoSuccess()
  {
    $db = $this->getMock('MySqlMock', array('execute'));
    $db->expects($this->any())
      ->method('execute')
      ->will($this->returnValue(true));
    $this->db->inject('db', $db);

    $res = $this->db->putPhoto('foo', array());
    $this->assertTrue($res, 'The MySql adapter did not return TRUE for putPhoto');
  }

  public function testPutPhotoFailure()
  {
    $db = $this->getMock('MySqlMock', array('execute'));
    $db->expects($this->any())
      ->method('execute')
      ->will($this->returnValue(false));
    $this->db->inject('db', $db);

    $res = $this->db->putPhoto('foo', array());
    $this->assertFalse($res, 'The MySql adapter did not return FALSE for putPhoto');
  }

  public function testPutTagSuccess()
  {
    $db = $this->getMock('MySqlMock', array('execute'));
    $db->expects($this->any())
      ->method('execute')
      ->will($this->returnValue(true));
    $this->db->inject('db', $db);

    $res = $this->db->putTag('foo', array());
    $this->assertTrue($res, 'The MySql adapter did not return TRUE for putTag');
  }

  public function testPutTagFailure()
  {
    $db = $this->getMock('MySqlMock', array('execute'));
    $db->expects($this->any())
      ->method('execute')
      ->will($this->returnValue(false));
    $this->db->inject('db', $db);

    $res = $this->db->putTag('foo', array());
    $this->assertFalse($res, 'The MySql adapter did not return FALSE for putTag');
  }

  public function testPutUserSuccess()
  {
    $db = $this->getMock('MySqlMock', array('execute'));
    $db->expects($this->any())
      ->method('execute')
      ->will($this->returnValue(true));
    $this->db->inject('db', $db);

    $res = $this->db->putUser('foo', array());
    $this->assertTrue($res, 'The MySql adapter did not return TRUE for putUser');
  }

  public function testPutUserFailure()
  {
    $db = $this->getMock('MySqlMock', array('execute'));
    $db->expects($this->any())
      ->method('execute')
      ->will($this->returnValue(false));
    $this->db->inject('db', $db);

    $res = $this->db->putUser('foo', array());
    $this->assertFalse($res, 'The MySql adapter did not return FALSE for putUser');
  }
}
