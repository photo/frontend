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

  public function create($attributes)
  {
    getAuthentication()->requireAuthentication(false);

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

    return $this->db->putActivity($id, $attributes);
  }

  public function list_($filters, $pageSize)
  {
    $filters['pageSize'] = $pageSize;
    if(!$this->user->isOwner())
      $filters['permission'] = '1';
    return $this->db->getActivities($filters);
  }

  public function view($id)
  {
    return $this->db->getActivity($id);
  }

  private function getDefaultAttributes()
  {
    return array(
      'appId' => $this->config->application->appId,
      'owner' => $this->config->user->email,
      'dateCreated' => time()
    );
  }

  private function validateParams($attributes)
  {
    if(!isset($attributes['owner']) || !isset($attributes['type']) || !isset($attributes['permission']))
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
