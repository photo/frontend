<?php
class EpiCache_Memcached extends EpiCache
{
  private static $connected = false;
  private $memcached = null;
  private $host = null;
  private $port = null;
  private $compress = null;
  private $expiry   = null;
  public function __construct($params = array())
  {
    $this->host = !empty($params[0]) ? $params[0] : 'localhost';
    $this->port = !empty($params[1]) ? $params[1] : 11211;;
    $this->compress = !empty($params[2]) ? $params[2] : 0;;
    $this->expiry   = !empty($params[3]) ? $params[3] : 3600;
  }

  public function delete($key, $timeout = 0)
  {
    if(!$this->connect() || empty($key))
      return false;

    return $this->memcached->delete($key, $timeout);
  }

  public function get($key, $useCache = true)
  {
    if(!$this->connect() || empty($key))
    {
      return null;
    }
    else if($useCache && $getEpiCache = $this->getEpiCache($key))
    {
      return $getEpiCache;
    }
    else
    {
      $value = $this->memcached->get($key);
      $this->setEpiCache($key, $value);
      return $value;
    }
  }

  public function set($key = null, $value = null, $ttl = null)
  {
    if(!$this->connect() || empty($key) || $value === null)
      return false;

    $expiry = $ttl === null ? $this->expiry : $ttl;
    $this->memcached->set($key, $value, $expiry);
    $this->setEpiCache($key, $value);
    return true;
  }

  private function connect()
  {
    if(self::$connected === true)
      return true;

    if(class_exists('Memcached'))
    {
      $this->memcached = new Memcached;
      
      if($this->memcached->addServer($this->host, $this->port))
        return self::$connected = true;
      else
        EpiException::raise(new EpiCacheMemcacheConnectException('Could not connect to memcache server'));
    }

    EpiException::raise(new EpiCacheMemcacheClientDneException('No memcache client exists'));
  }
}
