<?php
class Notification extends BaseModel
{
  const typeFlash = 'flash';
  const typeStatic = 'static';

  private $key = 'notification';
  public function __construct()
  {
    parent::__construct();
    $this->user = new User;
    $this->key = sprintf('%s-%s', $this->key, $this->user->getEmailAddress());
  }

  public function add($msg, $type = self::typeFlash)
  {
    $current = $this->cache->get($this->key);
    if(!is_array($current))
      $current = array(self::typeFlash => array(), self::typeStatic => array());

    $current[$type][] = $msg;
    return $this->cache->set($this->key, $current);
  }

  public function get()
  {
    $current = $this->cache->get($this->key);
    if(empty($current))
      return null;

    if(!empty($current[self::typeFlash]))
    {
      $msg = $this->getAndRemoveFromFlash($current); // pass by reference
      $this->cache->set($this->key, $current);
      return array('msg' => $msg, 'type' => self::typeFlash);
    }
    elseif(!empty($current[self::typeStatic]))
    {
      $msg = $current[self::typeStatic][0];
      return array('msg' => $msg, 'type' => self::typeStatic);
    }

    return null;
  }

  public function getAndRemoveFromFlash(&$queue)
  {
    $msg = $queue[self::typeFlash][0];
    $queue[self::typeFlash] = array_slice($queue[self::typeFlash], 1, null, false);
    return $msg;    
  }

  public function lookup()
  {

  }
}
