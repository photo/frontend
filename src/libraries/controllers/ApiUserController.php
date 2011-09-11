<?php
/**
  * User controller for API endpoints.
  * 
  * @author Jaisen Mathai <jaisen@jmathai.com>
 */
class ApiUserController extends BaseController
{
  /**
    * Get the owner's groups
    *
    * @return string Standard JSON envelope 
    */
  public static function groups()
  {
    getAuthentication()->requireAuthentication();

    $groups = User::getGroups();
    if($groups === false)
      return self::error('An error occurred trying to get your groups', false);

    return self::success('A list of your groups', (array)$groups);
  }

  /**
    * Log a user in via BrowserID
    *
    * @return string Standard JSON envelope 
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

  /**
    * Update a group
    *
    * @param string $id id of the group to update
    * @return string Standard JSON envelope 
    */
  public static function postGroup($id = null)
  {
    getAuthentication()->requireAuthentication();

    if(!$id)
      $id = User::getNextId('group');

    $res = getDb()->postGroup($id, $_POST);

    if($res)
      return self::success("Group {$id} was updated", array_merge(array('id' => $id), $_POST));
    else
      return self::error("Could not updated group {$id}", false);
  }
}
