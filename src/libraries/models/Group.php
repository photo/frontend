<?php
/**
  * User model
  *
  * This is the model for group data.
  * @author Jaisen Mathai <jaisen@jmathai.com>
  */
class Group
{
  public static function create()
  {
    $params = self::getDefaultAttributes();
    foreach($params as $key => $value)
    {
      if(isset($_POST[$key]))
        $params[$key] = $_POST[$key];
    }

    if(!self::validate($params))
      return false;

    $nextGroupId = User::getNextId('group');
    if($nextGroupId === false)
      return false;

    $res = getDb()->putGroup($nextGroupId, $params);
    if($res === false)
      return false;

    return $nextGroupId;
  }

  public static function delete($id)
  {
    return getDb()->deleteGroup($id);
  }

  public static function getGroup($id)
  {
    $group = getDb()->getGroup($id);
    return $group;
  }

  /**
    * Get the next ID to be used for a action, group or photo.
    * The ID is a base 32 string that represents an autoincrementing integer.
    * @return string 
    */
  public static function getGroups($email = null)
  {
    return getDb()->getGroups($email);
  }

  public static function update($id, $params)
  {
    $defaults = self::getDefaultAttributes();
    $params = array();
    foreach($defaults as $key => $value)
    {
      if(isset($_POST[$key]))
        $params[$key] = $_POST[$key];
    }
    if(!self::validate($params, false))
      return false;

    return getDb()->postGroup($id, $params);
  }

  private static function getDefaultAttributes()
  {
    return array(
      'appId' => getConfig()->get('application')->appId,
      'name' => '',
      'members' => array()
    );
  }

  private static function validate($params, $create = true)
  {
    if( ($create && (!isset($params['appId']) || empty($params['appId']))) || (!isset($params['name']) || empty($params['name'])) )
      return false;
    return true;
  }
}
