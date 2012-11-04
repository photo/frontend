<?php
class UserTest extends PHPUnit_Framework_TestCase
{
  protected $user;

  public function setUp()
  {
    // to test the write methods
    $this->user = new User();
    $this->credential = $this->getMock('cred', array('isOAuthRequest', 'getConsumer', 'checkRequest','getErrorAsString','getEmailFromOAuth'));
    $this->credential->expects($this->any())
      ->method('getConsumer')
      ->will($this->returnValue(array('foo')));
  }

  public function testGetAvatarFromEmail()
  {
    $res = $this->user->getAvatarFromEmail(50, 'test@example.com');
    $this->assertEquals('http://www.gravatar.com/avatar/55502f40dc8b7c769880b10874abc9d0?s=50', $res);
  }

  public function testGetEmailAddressNonOAuth()
  {
    $session = $this->getMock('session', array('get'));
    $session->expects($this->any())
      ->method('get')
      ->will($this->returnValue('test@example.com'));
    $this->user->inject('session', $session);
    $this->credential->expects($this->any())
      ->method('isOAuthRequest')
      ->will($this->returnValue(false));
    $this->user->inject('credential', $this->credential);
    
    $res = $this->user->getEmailAddress();
    $this->assertEquals('test@example.com', $res);
  }

  public function testGetEmailAddressOAuth()
  {
    $this->credential->expects($this->any())
      ->method('isOAuthRequest')
      ->will($this->returnValue(true));
    $this->credential->expects($this->any())
      ->method('getEmailFromOAuth')
      ->will($this->returnValue('test@example.com'));
    $this->user->inject('credential', $this->credential);
    
    $res = $this->user->getEmailAddress();
    $this->assertEquals('test@example.com', $res);
  }

  public function testGetEmailAddressNull()
  {
    $session = $this->getMock('session', array('get'));
    $session->expects($this->any())
      ->method('get')
      ->will($this->returnValue(null));
    $this->user->inject('session', $session);
    $this->credential->expects($this->any())
      ->method('isOAuthRequest')
      ->will($this->returnValue(false));
    $this->user->inject('credential', $this->credential);
    
    $res = $this->user->getEmailAddress();
    $this->assertNull($res);
  }

  public function testGetNextIdPhoto()
  {
    $db = $this->getMock('Db', array('getUser', 'postUser', 'putUser'));
    $db->expects($this->any())
      ->method('getUser')
      ->will($this->onConsecutiveCalls(
        array('id' => 'test@example.com', 'lastPhotoId' => 'abc'),
        array('id' => 'test@example.com', 'lastPhotoId' => '0'),
        array('id' => 'test@example.com', 'lastPhotoId' => 'a1'),
        array('id' => 'test@example.com', 'lastPhotoId' => '9'),
        array('id' => 'test@example.com', 'lastPhotoId' => 'u')
      ));
    $db->expects($this->any())
      ->method('postUser')
      ->will($this->returnValue(true));
    $db->expects($this->any())
      ->method('putUser')
      ->will($this->returnValue(true));
    $this->user->inject('db', $db);

    // abc
    $res = $this->user->getNextId('photo');
    $this->assertEquals('abd', $res);
    // 0
    $res = $this->user->getNextId('photo');
    $this->assertEquals('1', $res);
    // a1
    $res = $this->user->getNextId('photo');
    $this->assertEquals('a2', $res);
    // 9
    $res = $this->user->getNextId('photo');
    $this->assertEquals('a', $res);
    // u
    $res = $this->user->getNextId('photo');
    $this->assertEquals('10', $res);
  }

  public function testGetNextIdPhotoFirstId()
  {
    $db = $this->getMock('Db', array('getUser', 'postUser'));
    $db->expects($this->any())
      ->method('getUser')
      ->will($this->returnValue(array('id' => 'test@example.com')));
    $db->expects($this->any())
      ->method('postUser')
      ->will($this->returnValue(true));
    $this->user->inject('db', $db);

    $res = $this->user->getNextId('photo');
    $this->assertEquals('1', $res);
  }

