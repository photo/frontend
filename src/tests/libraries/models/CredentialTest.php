<?php
class CredentialTest extends PHPUnit_Framework_TestCase
{
  public $headers = array('Authorization' => 'OAuth realm="http://sp.example.com/",
                oauth_consumer_key="0685bd9184jfhq22",
                oauth_token="ad180jjd733klru7",
                oauth_signature_method="HMAC-SHA1",
                oauth_signature="wOJIO9A2W5mFwDgiDvZbTSMK%2FPY%3D",
                oauth_timestamp="137131200",
                oauth_nonce="4572616e48616d6d65724c61686176",
                oauth_version="1.0"');

  public function setUp()
  {
    if (!extension_loaded('oauth')) {
      $this->markTestSkipped("Test requires ext/oauth");
      return;
    }
    $utility = $this->getMock('Utility', array('getAllHeaders'));
    $utility->expects($this->any())
      ->method('getAllHeaders')
      ->will($this->returnValue($this->headers));

    $this->credential = new Credential(array('utility' => $utility, 'db' => new FauxObject));
    $this->credential->sendHeadersOnError = false;
    $this->credential->isUnitTest = true;
    $this->credential->reset();
    $this->token = 'abcdefghijklmnopqrstuvwxyz0123456789';
  }

  public function testValidateOAuthLibraryExists()
  {
    if(class_exists('OAuthProvider'))
      return true;

    return false;
  }

  /**
   * @depends testValidateOAuthLibraryExists
   */
  public function testAddSuccess()
  {
    $provider = $this->getMock('foobar', array('generateToken'));
    $provider->expects($this->any())
      ->method('generateToken')
      ->will($this->returnValue($this->token));
    $db = $this->getMock('Db', array('putCredential'));
    $db->expects($this->any())
      ->method('putCredential')
      ->will($this->returnValue(true));
    $this->credential->inject('provider', $provider);
    $this->credential->inject('db', $db);
    
    $expected = substr(bin2hex($this->token), 0, 30);
    $id = $this->credential->add('name', array());
    $this->assertEquals($expected, $id, 'The ID returned by add was incorrect');
  }

  /**
   * @depends testValidateOAuthLibraryExists
   */
  public function testAddFailure()
  {
    $provider = $this->getMock('foobar', array('generateToken'));
    $provider->expects($this->any())
      ->method('generateToken')
      ->will($this->returnValue($this->token));
    $db = $this->getMock('Db', array('putCredential'));
    $db->expects($this->any())
      ->method('putCredential')
      ->will($this->returnValue(false));
    $this->credential->inject('provider', $provider);
    $this->credential->inject('db', $db);
    
    $id = $this->credential->add('name', array());
    $this->assertFalse($id, 'The ID returned by add was incorrect');
  }

  /**
   * @depends testValidateOAuthLibraryExists
   */
  public function testConvertTokenSuccess()
  {
    $db = $this->getMock('Db', array('postCredential'));
    $db->expects($this->any())
      ->method('postCredential')
      ->will($this->returnValue(true));
    $this->credential->inject('db', $db);
    
    $res = $this->credential->convertToken('id', array());
    $this->assertTrue($res, 'TRUE should be returned when token is successfully converted');
  }

  /**
   * @depends testValidateOAuthLibraryExists
   */
  public function testConvertTokenFailure()
  {
    $db = $this->getMock('Db', array('postCredential'));
    $db->expects($this->any())
      ->method('postCredential')
      ->will($this->returnValue(false));
    $this->credential->inject('db', $db);
    
    $res = $this->credential->convertToken('id', array());
    $this->assertFalse($res, 'When token conversion fails FALSE should be returned');
  }

  /**
   * @depends testValidateOAuthLibraryExists
   */
  public function testCheckRequestFailure()
  {
    $provider = $this->getMock('foobar', array('consumerHandler'));
    $provider->expects($this->any())
      ->method('consumerHandler')
      ->will($this->throwException(new OAuthException));
    $this->credential->inject('provider', $provider);
    
    $res = $this->credential->checkRequest();
    $this->assertFalse($res, 'When checkRequest throws an exception it should first be caught and a FALSE returned');
  }

  /**
   * @depends testValidateOAuthLibraryExists
   */
  public function testCheckConsumerOkay()
  {
    $db = $this->getMock('Db', array('getCredential'));
    $db->expects($this->any())
      ->method('getCredential')
      ->will($this->returnValue(array('status' => Credential::statusActive, 'clientSecret' => 'secret')));
    $this->credential->inject('db', $db);
    $this->credential->inject('provider', new FauxObject);
    
    $res = $this->credential->checkConsumer(new FauxObject);
    $this->assertEquals(OAUTH_OK, $res, 'When everything works OAUTH_OK should be returned');
  }

  /**
   * @depends testValidateOAuthLibraryExists
   */
  public function testCheckConsumerUnknownKey()
  {
    $db = $this->getMock('Db', array('getCredential'));
    $db->expects($this->any())
      ->method('getCredential')
      ->will($this->returnValue(false));
    $this->credential->inject('db', $db);
    $this->credential->inject('provider', new FauxObject);
    
    $res = $this->credential->checkConsumer(new FauxObject);
    $this->assertEquals(OAUTH_CONSUMER_KEY_UNKNOWN, $res, 'Unknown key should return OAUTH_CONSUMER_KEY_UNKNOWN');
  }

  /**
   * @depends testValidateOAuthLibraryExists
   */
  public function testCheckConsumerInactiveKey()
  {
    $db = $this->getMock('Db', array('getCredential'));
    $db->expects($this->any())
      ->method('getCredential')
      ->will($this->returnValue(array('status' => Credential::statusInactive, 'clientSecret' => 'secret')));
    $this->credential->inject('db', $db);
    $this->credential->inject('provider', new FauxObject);
    
    $res = $this->credential->checkConsumer(new FauxObject);
    $this->assertEquals(OAUTH_CONSUMER_KEY_REFUSED, $res, 'Inactive key should return OAUTH_CONSUMER_KEY_REFUSED');
  }

  /**
   * @depends testValidateOAuthLibraryExists
   */
  public function testCheckTimestampAndNonceBadTimestamp()
  {
    $provider = new stdClass;
    $provider->timestamp = time()+301;
    $provider->nonce = 'nonce';

    $lastTimestamp = time();
    $cache = $this->getMock('Cache', array('get'));
    $cache->expects($this->any())
      ->method('get')
      ->will($this->returnValue(array($lastTimestamp => array())));
    $this->credential->inject('cache', $cache);
    
    $res = $this->credential->checkTimestampAndNonce($provider);
    $this->assertEquals(OAUTH_BAD_TIMESTAMP, $res, 'Future timestamp should return OAUTH_BAD_TIMESTAMP');

    $provider->timestamp = time()-301;
    
    $res = $this->credential->checkTimestampAndNonce($provider);
    $this->assertEquals(OAUTH_BAD_TIMESTAMP, $res, 'Old timestamp should return OAUTH_BAD_TIMESTAMP');
  }

  /**
   * see #628 and #738 for details
   * @depends testValidateOAuthLibraryExists
   */
  public function testCheckTimestampAndNonceGracePeriod()
  {
    $provider = new stdClass;
    $provider->timestamp = time()+299;
    $provider->nonce = 'nonce';

    $lastTimestamp = time();
    $cache = $this->getMock('Cache', array('get'));
    $cache->expects($this->any())
      ->method('get')
      ->will($this->returnValue(array($lastTimestamp => array())));
    $this->credential->inject('cache', $cache);
    
    $res = $this->credential->checkTimestampAndNonce($provider);
    $this->assertEquals(OAUTH_OK, $res, 'Timestamps can be up to 300 seconds into the future (grace period)');

    $provider->timestamp = time()-299;
    
    $res = $this->credential->checkTimestampAndNonce($provider);
    $this->assertEquals(OAUTH_OK, $res, 'Timestamps can be up to 300 seconds in he past (grace period)');
  }

  /**
   * @depends testValidateOAuthLibraryExists
   */
  public function testCheckTimestampAndNonceBadNonce()
  {
    $provider = new stdClass;
    $provider->timestamp = time()+5;
    $provider->nonce = 'nonce';

    $lastTimestamp = time();
    $cache = $this->getMock('Cache', array('get'));
    $cache->expects($this->any())
      ->method('get')
      ->will($this->returnValue(array($provider->timestamp => array('nonce' => true))));
    $this->credential->inject('cache', $cache);
    
    $res = $this->credential->checkTimestampAndNonce($provider);
    $this->assertEquals(OAUTH_BAD_NONCE, $res, 'Future timestamp should return OAUTH_BAD_TIMESTAMP');
  }

  /**
   * @depends testValidateOAuthLibraryExists
   */
  public function testCheckTimestampAndNonceOkay()
  {
    $provider = new stdClass;
    $provider->timestamp = time()+5;
    $provider->nonce = 'nonce';

    $lastTimestamp = time();
    $cache = $this->getMock('Cache', array('get'));
    $cache->expects($this->any())
      ->method('get')
      ->will($this->returnValue(array($provider->timestamp => array('nonce_different' => true))));
    $this->credential->inject('cache', $cache);
    
    $res = $this->credential->checkTimestampAndNonce($provider);
    $this->assertEquals(OAUTH_OK, $res, 'Same timestamp with new nonce should return OAUTH_OK');

    $provider->timestamp = time()+6;
    $res = $this->credential->checkTimestampAndNonce($provider);
    $this->assertEquals(OAUTH_OK, $res, 'New timestamp should return OAUTH_OK');
  }

  /**
   * @depends testValidateOAuthLibraryExists
   */
  public function testCheckTokenOkay()
  {
    $provider = new stdClass;
    $provider->consumer_key = 'consumer_key';

    $db = $this->getMock('Db', array('getCredential'));
    $db->expects($this->any())
      ->method('getCredential')
      ->will($this->returnValue(array('type' => Credential::typeAccess, 'userSecret' => 'secret')));
    $this->credential->inject('db', $db);
    $this->credential->inject('provider', new FauxObject);
    
    $res = $this->credential->checkToken($provider);
    $this->assertEquals(OAUTH_OK, $res, 'When everything works OAUTH_OK should be returned');
  }

  /**
   * @depends testValidateOAuthLibraryExists
   */
  public function testCheckTokenKeyUnknown()
  {
    $provider = new stdClass;
    $provider->consumer_key = 'consumer_key';

    $db = $this->getMock('Db', array('getCredential'));
    $db->expects($this->any())
      ->method('getCredential')
      ->will($this->returnValue(false));
    $this->credential->inject('db', $db);
    $this->credential->inject('provider', new FauxObject);
    
    $res = $this->credential->checkToken($provider);
    $this->assertEquals(OAUTH_CONSUMER_KEY_UNKNOWN, $res, 'Uknown consumer key should return OAUTH_CONSUMER_KEY_UNKNOWN');
  }

  /**
   * @depends testValidateOAuthLibraryExists
   */
  public function testCheckTokenVerifierInvalid()
  {
    $provider = new stdClass;
    $provider->consumer_key = 'consumer_key';
    $provider->verifier = 'verifier';

    $db = $this->getMock('Db', array('getCredential'));
    $db->expects($this->any())
      ->method('getCredential')
      ->will($this->returnValue(array('type' => Credential::typeRequest, 'verifier' => 'verifier_mismatch', 'userSecret' => 'secret')));
    $this->credential->inject('db', $db);
    $this->credential->inject('provider', new FauxObject);
    
    $res = $this->credential->checkToken($provider);
    $this->assertEquals(OAUTH_VERIFIER_INVALID, $res, 'When type is request and oauth verifier does not match return OAUTH_VERIFIER_INVALID');
  }

  /**
   * @depends testValidateOAuthLibraryExists
   */
  public function testGetConsumer()
  {
    $expected = array('type' => Credential::typeRequest, 'verifier' => 'verifier_mismatch', 'userSecret' => 'secret', 'time' => time());
    $db = $this->getMock('Db', array('getCredential'));
    $db->expects($this->any())
      ->method('getCredential')
      ->will($this->returnValue($expected));
    $this->credential->inject('db', $db);

    $res = $this->credential->getConsumer('key');
    $this->assertEquals($expected, $res, 'getConsumer should return exactly what is returned by db');
  }

  /**
   * @depends testValidateOAuthLibraryExists
   */
  public function testGetOAuthParameters()
  {
    $res = $this->credential->getOAuthParameters();
    list($key, $value) = each($res);
    $this->assertEquals('oauth_consumer_key', $key, 'getConsumer should return exactly what is returned by db');
  }

  /**
   * @depends testValidateOAuthLibraryExists
   */
  public function testIsOAuthRequestYes()
  {
    $res = $this->credential->isOAuthRequest();
    $this->assertTrue($res, 'When oauth headers are present isOAuthRequest should return TRUE');
  }

  /**
   * @depends testValidateOAuthLibraryExists
   */
  public function testIsOAuthRequestNo()
  {
    $utility = $this->getMock('Utility', array('getAllHeaders'));
    $utility->expects($this->any())
      ->method('getAllHeaders')
      ->will($this->returnValue(array()));
    $this->credential->inject('utility', $utility);

    // reset
    $this->credential->oauthParams = null;

    $res = $this->credential->isOAuthRequest();
    $this->assertFalse($res, 'When no oauth headers are present isOAuthRequest should return FALSE');
  }

  /**
   * @depends testValidateOAuthLibraryExists
   */
  public function testNonOAuthRequest()
  {
    $utility = $this->getMock('Utility', array('getAllHeaders'));
    $utility->expects($this->any())
      ->method('getAllHeaders')
      ->will($this->returnValue(array()));

    $this->credential = new Credential(array('utility' => $utility, 'db' => new FauxObject));
    $this->credential->sendHeadersOnError = false;
    $this->credential->isUnitTest = true;
    $res = $this->credential->checkRequest();
    $this->assertFalse($res, 'When no oauth headers are present isOAuthRequest should return FALSE');
  }
}
