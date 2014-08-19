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

  public function testGetAvatarFromEmailFromLibrary()
  {
    $db = $this->getMock('Db', array('getUser', 'postUser', 'putUser'));
    $db->expects($this->any())
      ->method('getUser')
      ->will($this->returnValue(array('id' => 'test@example.com', 'lastPhotoId' => 'abc', 'attrprofilePhoto' => 'http://foo/bar')));
    $themeObj = $this->getMock('ThemeObj', array('asset'));
    $themeObj->expects($this->any())
      ->method('asset')
      ->will($this->returnValue('/path/to/asset'));
    $this->user->inject('db', $db);
    $this->user->inject('themeObj', $themeObj);

    $_SERVER['HTTP_HOST'] = 'foo';

    $res = $this->user->getAvatarFromEmail(50, 'test@example.com');
    $this->assertEquals('http://foo/bar', $res);
  }

  public function testGetAvatarFromEmailGravatar()
  {
    $db = $this->getMock('Db', array('getUser', 'postUser', 'putUser'));
    $db->expects($this->any())
      ->method('getUser')
      ->will($this->returnValue(array('id' => 'test@example.com', 'lastPhotoId' => 'abc')));
    $themeObj = $this->getMock('ThemeObj', array('asset'));
    $themeObj->expects($this->any())
      ->method('asset')
      ->will($this->returnValue('/path/to/asset'));
    $config = new stdClass;
    $config->site = new stdClass;
    $config->site->useGravatar = 1;

    $this->user->inject('themeObj', $themeObj);
    $this->user->inject('db', $db);
    $this->user->inject('config', $config);

    $_SERVER['HTTP_HOST'] = 'foo';

    $res = $this->user->getAvatarFromEmail(50, 'test@example.com');
    $this->assertEquals('http://www.gravatar.com/avatar/55502f40dc8b7c769880b10874abc9d0?s=50&d=http%3A%2F%2Ffoo%2Fpath%2Fto%2Fasset', $res);
  }

  public function testGetAvatarFromEmailNoGravatar()
  {
    $db = $this->getMock('Db', array('getUser', 'postUser', 'putUser'));
    $db->expects($this->any())
      ->method('getUser')
      ->will($this->returnValue(array('id' => 'test@example.com', 'lastPhotoId' => 'abc')));
    $themeObj = $this->getMock('ThemeObj', array('asset'));
    $themeObj->expects($this->any())
      ->method('asset')
      ->will($this->returnValue('/path/to/asset'));
    $config = new stdClass;
    $config->site = new stdClass;
    $config->site->useGravatar = 0;

    $this->user->inject('themeObj', $themeObj);
    $this->user->inject('db', $db);
    $this->user->inject('config', $config);

    $_SERVER['HTTP_HOST'] = 'foo';

    $res = $this->user->getAvatarFromEmail(50, 'test@example.com');
    $this->assertEquals('http://foo/path/to/asset', $res);
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

  public function testGetUserByEmailFirstCallShouldCacheSuccess()
  {
    $db = $this->getMock('Db', array('getUser'));
    $db->expects($this->any())
      ->method('getUser')
      ->will($this->onConsecutiveCalls(
        array('id' => 'test@example.com', 'seq' => 1),
        array('id' => 'test@example.com', 'seq' => 2)
      ));
    $this->user->inject('db', $db);
    $res = $this->user->getUserByEmail('foo@bar.com');
    $this->assertEquals(1, $res['seq']);

    $res = $this->user->getUserByEmail('foo@bar.com');
    $this->assertEquals(1, $res['seq']);
  }

  public function testGetUserByEmailSkipCacheSuccess()
  {
    $db = $this->getMock('Db', array('getUser'));
    $db->expects($this->any())
      ->method('getUser')
      ->will($this->onConsecutiveCalls(
        array('id' => 'test@example.com', 'seq' => 1),
        array('id' => 'test@example.com', 'seq' => 2)
      ));
    $this->user->inject('db', $db);
    $res = $this->user->getUserByEmail('foo@bar.com');
    $this->assertEquals(1, $res['seq']);

    $res = $this->user->getUserByEmail('foo@bar.com', false);
    $this->assertEquals(2, $res['seq']);
  }

  public function testGetUserByEmailVerifyCacheByHandleSuccess()
  {
    $db = $this->getMock('Db', array('getUser'));
    $db->expects($this->any())
      ->method('getUser')
      ->will($this->onConsecutiveCalls(
        array('id' => 'test1@example.com', 'seq' => 1),
        array('id' => 'test2@example.com', 'seq' => 2)
      ));
    $this->user->inject('db', $db);
    $res = $this->user->getUserByEmail('foo1@bar.com');
    $this->assertEquals(1, $res['seq']);

    $res = $this->user->getUserByEmail('foo2@bar.com');
    $this->assertEquals(2, $res['seq']);
  }

  public function testGetUserByEmailCachedSuccess()
  {
    $this->user->inject('userArray', array('foo@bar.com' => '123'));
    $res = $this->user->getUserByEmail('foo@bar.com');
    $this->assertEquals('123', $res);
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

  public function testGetNextIdUpdateFailure()
  {
    $db = $this->getMock('Db', array('getUser', 'postUser', 'putUser'));
    $db->expects($this->any())
      ->method('getUser')
      ->will($this->onConsecutiveCalls(
        array('id' => 'test@example.com', 'lastPhotoId' => 'abc'),
        array('id' => '', 'lastPhotoId' => '')
      ));
    $db->expects($this->any())
      ->method('postUser')
      ->will($this->returnValue(false));
    $this->user->inject('db', $db);

    // abc
    $res = $this->user->getNextId('photo');
    $this->assertEquals(false, $res);
  }


  public function testGetAttributeNameSuccess()
  {
    $res = $this->user->getAttributeName('foobar');
    $this->assertEquals('attrfoobar', $res);
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

    $this->user->clearUserCache();
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
    $this->assertFalse($res);
    $res = $this->user->isOwner(true);
    $this->assertTrue($res);
    $res = $this->user->isAdmin();
    $this->assertTrue($res);
  }

  public function testIsOwnerTrueLoggedInAsAdminIgnoreCase()
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
    $config->user->admins = 'SoMeOnEeLsE@ExAmPlE.CoM';
    $this->user->inject('session', $session);
    $this->user->inject('credential', $this->credential);
    $this->user->inject('config', $config);

    $res = $this->user->isOwner();
    $this->assertFalse($res);
    $res = $this->user->isOwner(true);
    $this->assertTrue($res);
    $res = $this->user->isAdmin();
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

  public function testIsOwnerIgnoreCase()
  {
    $this->credential->expects($this->any())
      ->method('isOAuthRequest')
      ->will($this->returnValue(false));
    $session = $this->getMock('session', array('get'));
    $session->expects($this->any())
      ->method('get')
      ->will($this->returnValue('TeSt@eXaMpLe.CoM'));
    $config = new stdClass;
    $config->user = new stdClass;
    $config->user->email = 'test@example.com';
    $this->user->inject('session', $session);
    $this->user->inject('credential', $this->credential);
    $this->user->inject('config', $config);

    $res = $this->user->isOwner();
    $this->assertTrue($res);
  }

  public function testIsOwnerOAuthIgnoreCase()
  {
    $this->credential->expects($this->once())
      ->method('isOAuthRequest')
      ->will($this->returnValue(true));
    $this->credential->expects($this->once())
      ->method('checkRequest')
      ->will($this->returnValue(true));
    $this->credential->expects($this->once())
      ->method('getEmailFromOAuth')
      ->will($this->returnValue('TeSt@ExAmPlE.CoM'));
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
