<?php
class Notification extends BaseModel
{
  const typeFlash = 'flash';
  const typeStatic = 'static';
  const modeConfirm = 'confirm';
  const modeError = 'error';

  private $key = 'notification';
  public function __construct()
  {
    parent::__construct();
    $this->user = new User;
    $this->key = sprintf('%s-%s', $this->key, $this->user->getEmailAddress());
  }

  public function add($msg, $type = self::typeFlash, $mode = self::modeConfirm)
  {
    $current = $this->cache->get($this->key);
    if(!is_array($current))
      $current = array(self::typeFlash => array(), self::typeStatic => array());

    $current[$type][] = array('msg' => $msg, 'mode' => $mode);
    return $this->cache->set($this->key, $current);
  }

  public function delete()
  {
    $current = $this->cache->get($this->key);
    $msg = $this->getAndRemoveFrom($current, self::typeStatic);
    $this->cache->set($this->key, $current);
    return $msg;
  }

  public function get($type = null)
  {
    $current = $this->cache->get($this->key);
    if(!is_array($current))
      return 0;

    if($type !== null)
      $fetchType = $type;
    elseif(!empty($current[self::typeFlash]))
      $fetchType = self::typeFlash;
    elseif(!empty($current[self::typeStatic]))
      $fetchType = self::typeStatic;
    else
      return 0;

    if($fetchType === self::typeFlash)
    {
      $note = $this->getAndRemoveFrom($current, self::typeFlash); // pass by reference
      $this->cache->set($this->key, $current);
      return array_merge(array('type' => self::typeFlash), $note);
    }
    elseif($fetchType === self::typeStatic)
    {
      $note = $current[self::typeStatic][0];
      return array_merge(array('type' => self::typeStatic), $note);

    }
  }

  public function getAndRemoveFrom(&$queue, $type)
  {
    $msg = $queue[$type][0];
    $queue[$type] = array_slice($queue[$type], 1, null, false);
    return $msg;    
  }
}
