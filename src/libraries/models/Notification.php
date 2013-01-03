<?php
class Notification extends BaseModel
{
  private $key = 'flash-notification';
  public function __construct()
  {
    parent::__construct();
  }

  public function add($msg)
  {
    $this->session->set($this->key, $msg);
  }

  public function get()
  {
    $msg = $this->session->get($this->key);
    if(!empty($msg))
      $this->session->delete($this->key);
    return $msg;
  }

  public function lookup()
  {

  }
}