  public function testGetNextIdPhotoKeyNotExistYet()
  {
    $db = $this->getMock('Db', array('getUser', 'postUser'));
    $db->expects($this->any())
      ->method('getUser')
      ->will($this->returnValue(array('id' => 'test@example.com', 'lastActionId' => 'def')));
    $db->expects($this->any())
      ->method('postUser')
      ->will($this->returnValue(true));
    $this->user->inject('db', $db);

    $res = $this->user->getNextId('photo');
    $this->assertEquals('1', $res);
  }

  public function testGetNextIdFailure()
  {
    $db = $this->getMock('Db', array('getUser', 'postUser'));
    $db->expects($this->any())
      ->method('getUser')
      ->will($this->returnValue(false));
    $this->user->inject('db', $db);

    $res = $this->user->getNextId('photo');
    $this->assertFalse($res);
  }

  public function testGetAttributeSuccess()
  {
    $this->user->inject('user', array('attrfoobar' => '1234'));
    $res = $this->user->getAttribute('foobar');
    $this->assertEquals('1234', $res);
  }

  public function testGetAttributeFailure()
  {
    $this->user->inject('user', array('attrfoobar' => '1234'));
    $res = $this->user->getAttribute('foobarx');
    $this->assertEquals(false, $res);
  }

  public function testGetUserRecordFromRuntimeCache()
  {
    $this->user->inject('user', '1');
    $res = $this->user->getUserRecord();
    $this->assertEquals('1', $res);
  }

  public function testGetUserRecordOverridingRuntimeCache()
  {
    $this->user->inject('user', '1');

    $db = $this->getMock('Db', array('getUser'));
    $db->expects($this->any())
      ->method('getUser')
      ->will($this->returnValue('2'));
    $this->user->inject('db', $db);

    $this->user->getUserRecord(false);
    $res = $this->user->getUserRecord();
    $this->assertEquals('2', $res);
  }

  public function testGetUserRecordWhenNullAndFailsToCreate()
  {
    $db = $this->getMock('Db', array('getUser', 'putUser'));
    $db->expects($this->any())
      ->method('getUser')
      ->will($this->returnValue(null));
    $db->expects($this->any())
      ->method('putUser')
      ->will($this->returnValue(false));
    $this->user->inject('db', $db);

    $res = $this->user->getUserRecord();
    $this->assertFalse($res);
  }

  public function testGetUserRecordWhenNull()
  {
    $db = $this->getMock('Db', array('getUser', 'putUser'));
    $db->expects($this->at(1))
      ->method('getUser')
      ->will($this->returnValue(null));
    $db->expects($this->at(2))
      ->method('getUser')
      ->will($this->returnValue('1'));
    $db->expects($this->any())
      ->method('putUser')
      ->will($this->returnValue(true));
    $this->user->inject('db', $db);

    $res = $this->user->getUserRecord();
    $this->assertEquals('1', $res);
  }

  public function testGetUserRecordWhenError()
  {
    $db = $this->getMock('Db', array('getUser'));
    $db->expects($this->once())
      ->method('getUser')
      ->will($this->returnValue(false));
    $this->user->inject('db', $db);

    $res = $this->user->getUserRecord();
    $this->assertFalse($res);
  }

  public function testIsLoggedInFalse()
  {
    $this->credential->expects($this->once())
      ->method('isOAuthRequest')
      ->will($this->returnValue(false));
    $session = $this->getMock('session', array('get'));
    $session->expects($this->any())
      ->method('get')
      ->will($this->returnValue(null));
    $this->user->inject('session', $session);
    $this->user->inject('credential', $this->credential);

    $res = $this->user->isLoggedIn();
    $this->assertFalse($res);
  }

  public function testIsLoggedInFalseWithOAuth()
  {
    $this->credential->expects($this->once())
      ->method('isOAuthRequest')
      ->will($this->returnValue(true));
    $this->credential->expects($this->once())
      ->method('checkRequest')
      ->will($this->returnValue(false));
    $this->user->inject('credential', $this->credential);

    $res = $this->user->isLoggedIn();
    $this->assertFalse($res);
  }

