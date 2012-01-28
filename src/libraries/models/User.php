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
class User extends BaseModel
{
  const mobilePassphraseExpiry = 900; // 15 minutes

  /**
    * A user object that caches the value once it's been fetched from the remote datasource.
    * @access private
    * @var array
    */
  protected $user, $credential;

  /*
   * Constructor
   */
  public function __construct()
  {
    parent::__construct();
  }

  /**
    * Get an avatar given an email address
    * See http://en.gravatar.com/site/implement/images/ and http://en.gravatar.com/site/implement/hash/
    *
    * @return string
    */
  public function getAvatarFromEmail($size = 50, $email = null)
  {
    if($email === null)
      $email = $this->session->get('email');

    $hash = md5(strtolower(trim($email)));
    return "http://www.gravatar.com/avatar/{$hash}?s={$size}";
  }

  /**
    * Get the email address of the logged in user.
    *
    * @return string
    */
  public function getEmailAddress()
  {
    // TODO support oauth calls
    return $this->session->get('email');
  }

  /**
    * Get mobile passphrase key
    * @return string
    */
  public function getMobilePassphrase()
  {
    $phrase = $this->cache->get($this->getMobilePassphraseKey());
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
  public function getNextId($type)
  {
    $type = ucwords($type);
    $key = "last{$type}Id";
    $user = $this->getUserRecord();
    if($user === false)
      return false;

    if(!isset($user[$key]))
      $user[$key] = '';
    $nextIntId = base_convert($user[$key], 31, 10) + 1;
    $nextId = base_convert($nextIntId, 10, 31);
    $this->update(array($key => $nextId));
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
  public function getUserRecord()
  {
    // we cache the user entry per request
    if($this->user)
      return $this->user;

    $res = $this->db->getUser();
    // if null create, onerror return false
    if($res === null)
    {
      // user entry does not exist, create it
      $res = $this->create();
      if(!$res)
        return false;

      // fetch the record to return
      $res = $this->db->getUser();
      if(!$res)
        return false;
    }
    elseif($res === false)
    {
      return false;
    }

    $this->user = $res;
    return $this->user;
  }

  public function isLoggedIn()
  {
    $credential = $this->getCredentialObject();
    if($credential->isOAuthRequest())
      return $credential->checkRequest() === true;
    else
      return $this->session->get('email') != '';
  }

  public function isOwner()
  {
    $user = $this->config->user;
    $credential = $this->getCredentialObject();
    if($credential->isOAuthRequest())
    {
      return $credential->checkRequest() === true && $credential->getEmailFromOAuth() === $user->email;;
    }
    elseif(!$this->isLoggedIn())
    {
      return false;
    }
    else
    {
      if($user === null)
        return false;
      $len = max(strlen($this->session->get('email')), strlen($user->email));
      return isset($user->email) && strncmp($this->session->get('email'), $user->email, $len) === 0;
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
  public function login($provider, $params)
  {
    $email = getLogin($provider)->verifyEmail($params);
    if($email === false)
      return false;

    $this->setEmail($email);
    return true;
  }

  /**
    * Log a user out.
    *
    * @return voic
    */
  public function logout()
  {
    $this->session->end();
  }

  /**
    * Set the session email.
    *
    * @return voic
    */
  public function setEmail($email)
  {
    $this->session->set('email', $email);
    $this->session->set('crumb', md5($this->config->secrets->secret . time()));
  }

  /**
    * Sets the mobile passphrase key
    *
    * @return string
    */
  public function setMobilePassphrase($destroy = false)
  {
    if($destroy === true)
    {
      $this->cache->set($this->getMobilePassphraseKey(), '');
      return null;
    }
    $phrase = sprintf('%s-%s', substr(md5(uniqid()), 0, 6), time()+self::mobilePassphraseExpiry);
    $this->cache->set($this->getMobilePassphraseKey(), $phrase, self::mobilePassphraseExpiry);
    return $phrase;
  }

  /**
    * Creates and returns a credential object
    *
    * @return object
    */
  protected function getCredentialObject()
  {
    if(isset($this->credential))
      return $this->credential;

    $this->credential = new Credential;
    return $this->credential;
  }

  /**
    * Create a new user record.
    * This method should only be called if no record already exists.
    * The user record has a key of 1 and default attributes specified by $this->getDefaultAttributes().
    * Differs from $this->update on the implementation at the adapter layer.
    *
    * @return boolean
    */
  private function create()
  {
    return $this->db->putUser($this->getDefaultAttributes());
  }

  /**
    * Default attributes for a new user record.
    *
    * @return array
    */
  private function getDefaultAttributes()
  {
    return array('lastPhotoId' => '', 'lastActionId' => '', 'lastGroupId' => '', 'lastWebhookId' => '');
  }

  /**
    * Mobile passphrase key.
    *
    * @return array
    */
  private function getMobilePassphraseKey()
  {
    return sprintf('%s-%s', 'mobile.passphrase.key', getenv('HTTP_HOST'));
  }

  /**
    * Update an existing user record.
    * This method updates an existing user record.
    * Differs from $this->create on the implementation at the adapter layer.
    *
    * The user record has a key of 1 and default attributes specified by $this->getDefaultAttributes().
    *
    * @return boolean
    */
  private function update($params)
  {
    $user = $this->getUserRecord();
    $params = array_merge($this->getDefaultAttributes(), $user, $params);
    return $this->db->postUser($params);
  }
}
