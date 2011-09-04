<?php
class Auth
{
  private $provider;

  public function checkRequest()
  {
    try
    {
      if(!$this->initProvider())
        return false;

      $this->provider->consumerHandler(array($this,'getConsumer'));	
      $this->provider->timestampNonceHandler(array($this,'checkTimestampAndNonce'));
      $this->provider->tokenHandler(array($this,'tokenHandler'));
      $this->provider->setParam('__route__', null);
      $this->provider->setRequestTokenPath('/v1/oauth/token/request'); // No token needed for this end point
      $this->provider->checkOAuthRequest('http://opme/v1/oauth/test', OAUTH_HTTP_METHOD_GET);
      return true;
    }
    catch(OAuthException $e)
    {
      getLogger()->crit(OAuthProvider::reportProblem($e));
      return false;
    }
  }

  public function getConsumer($provider)
  {
//  $consumer = new stdClass;
//  $consumer->consumer_key = 'token';//$provider->consumer_key;
//  $consumer->key_status = 0;
//  $consumer->secret = 'secret';

//  if($provider->consumer_key != $consumer->consumer_key)
//    return OAUTH_CONSUMER_KEY_UNKNOWN;
//  else if($consumer->key_status != 0)  // 0 is active, 1 is throttled, 2 is blacklisted
//    return OAUTH_CONSUMER_KEY_REFUSED;

    $provider->consumer_secret = 'token'; //$consumer->secret;
    return OAUTH_OK;
  }

  public function checkTimestampAndNonce($provider)
  {
    return OAUTH_OK;
  }

  public function tokenHandler($provider)
  {
    $provider->token_secret = 'token';
    return OAUTH_OK;
  }

  public function generateConsumerKeyAndSecret() {
    $fp = fopen('/dev/urandom','rb');
    $entropy = fread($fp, 32);
    fclose($fp);
    // in case /dev/urandom is reusing entropy from its pool, let's add a bit more entropy
    $entropy .= uniqid(mt_rand(), true);
    $hash = sha1($entropy);  // sha1 gives us a 40-byte hash
    // The first 30 bytes should be plenty for the consumer_key
    // We use the last 10 for the shared secret
    return array(substr($hash,0,30),substr($hash,30,10));
  }

  public function getOAuthParameters()
  {
    // default null values
//  $params = array('oauth_consumer_key' => 'token', 'oauth_token' => 'token', 'oauth_nonce' => null, 'oauth_timestamp' => null, 
//    'oauth_signature_method' => null, 'oauth_signature' => null);

    // fetch values from header
    $headers = getallheaders();
    foreach($headers as $name => $header)
    {
      if(stripos($name, 'authorization') === 0)
      {
        $parameters = explode(',', $header);
        foreach($parameters as $parameter)
        {
          list($key, $value) = explode('=', $parameter);
          if(strpos($key, 'oauth_') !== 0)
            continue;

          $params[$key] = urldecode(substr($value, 1, -1));
        }
      }
    }

    // override with values from GET
    foreach($_GET as $key => $value)
    {
      if(strpos($key, 'oauth_') === 0)
        $params[$key] = $value;
    }

    ksort($params);
    print_r($params);
    return $params;
  }

  public function initProvider()
  {
    if(!$this->provider)
      $this->provider = new OAuthProvider($this->getOAuthParameters());

    if(!$this->provider)
      return false;
    return true;
  }
}
