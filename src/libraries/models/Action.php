<?php
/**
 * Action model.
 *
 * This handles adding comments, favorites as well as deleting them.
 * @author Jaisen Mathai <jaisen@jmathai.com>
 */
class Action
{
  /**
    * Add an action to a photo/video.
    * Accepts a set of params that must include a type and targetType
    *
    * @param array $params Params describing the action to be added
    * @return mixed Action ID on success, false on failure
    */
  public static function create($params)
  {
    if(!isset($params['type']) || !isset($params['targetType']))
      return false;

    $id = User::getNextId('action');
    if($id === false)
    {
      getLogger()->crit("Could not fetch next action ID for {$params['type']}");
      return false;
    }
    $params = array_merge(self::getDefaultAttributes(), $params);
    $params['permalink'] = sprintf('%s#action-%s', $params['targetUrl'], $id);
    $db = getDb();
    $action = $db->putAction($id, $params);
    if(!$action)
    {
      getLogger()->crit("Could not save action ID ({$id}) for {$params['type']}");
      return false;
    }

    return $id;
  }

  /**
    * Delete an action to a photo/video.
    *
    * @param string $id ID of the action to be deleted.
    * @return boolean
    */
  public static function delete($id)
  {
    return getDb()->deleteAction($id);
  }

  /**
    * Retrieve a specific action.
    *
    * @param string $id ID of the action to be retrieved.
    * @return boolean
    */
  public static function view($id)
  {
    return getDb()->getAction($id);
  }

  /**
    * Defines the default attributes for an action.
    * Used when adding an action.
    *
    * @return array
    */
  private static function getDefaultAttributes()
  {
    return array(
      'appId' => getConfig()->get('application')->appId,
      'owner' => getConfig()->get('user')->email,
      'email' => '',
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
