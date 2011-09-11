<?php
/**
  * User controller for API endpoints.
  * 
  * @author Jaisen Mathai <jaisen@jmathai.com>
 */
class ApiUserController extends BaseController
{
  /**
    * Log a user in.
    *
    * @return string Standard JSON envelope 
    */
  public static function login()
  {
    $wasUserLoggedIn = User::login($_POST['assertion'], $_SERVER['HTTP_HOST']);
    if($wasUserLoggedIn)
      return self::success('User was logged in successfully', array('email' => getSession()->get('email')));
    else
      return self::error('User was not able to be logged in', false);
  }

  /**
    * Log a user out.
    *
    * @return string Standard JSON envelope 
    */
  public static function logout()
  {
    User::logout();
    return self::success('User was logged out successfully');
  }
}
