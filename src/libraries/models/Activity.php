<?php
/**
 * Activity model.
 *
 * This handles adding and retrieving activity
 * @author Jaisen Mathai <jaisen@jmathai.com>
 */
class Activity extends BaseModel
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

  public function create($elementId, $attributes)
  {
    if(empty($elementId) || empty($attributes))
    {
      $this->logger->warn('When creating an activity one of the following attributes were not passed, elementId or attributes');
      return false;
    }

    $attributes = array_merge($this->getDefaultAttributes(), $attributes);
    $attributes = $this->whitelistParams($attributes);
    if(!$this->validateParams($attributes))
    {
      $this->logger->warn('Not all required paramaters were passed to create an activity');
      return false;
    }

    $id = $this->user->getNextId('activity');
    if($id === false)
    {
      $this->logger->warn('Could not fetch the next activity id');
      return false;
    }

    $attributes['owner'] = $this->owner;
    $attributes['actor'] = $this->getActor();
    return $this->db->putActivity($id, $elementId, $attributes);
  }

  public function deleteForElement($elementId, $types)
  {
    $types = (array)$types;
    return $this->db->deleteActivitiesForElement($elementId, $types);
  }

  public function list_($filters, $pageSize)
  {
    $filters['pageSize'] = $pageSize;
    if(!$this->user->isAdmin())
      $filters['permission'] = '1';
    return $this->db->getActivities($filters);
  }

  public function purge()
  {
    return $this->db->postActivitiesPurge();
  }

  public function view($id)
  {
    return $this->db->getActivity($id);
  }

  private function getDefaultAttributes()
  {
    return array(
      'appId' => $this->config->application->appId,
      'dateCreated' => time()
    );
  }

  private function validateParams($attributes)
  {
    if(!isset($attributes['type']) || !isset($attributes['permission']))
      return false;

    return true;
  }

  private function whitelistParams($attributes)
  {
    $returnAttrs = array();
    $matches = array('id' => 1, 'owner' => 1, 'appId' => 1, 'type' => 1, 'data' => 1, 'permission' => 1, 'dateCreated' => 1);

    foreach($attributes as $key => $val)
    {
      if(isset($matches[$key]))
      {
        $returnAttrs[$key] = $val;
        continue;
      }

      $returnAttrs[$key] = $val;
    }

    return $returnAttrs;
  }
}
