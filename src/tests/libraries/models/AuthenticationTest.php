<?php
$_REQUEST['oauth_consumer_key'] = 'foo';
class AuthenticationWrapper extends Authentication
{
  public function __construct()
  {
    $params = array('user' => new FauxObject, 'session' => new FauxObject, 'credential' => new FauxObject);
    parent::__construct($params);
  }
  public function inject($key, $value)
  {
    $this->$key = $value;
  }
}

class AuthenticationTest extends PHPUnit_Framework_TestCase
{
  public function setUp()
  {
    $this->authentication = new AuthenticationWrapper; 
    $db = $this->getMock('db', array('getConsumer'));
    $db->expects($this->any())
      ->method('getConsumer')
      ->will($this->returnValue(array('foo')));

    $this->credential = $this->getMock('Credential', array('isOAuthRequest', 'getConsumer', 'checkRequest','getErrorAsString'));
    $this->credential->expects($this->any())
      ->method('getConsumer')
      ->will($this->returnValue(array('foo')));

    restore_exception_handler();
  }

  public function testIsRequestAuthenticatedUserLoggedIn()
  {
    $user = $this->getMock('User', array('isLoggedIn'));
    $user->expects($this->any())
      ->method('isLoggedIn')
      ->will($this->returnValue(true));
    $this->authentication->inject('user', $user);

    $resp = $this->authentication->isRequestAuthenticated();
    $this->assertTrue($resp, 'When user is logged in isAuthenticatedRequest should return true');
  }

  public function testIsRequestAuthenticatedUserNotLoggedInNoOAuth()
  {
    $user = $this->getMock('User', array('isLoggedIn'));
    $user->expects($this->any())
      ->method('isLoggedIn')
      ->will($this->returnValue(false));
    $this->credential->expects($this->any())
      ->method('isOAuthRequest')
      ->will($this->returnValue(false));
    $this->authentication->inject('user', $user);
    $this->authentication->inject('credential', $this->credential);

    $resp = $this->authentication->isRequestAuthenticated();
    $this->assertFalse($resp, 'When user is NOT logged in and no valid OAuth isAuthenticatedRequest should return false');
  }

  public function testIsRequestAuthenticatedUserNotLoggedInWithOAuth()
  {
    $user = $this->getMock('User', array('isLoggedIn'));
    $user->expects($this->any())
      ->method('isLoggedIn')
      ->will($this->returnValue(false));
    $this->authentication->inject('user', $user);

    $this->credential->expects($this->any())
      ->method('isOAuthRequest')
      ->will($this->returnValue(true));

    $this->authentication->inject('credential', $this->credential);

    $resp = $this->authentication->isRequestAuthenticated();
    $this->assertTrue($resp, 'When user is NOT logged in but has valid OAuth isAuthenticatedRequest should return true');
  }

  /**
  * @expectedException OPAuthorizationOAuthException
  */
  public function testRequireAuthenticationOAuthInvalid()
  {
    $this->credential->expects($this->any())
      ->method('isOAuthRequest')
      ->will($this->returnValue(true));
    $this->credential->expects($this->any())
      ->method('checkRequest')
      ->will($this->returnValue(false));
    $this->credential->expects($this->any())
      ->method('getErrorAsString')
      ->will($this->returnValue('foobar'));

    $this->authentication->inject('credential', $this->credential);

    // as long as no exception is thrown, we're good
    $this->authentication->requireAuthentication();
  }

  public function testRequireAuthenticationOAuthValid()
  {
    $this->credential->expects($this->any())
      ->method('isOAuthRequest')
      ->will($this->returnValue(true));
    $this->credential->expects($this->any())
      ->method('checkRequest')
      ->will($this->returnValue(true));
    $this->credential->expects($this->any())
      ->method('getErrorAsString')
      ->will($this->returnValue('foobar'));

    $this->authentication->inject('credential', $this->credential);

    // as long as no exception is thrown, we're good
    $this->authentication->requireAuthentication();
  }

