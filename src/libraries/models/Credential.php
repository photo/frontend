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

    $randomConsumer = bin2hex($this->provider->generateToken(25));
    $randomUser = bin2hex($this->provider->generateToken(20));
    $id = substr($randomConsumer, 0, 30);
    $params = array(
      'name' => $name,
      'clientSecret' => substr($randomConsumer, -10),
      'userToken' => substr($randomUser, 0, 30),
      'userSecret' => substr($randomUser, -10),
      'permissions' => $permissions,
      'verifier' => substr($randomConsumer, 30, 10),
      'type' => self::typeUnauthorizedRequest,
      'status' => self::statusActive
    );
    $res = getDb()->putCredential($id, $params);
    if($res)
      return $id;

    return false;
  }

  public function convertToken($id, $toTokenType)
  {
    if(!class_exists('OAuthProvider'))
    {
      getLogger()->warn('No OAuthProvider class found on this system');
      return false;
    }

    $params = array('type' => $toTokenType);
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
    elseif($consumer['type'] == self::typeRequest && $consumer['verifier'] != $provider->verifier)
    {
      getLogger()->warn(sprintf('Invalid OAuth verifier: %s', $provider->verifier));
      return OAUTH_VERIFIER_INVALID;
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
    $headers = Utility::getAllHeaders();
    foreach($headers as $name => $header)
    {
      if(stripos($name, 'authorization') === 0)
      {
        $parameters = explode(',', $header);
        foreach($parameters as $parameter)
        {
          list($key, $value) = explode('=', $parameter);
          $key = trim(substr($key, strpos($key, 'oauth_')));
          $value = trim($value);
          if(strpos($key, 'oauth_') !== 0)
            continue;

          $this->oauthParams[$key] = urldecode(substr($value, 1, -1));
        }
      }
    }

    // override with values from REQUEST
    foreach($_REQUEST as $key => $value)
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
    // oauth_token and oauth_callback can be passed in for authenticated endpoints to obtain a credential
    return (count($params) > 2);
  }
}

function getCredential()
{
  static $credential;
  if(!$credential)
    $credential = new Credential;

  return $credential;
}
