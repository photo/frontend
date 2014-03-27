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
  public function __construct($params = null)
  {
    parent::__construct();
    if(isset($params['user']))
      $this->user = $params['user'];
    else
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

  public function getGroups($email = null)
  {
    return $this->db->getGroups($email);
  }

  public function getGroupsByMember($email)
  {
    return $this->db->getGroupMemberGroups($email);
  }

  public function manageMembers($id, $emails, $action)
  {
    $utilityObj = new Utility;
    $host = $utilityObj->getHost(true);
    foreach($emails as $k => $v) 
    {
      if(stristr($v, '@') === false)
        unset($emails[$k]);
    }

    if(count($emails) === 0)
      return false;

    $res = false;
    switch($action)
    {
      case 'add':
        $res = $this->db->putGroupMembers($id, $host, $emails);
        if($res)
          $this->notifyMembers($id, $emails);
        break;
      case 'remove':
        $res = $this->db->deleteGroupMembers($id, $emails);
        break;
    }
    return $res;
  }

  public function getMembersAcrossGroups()
  {
    return $this->db->getGroupsMembers();
  }

  public function getNewMembersFromList($emails)
  {
    $members = $this->getMembersAcrossGroups();
    // TODO check php function to extract colum from multi dimensional array
    $existingMembers = array();
    foreach($members as $m)
      $existingMembers[] = $m['email'];

    return array_diff($emails, $existingMembers);
  }

  public function undelete($id)
  {
    return $this->db->restoreGroup($id);
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
