<?php
/**
  * User model
  *
  * This is the model for user data.
  * User data consists of application settings as well as profile information.
  * Application settings include things like default permissions and auto increment ids.
  * Profile information includes things like email address.
  * @author Jaisen Mathai <jaisen@jmathai.com>
  */
class User
{
  const mobilePassphraseExpiry = 900; // 15 minutes

  /**
    * A user object that caches the value once it's been fetched from the remote datasource.
    * @access private
    * @var array
    */
  private static $user;

  /**
    * Get an avatar given an email address
    * See http://en.gravatar.com/site/implement/images/ and http://en.gravatar.com/site/implement/hash/
    *
    * @return string
    */
  public static function getAvatarFromEmail($size = 50, $email = null)
  {
    if($email === null)
      $email = getSession()->get('email');

    $hash = md5(strtolower(trim($email)));
    return "http://www.gravatar.com/avatar/{$hash}?s={$size}";
  }

  /**
    * Get the email address of the logged in user.
    *
    * @return string 
    */
  public static function getEmailAddress()
  {
    return getSession()->get('email');
  }

  /**
    * Get mobile passphrase key
    * @return string
    */
  public static function getMobilePassphrase()
  {
    $phrase = getCache()->get(self::getMobilePassphraseKey());
    if(empty($phrase))
      return null;

    $parts = explode('-', $phrase);
    if($parts[1] < time())
      return null;

    return array('phrase' => $parts[0], 'expiresAt' => $parts[1]);
  }

  /**
    * Get the next ID to be used for a action, group or photo.
    * The ID is a base 32 string that represents an autoincrementing integer.
    * @return string
    */
  public static function getNextId($type)
  {
    $type = ucwords($type);
    $key = "last{$type}Id";
    $user = self::getUserRecord();
    if($user === false)
      return false;

    if(!isset($user[$key]))
      $user[$key] = '';
    $nextIntId = base_convert($user[$key], 31, 10) + 1;
    $nextId = base_convert($nextIntId, 10, 31);
    self::update(array($key => $nextId));
    return $nextId;
  }

  /**
    * Get the user record from the remote database.
    * If the record does not exist then attempt to create it before returning.
    * Returns false if no user record could be obtained or crated.
    * Returns the user array on success.
    *
    * @return mixed  FALSE on error, array on success
    */
  public static function getUserRecord()
  {
    // we cache the user entry per request
    if(self::$user)
      return self::$user;

    $res = getDb()->getUser();
    // if null create, onerror return false
    if($res === null)
    {
      // user entry does not exist, create it
      $res = self::create();
      if(!$res)
        return false;

      // fetch the record to return
      $res = getDb()->getUser();
      if(!$res)
        return false;
    }
    elseif($res === false)
    {
      return false;
    }

    self::$user = $res;
    return self::$user;
  }

  public static function isLoggedIn()
  {
    if(getCredential()->isOAuthRequest())
    {
      if(!getCredential()->checkRequest())
      {
        return false;
      }
      return true;
    }
    else
    {
      return getSession()->get('email') != '';
    }
  }

  public static function isOwner()
  {
    if(getCredential()->isOAuthRequest())
    {
      if(!getCredential()->checkRequest())
      {
        return false;
      }
      return true;
    }
    elseif(!self::isLoggedIn())
    {
      return false;
    }
    else
    {
      $user = getConfig()->get('user');
      if($user === null)
        return false;
      $len = max(strlen(getSession()->get('email')), strlen($user->email));
      return isset($user->email) && strncmp(getSession()->get('email'), $user->email, $len) === 0;
    }
  }

  /**
    * Validate the identity of the user using BrowserID
    * If the assertion is valid then the email address is stored in session with a random key to prevent XSRF.
    *
    * @param string $assertion Assertion from BrowserID.org
    * @param string $audience Audience from BrowserID.org
    * @return boolean
    */
  public static function login($assertion, $audience)
  {
    $ch = curl_init('https://browserid.org/verify');
    $params = array('assertion' => $assertion, 'audience' => $audience);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_CAINFO, getConfig()->get('paths')->configs.'/ca-bundle.crt');
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);

    $response = curl_exec($ch);
    $response = json_decode($response, 1);
    if(!isset($response['status']) || $response['status'] != 'okay')
      return false;

    self::setEmail($response['email']);
    return true;
  }

  /**
    * Log a user out.
    *
    * @return voic
    */
  public static function logout()
  {
    getSession()->end();
  }

  /**
    * Set the session email.
    *
    * @return voic
    */
  public static function setEmail($email)
  {
    getSession()->set('email', $email);
    getSession()->set('crumb', md5(getConfig()->get('secrets')->secret . time()));
  }

  /**
    * Sets the mobile passphrase key
    *
    * @return string
    */
  public static function setMobilePassphrase($destroy = false)
  {
    if($destroy === true)
    {
      getCache()->set(self::getMobilePassphraseKey(), '');
      return null;
    }
    $phrase = sprintf('%s-%s', substr(md5(uniqid()), 0, 6), time()+self::mobilePassphraseExpiry);
    getCache()->set(self::getMobilePassphraseKey(), $phrase, self::mobilePassphraseExpiry);
    return $phrase;
  }

  /**
    * Create a new user record.
    * This method should only be called if no record already exists.
    * The user record has a key of 1 and default attributes specified by self::getDefaultAttributes().
    * Differs from self::update on the implementation at the adapter layer.
    *
    * @return boolean
    */
  private static function create()
  {
    return getDb()->putUser(getConfig()->get('user')->email, self::getDefaultAttributes());
  }

  /**
    * Default attributes for a new user record.
    *
    * @return array
    */
  private static function getDefaultAttributes()
  {
    return array('lastPhotoId' => '', 'lastActionId' => '', 'lastGroupId' => '', 'lastWebhookId' => '');
  }

  /**
    * Mobile passphrase key.
    *
    * @return array
    */
  private static function getMobilePassphraseKey()
  {
    return sprintf('%s-%s', 'mobile.passphrase.key', getenv('HTTP_HOST'));
  }

  /**
    * Update an existing user record.
    * This method updates an existing user record.
    * Differs from self::create on the implementation at the adapter layer.
    *
    * The user record has a key of 1 and default attributes specified by self::getDefaultAttributes().
    *
    * @return boolean
    */
  private static function update($params)
  {
    $params = array_merge(self::getDefaultAttributes(), $params);
    return getDb()->postUser($params);
  }
}