  public function testIsLoggedInTrue()
  {
    $this->credential->expects($this->once())
      ->method('isOAuthRequest')
      ->will($this->returnValue(false));
    $session = $this->getMock('session', array('get'));
    $session->expects($this->any())
      ->method('get')
      ->will($this->returnValue('test@example.com'));
    $this->user->inject('session', $session);
    $this->user->inject('credential', $this->credential);

    $res = $this->user->isLoggedIn();
    $this->assertTrue($res);
  }

  public function testIsLoggedInTrueWithOAuth()
  {
    $this->credential->expects($this->once())
      ->method('isOAuthRequest')
      ->will($this->returnValue(true));
    $this->credential->expects($this->once())
      ->method('checkRequest')
      ->will($this->returnValue(true));
    $this->user->inject('credential', $this->credential);

    $res = $this->user->isLoggedIn();
    $this->assertTrue($res);
  }

  public function testIsOwnerFalse()
  {
    $this->credential->expects($this->any())
      ->method('isOAuthRequest')
      ->will($this->returnValue(false));
    $session = $this->getMock('session', array('get'));
    $session->expects($this->any())
      ->method('get')
      ->will($this->returnValue(null));
    $this->user->inject('session', $session);
    $this->user->inject('credential', $this->credential);
    $this->user->inject('config', new FauxObject);

    $res = $this->user->isOwner();
    $this->assertFalse($res);
  }

  public function testIsOwnerFalseLoggedInAsSomeoneElse()
  {
    $this->credential->expects($this->any())
      ->method('isOAuthRequest')
      ->will($this->returnValue(false));
    $session = $this->getMock('session', array('get'));
    $session->expects($this->any())
      ->method('get')
      ->will($this->returnValue('someoneelse@example.com'));
    $this->getMock('session', array('get'));
    $session->expects($this->any())
      ->method('get')
      ->will($this->returnValue('test@example.com'));
    $config = new stdClass;
    $config->user = new stdClass;
    $config->user->email = 'test@example.com';
    $this->user->inject('session', $session);
    $this->user->inject('credential', $this->credential);
    $this->user->inject('config', $config);

    $res = $this->user->isOwner();
    $this->assertFalse($res);
  }

  public function testIsOwnerTrue()
  {
    $this->credential->expects($this->any())
      ->method('isOAuthRequest')
      ->will($this->returnValue(false));
    $session = $this->getMock('session', array('get'));
    $session->expects($this->any())
      ->method('get')
      ->will($this->returnValue('test@example.com'));
    $this->getMock('session', array('get'));
    $session->expects($this->any())
      ->method('get')
      ->will($this->returnValue('test@example.com'));
    $config = new stdClass;
    $config->user = new stdClass;
    $config->user->email = 'test@example.com';
    $this->user->inject('session', $session);
    $this->user->inject('credential', $this->credential);
    $this->user->inject('config', $config);

    $res = $this->user->isOwner();
    $this->assertTrue($res);
  }

  public function testIsOwnerTrueLoggedInAsAdmin()
  {
    $this->credential->expects($this->any())
      ->method('isOAuthRequest')
      ->will($this->returnValue(false));
    $session = $this->getMock('session', array('get'));
    $session->expects($this->any())
      ->method('get')
      ->will($this->returnValue('someoneelse@example.com'));
    $this->getMock('session', array('get'));
    $session->expects($this->any())
      ->method('get')
      ->will($this->returnValue('test@example.com'));
    $config = new stdClass;
    $config->user = new stdClass;
    $config->user->email = 'test@example.com';
    $config->user->admins = 'someoneelse@example.com';
    $this->user->inject('session', $session);
    $this->user->inject('credential', $this->credential);
    $this->user->inject('config', $config);

    $res = $this->user->isOwner();
    $this->assertTrue($res);
  }

