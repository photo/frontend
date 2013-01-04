<?php
class EpiSession_Memcached implements EpiSessionInterface
{
  private static $connected = false;
  private $key  = null;
  private $store= null;

  public function __construct($params = array())
  {
    $this->host = !empty($params[0]) ? $params[0] : 'localhost';
    $this->port = !empty($params[1]) ? $params[1] : 11211;;
    $this->compress = !empty($params[2]) ? $params[2] : 0;;
    $this->expiry   = !empty($params[3]) ? $params[3] : 3600;
    $this->domain   = !empty($params[4]) ? $params[4] : $_SERVER['HTTP_HOST'];
    if(empty($_COOKIE[EpiSession::COOKIE]))
    {
      $cookieVal = md5(uniqid(rand(), true));
      setcookie(EpiSession::COOKIE, $cookieVal, time()+$this->expiry, '/', $this->domain);
      $_COOKIE[EpiSession::COOKIE] = $cookieVal;
    }
  }

  public function delete($key)
  {
    unset($this->store[$key]);
    $this->memcached->set($this->key, $this->store);
  }

  public function end()
  {
    if(!$this->connect())
      return;

    $this->memcached->delete($this->key);
    $this->store = null;
    setcookie(EpiSession::COOKIE, null, time()-86400);
  }

  public function get($key = null)
  {
    if(!$this->connect() || empty($key) || !isset($this->store[$key]))
      return false;

    return $this->store[$key];
  }

  public function getAll()
  {
    if(!$this->connect())
      return;

    return $this->memcached->get($this->key);
  }

  public function set($key = null, $value = null)
  {
    if(!$this->connect() || empty($key))
      return false;

    $this->store[$key] = $value;
    $this->memcached->set($this->key, $this->store);
    return $value;
  }

  private function connect($params = null)
  {
    if(self::$connected)
      return true;

    if(class_exists('Memcached'))
    {
      $this->memcached = new Memcached;
      if($this->memcached->addServer($this->host, $this->port))
      {
        self::$connected = true;
        $this->key = empty($key) ? $_COOKIE[EpiSession::COOKIE] : $key;
        $this->store = $this->getAll();
        return true;
      }
      else
      {
        EpiException::raise(new EpiSessionMemcacheConnectException('Could not connect to memcache server'));
      }
    }
    EpiException::raise(new EpiSessionMemcacheClientDneException('Could not connect to memcache server'));
  }
}

class EpiSessionMemcacheConnectException extends EpiException {}
class EpiSessionMemcacheClientDneException extends EpiException {}
