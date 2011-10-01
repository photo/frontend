<?php
class Credential
{
  const typeUnauthorizedRequest = 'unauthorized_request';
  const typeRequest = 'request';
  const typeAccess = 'access';

  const statusInactive = '0';
  const statusActive = '1';

  const nonceCacheKey = 'oauthTimestamps';
  private $consumer, $oauthException, $oauthParams, $provider;

  public function __construct()
  {
    if(class_exists('OAuthProvider'))
      $this->provider = new OAuthProvider($this->getOAuthParameters());
  }

  public function add($name, $permissions = array('read'))
  {
    if(!class_exists('OAuthProvider'))
    {
      getLogger()->warn('No OAuthProvider class found on this system');
      return false;
    }

    $random = bin2hex($this->provider->generateToken(25));
    $id = substr($random, 0, 30);
    $params = array(
      'name' => $name,
      'clientSecret' => substr($random, -10),
      /*'user_token' => '',
      'user_secret' => '',*/
      'permissions' => $permissions,
      'verifier' => substr($random, 30, 10),
      'type' => self::typeUnauthorizedRequest,
      'status' => self::statusActive
    );
    $res = getDb()->putCredential($id, $params);
    if($res)
      return $id;
    
    return false;
  }

  public function addUserToken($id, $convertToAccessToken = false)
  {
    if(!class_exists('OAuthProvider'))
    {
      getLogger()->warn('No OAuthProvider class found on this system');
      return false;
    }

    $random = bin2hex($this->provider->generateToken(20));
    $params = array(
      'userToken' => substr($random, 0, 30),
      'userSecret' => substr($random, -10)
    );
    if($convertToAccessToken)
      $params['type'] = self::typeAccess;
    return getDb()->postCredential($id, $params);
  }

  public function checkRequest()
  {
    if(!class_exists('OAuthProvider'))
    {
      getLogger()->warn('No OAuthProvider class found on this system');
      return false;
    }

    try
    {
      $this->provider->consumerHandler(array($this,'checkConsumer'));	
      $this->provider->timestampNonceHandler(array($this,'checkTimestampAndNonce'));
      $this->provider->tokenHandler(array($this,'checkToken'));
      $this->provider->setParam('__route__', null);
      $this->provider->setRequestTokenPath('/v1/oauth/token/request'); // No token needed for this end point
      $this->provider->checkOAuthRequest();
      return true;
    }
    catch(OAuthException $e)
    {
      $this->oauthException = $e;
      getLogger()->crit(OAuthProvider::reportProblem($e));
      return false;
    }
  }

  public function checkConsumer($provider)
  {
    if(!class_exists('OAuthProvider'))
    {
      getLogger()->warn('No OAuthProvider class found on this system');
      // might need a better way to do this
      return false;
    }

    $consumer = $this->getConsumer($provider->consumer_key);
    if(!$consumer)
    {
      getLogger()->warn(sprintf('Could not find consumer for key %s', $provider->consumer_key));
      return OAUTH_CONSUMER_KEY_UNKNOWN;
    }
    else if($consumer['status'] != self::statusActive)
    {
      getLogger()->warn(sprintf('Consumer key %s refused', $provider->consumer_key));
      return OAUTH_CONSUMER_KEY_REFUSED;
    }

    $provider->consumer_secret = $consumer['clientSecret'];
    return OAUTH_OK;
  }

  public function checkTimestampAndNonce($provider)
  {
    if(!class_exists('OAuthProvider'))
    {
      getLogger()->warn('No OAuthProvider class found on this system');
      // might need a better way to do this
      return false;
    }

    $cache = getConfig()->get(self::nonceCacheKey);
    if(!$cache)
      $cache = array();
    list($lastTimestamp, $nonces) = each($cache);
    if($provider->timestamp > (time()+300) || $provider->timestamp < $lastTimestamp) 
    {
      // timestamp can't be more then 30 seconds into the future
      // or prior to the last timestamp
      return OAUTH_BAD_TIMESTAMP;
    }
    elseif(isset($cache[$provider->timestamp]))
    {
      // we've seen this timestamp before and need to check the nonce
      if(isset($nonces[$provider->nonce]))
      {
        // this nonce has been used
        return OAUTH_BAD_NONCE;
      }
      else
      {
        $cache[$provider->timestamp][$provider->nonce] = true;
        getCache()->set(self::nonceCacheKey, $cache);
        return OAUTH_OK;
      }
    }
    else
    {
      $cache = array($provider->timestamp => array($provider->nonce => true));
      getCache()->set(self::nonceCacheKey, $cache);
      return OAUTH_OK;
    }
  }

  public function checkToken($provider)
  {
    if(!class_exists('OAuthProvider'))
    {
      getLogger()->warn('No OAuthProvider class found on this system');
      // might need a better way to do this
      return false;
    }

    $consumer = $this->getConsumer($provider->consumer_key);
    if(!$consumer)
    {
      getLogger()->warn(sprintf('Could not find consumer for key %s', $provider->consumer_key));
      return OAUTH_CONSUMER_KEY_UNKNOWN;
    }

    $provider->token_secret = $consumer['userSecret'];
    return OAUTH_OK;
  }

  public function getConsumer($consumerKey)
  {
    if(!$this->consumer)
      $this->consumer = getDb()->getCredential($consumerKey);

    return $this->consumer;
  }

  public function getErrorAsString()
  {
    return OAuthProvider::reportProblem($this->oauthException);
  }

  public function getOAuthParameters()
  {
    if($this->oauthParams)
      return $this->oauthParams;

    $this->oauthParams = array();
    // fetch values from header
    // See issue 171: getallheaders() might not be available on FastCGI or non-Apache.
    if(function_exists('getallheaders'))
    {
      $headers = getallheaders();
    }
    else
    {
      $headers = array();
      // solution suggested by http://us.php.net/manual/en/function.apache-request-headers.php#70810
      foreach ($_SERVER as $name => $value)
      {
	if (substr($name, 0, 5) == 'HTTP_')
        {
	  $headers[str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))))] = $value;
        }
      }
    }
    foreach($headers as $name => $header)
    {
      if(stripos($name, 'authorization') === 0)
      {
        $parameters = explode(',', $header);
        foreach($parameters as $parameter)
        {
          list($key, $value) = explode('=', $parameter);
          $key = trim($key);
          $value = trim($value);
          if(strpos($key, 'oauth_') !== 0)
            continue;

          $this->oauthParams[$key] = urldecode(substr($value, 1, -1));
        }
      }
    }

    // override with values from GET
    foreach($_GET as $key => $value)
    {
      if(strpos($key, 'oauth_') === 0)
        $this->oauthParams[$key] = $value;
    }

    ksort($this->oauthParams);
    return $this->oauthParams;
  }

  public function isOAuthRequest()
  {
    $params = $this->getOAuthParameters();
    return !empty($params);
  }
}

function getCredential()
{
  static $credential;
  if(!$credential)
    $credential = new Credential;

  return $credential;
}
