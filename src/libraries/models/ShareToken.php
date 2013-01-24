<?php
class ShareToken extends BaseModel
{
  public function __construct()
  {
    parent::__construct();
  }

  public function getAll()
  {
    //$tokens = $this->db->
  }

  public function get($token)
  {
    return $this->db->getShareToken($token);
  }
}
