<?php
/**
 * Webhook model.
 *
 * This handles adding and updating webhooks.
 * @author Jaisen Mathai <jaisen@jmathai.com>
 */
class Webhook
{
  /**
    * Add an action to a photo/video.
    * Accepts a set of params that must include a type and targetType
    *
    * @param array $params Params describing the action to be added
    * @return mixed Action ID on success, false on failure 
    */
  public static function add($params)
  {
    if(!isset($params['callback']) || !isset($params['topic']))
    {
      getLogger()->info(sprintf('Not all required paramaters were passed in to add(), %s', json_encode($params)));
      return false;
    }
    $id = User::getNextId('webhook');
    if($id === false)
    {
      getLogger()->crit('Could not fetch next webhook ID');
      return false;
    }

    $db = getDb();
    $action = $db->putWebhook($id, $params);
    if(!$action)
    {
      getLogger()->crit("Could not save webhook ID ({$id})");
      return false;
    }

    return $id;
  }

  /**
    * Delete a webhook.
    *
    * @param string $id ID of the webhook to be deleted.
    * @return boolean
    */
  public static function delete($id)
  {
    return getDb()->deleteWebhook($id);
  }

  /**
    * Get webhook by id.
    *
    * @param string $id ID of the webhook to be deleted.
    * @return array
    */
  public static function getById($id)
  {
    return getDb()->getWebhook($id);
  }

  /**
    * Get all webhooks.
    *
    * @return array
    */
  public static function getAll()
  {
    return getDb()->getWebhooks();
  }

  /**
    * Update a webhook.
    *
    * @param string $id ID of the webhook to be updated.
    * @return boolean
    */
  public static function update($id, $params)
  {
    return getDb()->postWebhook($id, $params);
  }

  /**
    * Defines the default attributes for a webhook.
    * Used when adding a webhook.
    *
    * @return array
    */
  private static function getDefaultAttributes()
  {
    return array(
      'appId' => getConfig()->get('application')->appId
    );
  }
}
