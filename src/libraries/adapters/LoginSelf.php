<?php
/**
 * Instance implementation
 *
 * This class defines the functionality defined by LoginInterface for Trovebox.
 * @author Jaisen Mathai <jaisen@jmathai.com>
 */

class LoginSelf implements LoginInterface
{
  private $config, $db, $utility, $user;
  public function __construct()
  {
    $this->config = getConfig()->get();
    $this->db = getDb();
    $this->utility = new Utility;
    $this->user = new User;
  }

  public function verifyEmail($args)
  {
    $email = $args['email'];
    $password = $args['password'];
    if($this->config->site->allowOpenPhotoLogin != 1 || $email == '' || $password == '')
      return false;

    $user = $this->db->getUserByEmailAndPassword($email, $password);
    if(!$user)
      return false;

    return $user['id'];
  }
}
