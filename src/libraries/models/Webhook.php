<?php
/**
 * Webhook model.
 *
 * This handles adding and updating webhooks.
 * @author Jaisen Mathai <jaisen@jmathai.com>
 */
class Webhook extends BaseModel
{
  /*
   * Constructor
   */
  public function __construct()
  {
    parent::__construct();
  }

  /**
    * Create an action to a photo/video.
    * Accepts a set of params that must include a type and targetType
    *
    * @param array $params Params describing the action to be added
    * @return mixed Action ID on success, false on failure
    */
  public function create($params)
  {
    $params = $this->getValidAttributes($params);
    if(!isset($params['callback']) || !isset($params['topic']))
    {
      $this->logger->info(sprintf('Not all required paramaters were passed in to create(), %s', json_encode($params)));
      return false;
    }

    $id = md5(rand());
    if($id === false)
    {
      $this->logger->crit('Could not fetch next webhook ID');
      return false;
    }

    $params['id'] = $id;
    $params['owner'] = $this->owner;
    $params['actor'] = $this->getActor();

    $status = $this->db->putWebhook($id, $params);
    if(!$status)
    {
      $this->logger->crit("Could not save webhook ID ({$id})");
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
  public function delete($id)
  {
    return $this->db->deleteWebhook($id);
  }

  /**
    * Get webhook by id.
    *
    * @param string $id ID of the webhook to be deleted.
    * @return array
    */
  public function getById($id)
  {
    return $this->db->getWebhook($id);
  }

  /**
    * Get all webhooks.
    *
    * @return array
    */
  public function getAll($topic = null)
  {
    return $this->db->getWebhooks($topic);
  }

  /**
    * Update a webhook.
    *
    * @param string $id ID of the webhook to be updated.
    * @return boolean
    */
  public function update($id, $params)
  {
    return $this->db->postWebhook($id, $params);
  }

  public function getValidAttributes($params)
  {
    $valid = array('id' => 1, 'appId' => 1, 'callback' => 1, 'topic' => 1, 'verifyToken' => 1, 'challenge' => 1, 'secret' => 1);
    foreach((array)$params as $key => $val)
    {
      if(!isset($valid[$key]))
        unset($params[$key]);
    }
    return $params;
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
      'appId' => $this->config->application->appId
    );
  }
}
