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
    * Encrypt user password
    *
    * @return string
   */
  public function encryptPassword($password)
  {
    return sha1(sprintf('%s-%s', $password, $this->config->secrets->passwordSalt));
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
    $credential = $this->getCredentialObject();
    if($credential->isOAuthRequest())
      return $credential->getEmailFromOAuth();
    else
      return $this->session->get('email');
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

    /*$nextId = $this->getAttribute($type);
    if($nextId === false)
      $nextId = '';
    $nextIntId = base_convert($nextId, 31, 10) + 1;
    $nextId = base_convert($nextIntId, 10, 31);*/
    $this->update(array($key => $nextId));
    return $nextId;
  }

  /**
    * Gets an attribute from the user's entry in the user db
    * @param string $name The name of the value to retrieve
    *
    * @return mixed String on success FALSE on failure
    */
  public function getAttribute($name)
  {
    $user = $this->getUserRecord();
    if($user === false)
      return false;

    if(isset($user[$name]))
      return $user[$name];
    return false;
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
    if(!isset($this->config->user))
      return false;

    $user = $this->config->user;
    $credential = $this->getCredentialObject();
    $loggedInEmail = $this->session->get('email');
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
      $len = max(strlen($loggedInEmail), strlen($user->email));
      $isOwner = isset($user->email) && strncmp(strtolower($loggedInEmail), strtolower($user->email), $len) === 0;
      if($isOwner)
        return true;

      if(isset($user->admins))
      {
        $admins = (array)explode(',', $user->admins);
        if(array_search(strtolower($loggedInEmail), array_map('strtolower', $admins)) !== false)
          return true;
      }
    }
    return false;
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
    * @return void
    */
  public function logout()
  {
    $this->session->end();
  }

  /**
    * Set the session email.
    *
    * @return void
    */
  public function setEmail($email)
  {
    $this->session->set('email', $email);
    $this->session->set('crumb', md5($this->config->secrets->secret . time()));
    if($this->isOwner())
      $this->session->set('site', $_SERVER['HTTP_HOST']);
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
  public function update($params)
  {
    $user = $this->getUserRecord();
    $params = array_merge($this->getDefaultAttributes(), $user, $params);
    // encrypt the password if present, else remove it
    if(isset($params['password']))
    {
      if(!empty($params['password']))
        $params['password'] = $this->encryptPassword($params['password']);
      else
        unset($params['password']);
    }
    // TODO check $params against a whitelist
    return $this->db->postUser($params);
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
    return array('lastPhotoId' => '', 'lastActionId' => '', 'lastGroupId' => '', 'lastWebhookId' => '', 'password' => '');
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
}
