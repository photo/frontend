<?php
/**
  * User model
  *
  * This is the model for group data.
  * @author Jaisen Mathai <jaisen@jmathai.com>
  */
class Group extends BaseModel
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
    $whitelist = $validParams = $this->getDefaultAttributes();
    foreach($params as $key => $value)
    {
      if(isset($whitelist[$key]))
        $validParams[$key] = $params[$key];
    }

    if(!$this->validate($validParams))
      return false;

    $nextGroupId = $this->user->getNextId('group');
    if($nextGroupId === false)
      return false;

    $res = $this->db->putGroup($nextGroupId, $validParams);
    if($res === false)
      return false;

    return $nextGroupId;
  }

  public function delete($id)
  {
    return $this->db->deleteGroup($id);
  }

  public function getGroup($id)
  {
    $group = $this->db->getGroup($id);
    return $group;
  }

  /**
    * Get the next ID to be used for a action, group or photo.
    * The ID is a base 32 string that represents an autoincrementing integer.
    * @return string
    */
  public function getGroups($email = null)
  {
    return $this->db->getGroups($email);
  }

  public function update($id, $params)
  {
    $defaults = $this->getDefaultAttributes();
    $validParams = array();
    foreach($defaults as $key => $value)
    {
      if(isset($params[$key]))
        $validParams[$key] = $params[$key];
    }
    if(!$this->validate($validParams, false))
      return false;

    return $this->db->postGroup($id, $validParams);
  }

  private function getDefaultAttributes()
  {
    return array(
      'appId' => $this->config->application->appId,
      'name' => '',
      'members' => array()
    );
  }

  private function validate($params, $create = true)
  {
    if( ($create && (!isset($params['appId']) || empty($params['appId']))) || (!isset($params['name']) || empty($params['name'])) )
      return false;
    return true;
  }
}
