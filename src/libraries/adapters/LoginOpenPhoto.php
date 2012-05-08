<?php
/**
 * BrowserId implementation
 *
 * This class defines the functionality defined by LoginInterface for OpenPhoto.
 * @author Jaisen Mathai <jaisen@jmathai.com>
 */

class LoginOpenPhoto implements LoginInterface
{
  private $config, $db, $utility;
  public function __construct()
  {
    $this->config = getConfig()->get();
    $this->db = getDb();
    $this->utility = new Utility;
  }

  public function verifyEmail($args)
  {
    $email = $args['email'];
    $password = $args['password'];
    if($this->config->site->allowOpenPhotoLogin != 1 || $email == '' || $password == '')
      return false;

    $passwordHashed = $this->utility->encrypt($password, $this->config->secrets->passwordSalt, $this->config->secrets->passwordSecret);
    $user = $this->db->getUserByEmailAndPassword($email, $passwordHashed);
    if(!$user)
      return false;

    return $user['id'];
  }
}
