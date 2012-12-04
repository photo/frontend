<?php
/**
 * ResourceMap model.
 *
 * @author Jaisen Mathai <jaisen@jmathai.com>
 */
class ResourceMap extends BaseModel
{
  /*
   * Constructor
   */
  public function __construct()
  {
    parent::__construct();
    $this->user = new User;
  }

  public function create($params)
  {
    $id = $this->user->getNextId('resourceMap');
    if(!$id)
      return false;

    $data = array('uri' => $params['uri'], 'method' => 'GET', 'dateCreated' => time());
    if(isset($params['method']))
      $data['method'] = $params['method'];

    $data['owner'] = $this->owner;
    $data['actor'] = $this->getActor();

    $result = $this->db->putResourceMap($id, $data);
    if(!$result)
    {
      $this->logger->warn('Error creating resource map in the database');
      return false;
    }

    return $id;
  }

  public function getResourceMap($id)
  {
    $result = $this->db->getResourceMap($id);
    if(!$result)
      return false;
  
    return $result;
  }
}
