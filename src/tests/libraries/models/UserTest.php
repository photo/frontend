<?php
class UserTest extends PHPUnit_Framework_TestCase
{
  protected $user;

  public function setUp()
  {
    // to test the write methods
    $this->user = new User();
  }

  public function testGetAvatarFromEmail()
  {
    $res = $this->user->getAvatarFromEmail(50, 'test@example.com');
    $this->assertEquals('http://www.gravatar.com/avatar/55502f40dc8b7c769880b10874abc9d0?s=50', $res);
  }

  public function testGetEmailAddress()
  {
    $session = $this->getMock('session', array('get'));
    $session->expects($this->any())
      ->method('get')
      ->will($this->returnValue('test@example.com'));
    $this->user->inject('session', $session);
    
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
    
    $res = $this->user->getEmailAddress();
    $this->assertNull($res);
  }

  public function testGetNextIdPhoto()
  {
    $db = $this->getMock('Db', array('getUser', 'postUser'));
    $db->expects($this->any())
      ->method('getUser')
      ->will($this->returnValue(array('id' => 'test@example.com', 'lastPhotoId' => 'abc', 'lastActionId' => 'def')));
    $db->expects($this->any())
      ->method('postUser')
      ->will($this->returnValue(true));
    $this->user->inject('db', $db);

    $res = $this->user->getNextId('photo');
    $this->assertEquals('abd', $res);
  }

  public function testGetNextIdPhotoFirstTime()
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

  public function testGetUserRecordFromRuntimeCache()
  {
    $this->user->inject('user', '1');
    $res = $this->user->getUserRecord();
    $this->assertEquals('1', $res);
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
    $cred = $this->getMock('Credential', array('isOAuthRequest'));
    $cred->expects($this->once())
      ->method('isOAuthRequest')
      ->will($this->returnValue(false));
    $session = $this->getMock('session', array('get'));
    $session->expects($this->any())
      ->method('get')
      ->will($this->returnValue(null));
    $this->user->inject('session', $session);
    $this->user->inject('credential', $cred);

    $res = $this->user->isLoggedIn();
    $this->assertFalse($res);
  }

  public function testIsLoggedInTrue()
  {
    $cred = $this->getMock('Credential', array('isOAuthRequest'));
    $cred->expects($this->once())
      ->method('isOAuthRequest')
      ->will($this->returnValue(false));
    $session = $this->getMock('session', array('get'));
    $session->expects($this->any())
      ->method('get')
      ->will($this->returnValue('test@example.com'));
    $this->user->inject('session', $session);
    $this->user->inject('credential', $cred);

    $res = $this->user->isLoggedIn();
    $this->assertTrue($res);
  }

  public function testIsLoggedInOAuthFalse()
  {
    $cred = $this->getMock('Credential', array('isOAuthRequest','checkRequest'));
    $cred->expects($this->once())
      ->method('isOAuthRequest')
      ->will($this->returnValue(true));
    $cred->expects($this->once())
      ->method('checkRequest')
      ->will($this->returnValue(false));
    $this->user->inject('credential', $cred);

    $res = $this->user->isLoggedIn();
    $this->assertFalse($res);
  }

  public function testIsLoggedInOAuthTrue()
  {
    $cred = $this->getMock('Credential', array('isOAuthRequest','checkRequest'));
    $cred->expects($this->once())
      ->method('isOAuthRequest')
      ->will($this->returnValue(true));
    $cred->expects($this->once())
      ->method('checkRequest')
      ->will($this->returnValue(true));
    $this->user->inject('credential', $cred);

    $res = $this->user->isLoggedIn();
    $this->assertTrue($res);
  }

  public function testIsOwnerFalse()
  {
    $cred = $this->getMock('Credential', array('isOAuthRequest'));
    $cred->expects($this->any())
      ->method('isOAuthRequest')
      ->will($this->returnValue(false));
    $session = $this->getMock('session', array('get'));
    $session->expects($this->any())
      ->method('get')
      ->will($this->returnValue(null));
    $this->user->inject('session', $session);
    $this->user->inject('credential', $cred);
    $this->user->inject('config', new FauxObject);

    $res = $this->user->isOwner();
    $this->assertFalse($res);
  }

  public function testIsOwnerFalseLoggedInAsSomeoneElse()
  {
    $cred = $this->getMock('Credential', array('isOAuthRequest'));
    $cred->expects($this->any())
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
    $this->user->inject('credential', $cred);
    $this->user->inject('config', $config);

    $res = $this->user->isOwner();
    $this->assertFalse($res);
  }

  public function testIsOwnerTrue()
  {
    $cred = $this->getMock('Credential', array('isOAuthRequest'));
    $cred->expects($this->any())
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
    $this->user->inject('credential', $cred);
    $this->user->inject('config', $config);

    $res = $this->user->isOwner();
    $this->assertTrue($res);
  }

  public function testIsOwnerTrueLoggedInAsAdmin()
  {
    $cred = $this->getMock('Credential', array('isOAuthRequest'));
    $cred->expects($this->any())
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
    $this->user->inject('credential', $cred);
    $this->user->inject('config', $config);

    $res = $this->user->isOwner();
    $this->assertTrue($res);
  }

  public function testIsOwnerOAuthInvalid()
  {
    $cred = $this->getMock('Credential', array('isOAuthRequest','checkRequest'));
    $cred->expects($this->once())
      ->method('isOAuthRequest')
      ->will($this->returnValue(true));
    $cred->expects($this->once())
      ->method('checkRequest')
      ->will($this->returnValue(false));
    $this->user->inject('credential', $cred);
    $config = new stdClass;
    $config->user = new stdClass;
    $config->user->email = 'doesnt matter';
    $this->user->inject('config', $config);

    $res = $this->user->isOwner();
    $this->assertFalse($res);
  }

  public function testIsOwnerOAuthValidNotOwner()
  {
    $cred = $this->getMock('Credential', array('isOAuthRequest','checkRequest','getEmailFromOAuth'));
    $cred->expects($this->once())
      ->method('isOAuthRequest')
      ->will($this->returnValue(true));
    $cred->expects($this->once())
      ->method('checkRequest')
      ->will($this->returnValue(true));
    $cred->expects($this->once())
      ->method('getEmailFromOAuth')
      ->will($this->returnValue('test@example.com'));
    $this->user->inject('credential', $cred);
    $config = new stdClass;
    $config->user = new stdClass;
    $config->user->email = 'different@example.com';
    $this->user->inject('config', $config);

    $res = $this->user->isOwner();
    $this->assertFalse($res);
  }

  public function testIsOwnerOAuthTrue()
  {
    $cred = $this->getMock('Credential', array('isOAuthRequest','checkRequest','getEmailFromOAuth'));
    $cred->expects($this->once())
      ->method('isOAuthRequest')
      ->will($this->returnValue(true));
    $cred->expects($this->once())
      ->method('checkRequest')
      ->will($this->returnValue(true));
    $cred->expects($this->once())
      ->method('getEmailFromOAuth')
      ->will($this->returnValue('test@example.com'));
    $this->user->inject('credential', $cred);
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
}
