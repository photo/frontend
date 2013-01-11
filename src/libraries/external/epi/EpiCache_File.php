<?php
class EpiCache_File extends EpiCache
{
  private $expiry   = null, $dir;
  public function __construct($params = array())
  {
    $this->expiry   = !empty($params[0]) ? $params[0] : 3600;
    $this->dir      = sprintf('%s/openphoto-cache', sys_get_temp_dir());
    if(!is_dir($this->dir))
      mkdir($this->dir, 0744);

    if(!is_dir($this->dir))
      throw new EpiException('Could not create cache directory');
  }

  public function delete($key = null)
  {
    if(empty($key))
      return;

    if(file_exists($file = $this->getFileName($key)))
      unlink($file);
    $this->deleteEpiCacheKey($key);
    return true;
  }

  public function get($key)
  {
    if(empty($key)){
      return null;
    }else if($getEpiCache = $this->getEpiCache($key)){
      return $getEpiCache;
    }else{
      $file = $this->getFileName($key);
      if(!file_exists($file))
        return null;
      
      $contents = file_get_contents($file);
      preg_match('/(.+)-([0-9]+)$/', $contents, $parts);
      if($parts[2] < time())
        return null;

      $this->setEpiCache($key, $parts[1]);
      return $parts[1];
    }
  }

  public function set($key = null, $value = null, $expiry = null)
  {
    if(empty($key) || $value === null)
      return false;

    if($expiry === null)
      $expiry = $this->expiry;

    $expiry += time();
    $saved = file_put_contents($this->getFileName($key), "{$value}-{$expiry}");
    if(!$saved)
      return false;

    $this->setEpiCache($key, $value);
    return true;
  }

  private function getFileName($key)
  {
    return sprintf('%s/%s', $this->dir, $key);
  }
}
