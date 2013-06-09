<?php
class Token extends BaseModel
{
  public function __construct()
  {
    parent::__construct();
  }

  public function create($params)
  {
    $id = substr(sha1(uniqid(time(), true)), -10);
    $params = $this->whitelistParams($params);
    $res =  $this->db->putShareToken($id, $params);
    if(!$res)
      return false;

    return $id;
  }

  public function delete($id)
  {
    return $this->db->deleteShareToken($id);
  }

  public function get($id)
  {
    return $this->db->getShareToken($id);
  }

  public function getByTarget($type, $data)
  {
    return $this->db->getShareTokens($type, $data);
  }

  public function getAll()
  {
    return $this->db->getShareTokens();
  }

  private function whitelistParams($params)
  {
    $matches = array('id' => 1,'type' => 1,'data' => 1,'dateExpires');
    foreach($params as $key => $val)
    {
      if(!isset($matches[$key]))
        unset($params[$key]);
    }
    return $params;
  }
}