  /**
  * @expectedException OPAuthorizationSessionException
  */
  public function testRequireAuthenticationNotLoggedIn()
  {
    $user = $this->getMock('User', array('isLoggedIn','isOwner'));
    $user->expects($this->any())
      ->method('isLoggedIn')
      ->will($this->returnValue(false));
    /*$user->expects($this->any())
      ->method('isOwner')
      ->will($this->returnValue(true));*/
    $this->authentication->inject('user', $user);
    $this->credential->expects($this->any())
      ->method('isOAuthRequest')
      ->will($this->returnValue(false));
    $this->authentication->inject('user', $user);
    $this->authentication->inject('credential', $this->credential);

    // should throw an exception
    $this->authentication->requireAuthentication();
  }

  public function testRequireAuthenticationIsNotOwnerButValid()
  {
    $user = $this->getMock('User', array('isLoggedIn','isOwner'));
    $user->expects($this->any())
      ->method('isLoggedIn')
      ->will($this->returnValue(true));
    $user->expects($this->any())
      ->method('isOwner')
      ->will($this->returnValue(false));
    $this->authentication->inject('user', $user);
    $this->credential->expects($this->any())
      ->method('isOAuthRequest')
      ->will($this->returnValue(false));
    $this->authentication->inject('user', $user);
    $this->authentication->inject('credential', $this->credential);

    // as long as no exception is thrown we're good
    $this->authentication->requireAuthentication(false);
  }

  /**
  * @expectedException OPAuthorizationSessionException
  */
  public function testRequireAuthenticationIsNotOwnerInvalidPassingNoParameter()
  {
    $user = $this->getMock('User', array('isLoggedIn','isOwner'));
    $user->expects($this->any())
      ->method('isLoggedIn')
      ->will($this->returnValue(true));
    $user->expects($this->any())
      ->method('isOwner')
      ->will($this->returnValue(false));
    $this->authentication->inject('user', $user);
    $this->credential->expects($this->any())
      ->method('isOAuthRequest')
      ->will($this->returnValue(false));
    $this->authentication->inject('user', $user);
    $this->authentication->inject('credential', $this->credential);

    // as long as no exception is thrown we're good
    $this->authentication->requireAuthentication();
  }

  /**
  * @expectedException OPAuthorizationSessionException
  */
  public function testRequireAuthenticationIsNotOwnerInvalid()
  {
    $user = $this->getMock('User', array('isLoggedIn','isOwner'));
    $user->expects($this->any())
      ->method('isLoggedIn')
      ->will($this->returnValue(true));
    $user->expects($this->any())
      ->method('isOwner')
      ->will($this->returnValue(false));
    $this->authentication->inject('user', $user);
    $this->credential->expects($this->any())
      ->method('isOAuthRequest')
      ->will($this->returnValue(false));
    $this->authentication->inject('user', $user);
    $this->authentication->inject('credential', $this->credential);

    // should thrown an exception
    $this->authentication->requireAuthentication(true);
  }

  public function testRequireCrumbIsOAuth()
  {
    $this->credential->expects($this->any())
      ->method('isOAuthRequest')
      ->will($this->returnValue(true));
    $this->authentication->inject('credential', $this->credential);

    // as long as no exception is thrown we're good
    $this->authentication->requireCrumb();
  }

  public function testRequireCrumbValid()
  {
    $this->credential->expects($this->any())
      ->method('isOAuthRequest')
      ->will($this->returnValue(false));
    $session = $this->getMock('Session', array('get'));
    $session->expects($this->any())
      ->method('get')
      ->will($this->returnValue('foobar'));
    $this->authentication->inject('credential', $this->credential);
    $this->authentication->inject('session', $session);

    // as long as no exception is thrown we're good
    $this->authentication->requireCrumb('foobar');
  }

  /**
  * @expectedException OPAuthorizationException
  */
  public function testRequireCrumbInvalid()
  {
    $this->credential->expects($this->any())
      ->method('isOAuthRequest')
      ->will($this->returnValue(false));
    $session = $this->getMock('Session', array('get'));
    $session->expects($this->any())
      ->method('get')
      ->will($this->returnValue('foobar'));
    $this->authentication->inject('credential', $this->credential);
    $this->authentication->inject('session', $session);

    // as long as no exception is thrown we're good
    $this->authentication->requireCrumb('invalid');
  }
}
