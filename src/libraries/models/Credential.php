<?php
class Credential
{
  const typeUnauthorizedRequest = 'unauthorized_request';
  const typeRequest = 'request';
  const typeAccess = 'access';

  const statusInactive = '0';
  const statusActive = '1';

  const nonceCacheKey = 'oauthTimestamps';
  private $provider, $consumer;

  public function __construct()
  {
    $this->provider = new OAuthProvider($this->getOAuthParameters());
  }

  public function add($name, $permissions = array('read'))
  {
    $random = bin2hex($this->provider->generateToken(25));
    $id = substr($random, 0, 30);
    $params = array(
      'name' => $name,
      'client_secret' => substr($random, -10),
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
    $random = bin2hex($this->provider->generateToken(20));
    $params = array(
      'user_token' => substr($random, 0, 30),
      'user_secret' => substr($random, -10)
    );
    if($convertToAccessToken)
      $params['type'] = self::typeAccess;
    return getDb()->postCredential($id, $params);
  }

  public function checkRequest()
  {
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
      getLogger()->crit(OAuthProvider::reportProblem($e));
      return false;
    }
  }

  public function checkConsumer($provider)
  {
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

    $provider->consumer_secret = $consumer['client_secret'];
    return OAUTH_OK;
  }

  public function checkTimestampAndNonce($provider)
  {
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
    $consumer = $this->getConsumer($provider->consumer_key);
    if(!$consumer)
    {
      getLogger()->warn(sprintf('Could not find consumer for key %s', $provider->consumer_key));
      return OAUTH_CONSUMER_KEY_UNKNOWN;
    }

    $provider->token_secret = $consumer['user_secret'];
    return OAUTH_OK;
  }

  public function getConsumer($consumerKey)
  {
    if(!$this->consumer)
      $this->consumer = getDb()->getCredential($consumerKey);

    return $this->consumer;
  }

  public function getOAuthParameters()
  {
    $params = array();
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
    return $params;
  }
}

function getCredential()
{
  static $credential;
  if(!$credential)
    $credential = new Credential;

  return $credential;
}
