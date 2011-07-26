<?php
/**
  * User controller for API endpoints.
  * 
  * @author Jaisen Mathai <jaisen@jmathai.com>
 */
class ApiUserController extends BaseController
{
  /**
    * Create a new version of the photo with ID $id as specified by $width, $height and $options.
    *
    * @param string $id ID of the photo to create a new version of.
    * @param string $hash Hash to validate this request before creating photo.
    * @param int $width The width of the photo to which this URL points.
    * @param int $height The height of the photo to which this URL points.
    * @param int $options The options of the photo wo which this URL points.
    * @return string HTML
    */
  public static function login()
  {
    $wasUserLoggedIn = User::login($_POST['assertion'], $_SERVER['HTTP_HOST']);
    if($wasUserLoggedIn)
      return self::success('User was logged in successfully', array('email' => getSession()->get('email')));
    else
      return self::error('User was not able to be logged in', false);
  }
}