  public function testIsOwnerOAuthInvalid()
  {
    $this->credential->expects($this->any())
      ->method('isOAuthRequest')
      ->will($this->returnValue(true));
    $this->credential->expects($this->once())
      ->method('checkRequest')
      ->will($this->returnValue(false));
    $this->user->inject('credential', $this->credential);
    $config = new stdClass;
    $config->user = new stdClass;
    $config->user->email = 'doesnt matter';
    $this->user->inject('config', $config);

    $res = $this->user->isOwner();
    $this->assertFalse($res);
  }

  public function testIsOwnerOAuthValidNotOwner()
  {
    $this->credential->expects($this->once())
      ->method('isOAuthRequest')
      ->will($this->returnValue(true));
    $this->credential->expects($this->once())
      ->method('checkRequest')
      ->will($this->returnValue(true));
    $this->credential->expects($this->once())
      ->method('getEmailFromOAuth')
      ->will($this->returnValue('test@example.com'));
    $this->user->inject('credential', $this->credential);
    $config = new stdClass;
    $config->user = new stdClass;
    $config->user->email = 'different@example.com';
    $this->user->inject('config', $config);

    $res = $this->user->isOwner();
    $this->assertFalse($res);
  }

  public function testIsOwnerOAuthTrue()
  {
    $this->credential->expects($this->once())
      ->method('isOAuthRequest')
      ->will($this->returnValue(true));
    $this->credential->expects($this->once())
      ->method('checkRequest')
      ->will($this->returnValue(true));
    $this->credential->expects($this->once())
      ->method('getEmailFromOAuth')
      ->will($this->returnValue('test@example.com'));
    $this->user->inject('credential', $this->credential);
    $config = new stdClass;
    $config->user = new stdClass;
    $config->user->email = 'test@example.com';
    $this->user->inject('config', $config);

    $res = $this->user->isOwner();
    $this->assertTrue($res);
  }

  public function testLogin()
  {
    $this->markTestIncomplete('This test has not been implemented yet.');
  }

  public function testSetEmail()
  {
    $session = $this->getMock('session', array('set'));
    $session->expects($this->any())
      ->method('set')
      ->will($this->returnValue('test@example.com'));
    $config = arrayToObject(array('secrets' => array('secret' => 'foo')));
    $this->user->inject('config', $config);
    $this->user->inject('session', $session);

    $res = $this->user->setEmail('foo');
    $this->assertNull($res);
  }

  public function testUpdateSuccess()
  {
    $db = $this->getMock('Db', array('getUser', 'postUser'));
    $db->expects($this->any())
      ->method('getUser')
      ->will($this->returnValue(array('id' => 'test@example.com', 'lastPhotoId' => 'abc', 'lastActionId' => 'def')));
    $db->expects($this->any())
      ->method('postUser')
      ->will($this->returnValue(true));
    $this->user->inject('db', $db);

    $res = $this->user->update(array('id' => '123'));
    $this->assertTrue($res);
  }

  public function testUpdateCacheIsUpdatedSuccess()
  {
    $db = $this->getMock('Db', array('getUser', 'postUser'));
    $db->expects($this->any())
      ->method('getUser')
      ->will($this->onConsecutiveCalls(array(),2));
    $db->expects($this->any())
      ->method('postUser')
      ->will($this->returnValue(true));
    $this->user->inject('db', $db);

    $res = $this->user->update(array('id' => '123'));
    $this->assertTrue($res);

    $user = $this->user->getUserRecord();
    $this->assertEquals(2, $user);
  }

  public function testUpdateFailure()
  {
    $db = $this->getMock('Db', array('getUser', 'postUser'));
    $db->expects($this->any())
      ->method('getUser')
      ->will($this->returnValue(array('id' => 'test@example.com', 'lastPhotoId' => 'abc', 'lastActionId' => 'def')));
    $db->expects($this->any())
      ->method('postUser')
      ->will($this->returnValue(false));
    $this->user->inject('db', $db);

    $res = $this->user->update(array('id' => '123'));
    $this->assertFalse($res);
  }
}
