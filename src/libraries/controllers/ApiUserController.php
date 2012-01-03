<?php
/**
  * User controller for API endpoints.
  *
  * @author Jaisen Mathai <jaisen@jmathai.com>
 */
class ApiUserController extends ApiBaseController
{
  /**
    * Call the parent constructor
    *
    * @return void
    */
  public function __construct()
  {
    parent::__construct();
    $this->user = new User;
  }

  /**
    * Log a user in via BrowserID
    *
    * @return string Standard JSON envelope
    */
  public function login($provider = null)
  {
    $wasUserLoggedIn = $this->user->login($provider, $_POST);
    if($wasUserLoggedIn)
      return $this->success('User was logged in successfully', array('email' => getSession()->get('email')));
    else
      return $this->error('User was not able to be logged in', false);
  }

  /**
    * Log a user in via mobilePassphrase
    *
    * @return string Standard JSON envelope
    */
  public function loginMobile()
  {
    $mobilePassphrase = $this->user->getMobilePassphrase();

    if(empty($mobilePassphrase) || !isset($_POST['passphrase']) || $mobilePassphrase['phrase'] != $_POST['passphrase'])
      return $this->forbidden('Unable to authenticate', false);

    $email = $this->config->user->email;
    $this->user->setEmail($email);
    $this->user->setMobilePassphrase(true); // unset
    if(isset($_POST['redirect']))
      $this->route->redirect($_POST['redirect'], null, true);
    else
      return $this->success('User was logged in successfully', array('email' => getSession()->get('email')));
  }

  /**
    * Log a user out.
    *
    * @return string Standard JSON envelope
    */
  public function logout()
  {
    $this->user->logout();
    return $this->success('User was logged out successfully');
  }

  /**
    * Update a group
    *
    * @param string $id id of the group to update
    * @return string Standard JSON envelope
    */
  public function postGroup($id = null)
  {
    getAuthentication()->requireAuthentication();

    if(!$id)
      $id = $this->user->getNextId('group');

    $res = getDb()->postGroup($id, $_POST);

    if($res)
      return $this->success("Group {$id} was updated", array_merge(array('id' => $id), $_POST));
    else
      return $this->error("Could not updated group {$id}", false);
  }
}
