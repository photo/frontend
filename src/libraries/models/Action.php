<?php
/**
 * Action model.
 *
 * This handles adding comments, favorites as well as deleting them.
 * @author Jaisen Mathai <jaisen@jmathai.com>
 */
class Action extends BaseModel
{
  /*
   * Constructor
   */
  public function __construct($params = null)
  {
    parent::__construct();
    if(isset($params['user']))
      $this->user = $params['user'];
    else
      $this->user = new User;
  }

  /**
    * Add an action to a photo/video.
    * Accepts a set of params that must include a type and targetType
    *
    * @param array $params Params describing the action to be added
    * @return mixed Action ID on success, false on failure
    */
  public function create($params)
  {
    if(!isset($params['type']) || !isset($params['targetType']))
      return false;

    $id = $this->user->getNextId('action');
    if($id === false)
    {
      $this->logger->crit("Could not fetch next action ID for {$params['type']}");
      return false;
    }
    $params = array_merge($this->getDefaultAttributes(), $params);
    $params['owner'] = $this->owner;
    $params['actor'] = $this->getActor();
    $params['permalink'] = sprintf('%s#action-%s', $params['targetUrl'], $id);
    $action = $this->db->putAction($id, $params);
    if(!$action)
    {
      $this->logger->crit("Could not save action ID ({$id}) for {$params['type']}");
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
  public function delete($id)
  {
    return $this->db->deleteAction($id);
  }

  /**
    * Retrieve a specific action.
    *
    * @param string $id ID of the action to be retrieved.
    * @return boolean
    */
  public function view($id)
  {
    return $this->db->getAction($id);
  }

  /**
    * Defines the default attributes for an action.
    * Used when adding an action.
    *
    * @return array
    */
  private function getDefaultAttributes()
  {
    return array(
      'appId' => $this->config->application->appId,
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
