<?php
class Action
{
  public static function add($params)
  {
    if(!isset($params['type']) || !isset($params['targetType']))
      return false;

    $id = User::getNextActionId();
    // TODO: add a log message
    if($id === false)
      return false;
    $params = array_merge(self::getDefaultAttributes(), $params);
    $db = getDb();
    $action = $db->putAction($id, $params);
    if(!$action)
      return false;

    return $id;
  }

  public static function delete($id)
  {
    return getDb()->deleteAction($id);
  }

  private static function getDefaultAttributes()
  {
    return array(
      'appId' => getConfig()->get('application')->appId,
      'name' => '',
      'avatar' => '',
      'website' => '',
      'targetUrl' => '',
      'permalink' => '',
      'value' => '',
      'datePosted' => time(),
      'status' => 1
    );
  }
}
