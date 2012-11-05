<?php
class Credential extends BaseModel
{
  const typeUnauthorizedRequest = 'unauthorized_request';
  const typeRequest = 'request';
  const typeAccess = 'access';

  const statusInactive = '0';
  const statusActive = '1';

  const nonceCacheKey = 'oauthTimestamps';
  public $oauthException, $oauthParams, $provider, $sendHeadersOnError = true, $isUnitTest = false;
  private static $consumer = null, $requestStatus = null;

  /**
    * Constructor
    */
  public function __construct($params = null)
  {
    parent::__construct();
    if(isset($params['utility']))
      $this->utility = $params['utility'];
    else
      $this->utility = new Utility;

    if(isset($params['db']))
      $this->db = $params['db'];

    $oauthParams = array('oauth_consumer_key' => '');
    if($this->isOAuthRequest())
    {
      $oauthParams = $this->getOAuthParameters();
      // seed the consumer (see #929 and #950)
      $this->getConsumer($oauthParams['oauth_consumer_key']);
    }
    
    if(class_exists('OAuthProvider'))
      $this->provider = new OAuthProvider($oauthParams);
  }

  /**
    * Add an oauth credential for this user
    *
    * @param string $name Human readable name for this credential
    * @param array $params Array of permissions
    * @return mixed Credential ID on success, false on failure
    */
  public function add($name, $permissions = array('read'))
  {
    if(!class_exists('OAuthProvider'))
    {
      $this->logger->warn('No OAuthProvider class found on this system');
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
      'status' => self::statusActive,
	  'dateCreated' => time()
    );
    $res = $this->db->putCredential($id, $params);
    if($res)
      return $id;

    return false;
  }

  /**
    * Convert an existing token from one type to another.
    *  Typically used to convert from an authorized request token to an access token
    *
    * @param string $name Human readable name for this credential
    * @param array $params Array of permissions
    * @return mixed Credential ID on success, false on failure
    */
  public function convertToken($id, $toTokenType)
  {
    if(!class_exists('OAuthProvider'))
    {
      $this->logger->warn('No OAuthProvider class found on this system');
      return false;
    }

    $params = array('type' => $toTokenType);
    return $this->db->postCredential($id, $params);
  }

  public function checkRequest()
  {
    if(self::$requestStatus !== null)
      return self::$requestStatus;

    if(!class_exists('OAuthProvider'))
    {
      $this->logger->warn('No OAuthProvider class found on this system');
      return false;
    }

    try
    {
      $this->provider->consumerHandler(array($this,'checkConsumer'));
      $this->provider->timestampNonceHandler(array($this,'checkTimestampAndNonce'));
      $this->provider->tokenHandler(array($this,'checkToken'));
      $this->provider->setParam('__route__', null);
      $this->provider->setRequestTokenPath('/v1/oauth/token/request'); // No token needed for this end point
      // unit test requires HTTP method context #929
      if($this->isUnitTest === true)
        $this->provider->checkOAuthRequest(null, OAUTH_HTTP_METHOD_GET);
      else
        $this->provider->checkOAuthRequest();
      self::$requestStatus = true;
    }
    catch(OAuthException $e)
    {
      $this->oauthException = $e;
      $this->logger->crit(OAuthProvider::reportProblem($e, $this->sendHeadersOnError));
      self::$requestStatus = false;
    }

    return self::$requestStatus;
  }

  public function checkConsumer($provider)
  {
    if(!class_exists('OAuthProvider'))
    {
      $this->logger->warn('No OAuthProvider class found on this system');
      // might need a better way to do this
      return false;
    }

    $consumer = $this->getConsumer($provider->consumer_key);
    if(!$consumer)
    {
      $this->logger->warn(sprintf('Could not find consumer for key %s', $provider->consumer_key));
      return OAUTH_CONSUMER_KEY_UNKNOWN;
    }
    else if($consumer['status'] != self::statusActive)
    {
      $this->logger->warn(sprintf('Consumer key %s refused', $provider->consumer_key));
      return OAUTH_CONSUMER_KEY_REFUSED;
    }

    $provider->consumer_secret = $consumer['clientSecret'];
    return OAUTH_OK;
  }

  public function checkTimestampAndNonce($provider)
  {
    if(!class_exists('OAuthProvider'))
    {
      $this->logger->warn('No OAuthProvider class found on this system');
      // might need a better way to do this
      return false;
    }

    $cache = $this->cache->get(self::nonceCacheKey);
    if(!$cache || !is_array($cache))
      $cache = array();

    list($lastTimestamp, $nonces) = each($cache);
    // change logic to check for request order to include a 5 minute grace period
    // see #628 and #738 for details
    if($provider->timestamp > (time()+300) || $provider->timestamp < ($lastTimestamp-300))
    {
      // timestamp can't be more then 30 seconds into the future
      // or prior to the last timestamp
      $this->logger->warn(sprintf('The provided timestamp of %s did not validate against the current timestamp of %s and lastTimestamp of %s', $provider->timestamp, time(), $lastTimestamp));
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
      $this->logger->warn('No OAuthProvider class found on this system');
      // might need a better way to do this
      return false;
    }
    $consumer = $this->getConsumer($provider->consumer_key);
    if(!$consumer)
    {
      $this->logger->warn(sprintf('Could not find consumer for key %s', $provider->consumer_key));
      return OAUTH_CONSUMER_KEY_UNKNOWN;
    }
    elseif($consumer['type'] == self::typeRequest && $consumer['verifier'] != $provider->verifier)
    {
      $this->logger->warn(sprintf('Invalid OAuth verifier: %s', $provider->verifier));
      return OAUTH_VERIFIER_INVALID;
    }

    $provider->token_secret = $consumer['userSecret'];
    return OAUTH_OK;
  }

  public function getConsumer($consumerKey)
  {
    if(!self::$consumer)
      self::$consumer = $this->db->getCredential($consumerKey);

    return self::$consumer;
  }

  public function getEmailFromOAuth()
  {
    if(!self::$consumer)
      return false;

    return self::$consumer['owner'];
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
    $headers = $this->utility->getAllHeaders();
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

  public function reset()
  {
    self::$consumer = null;
    self::$requestStatus = null;
  }
}
