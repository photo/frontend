<?php
require_once 'PHPUnit/Framework.php';

function arrayToObject($array)
{
  return json_decode(json_encode($array));
}

if(!function_exists('getCache'))
{
  function getCache() { return new FauxObject; }
}
if(!function_exists('getConfig'))
{
  function getConfig() { return new FauxObject; }
}
if(!function_exists('getLogger'))
{
  function getLogger() { return new FauxObject; }
}
if(!function_exists('getApi'))
{
  function getApi() { return new FauxObject; }
}
if(!function_exists('getRoute'))
{
  function getRoute() { return new FauxObject; }
}
if(!function_exists('getSession'))
{
  function getSession() { return new FauxObject; }
}
if(!function_exists('getDb'))
{
  function getDb() { return new FauxObject; }
}
if(!function_exists('getFs'))
{
  function getFs() { return new FauxObject; }
}
if(!function_exists('getCredential'))
{
  function getCredential() { return new FauxObject; }
}


if(!class_exists('BaseModel'))
{
  class BaseModel
  {
    public function __construct()
    {
      $this->logger = getLogger();
    }
    public function inject($key, $value)
    {
      $this->$key = $value;
    }
  }
}

class OPException extends Exception
{
  public static function raise($exception)
  {
    throw $exception;
  }
}

class OPAuthorizationException extends OPException{}
class OPAuthorizationOAuthException extends OPAuthorizationException{}
class OPAuthorizationSessionException extends OPAuthorizationException{}
class OPInvalidImageException extends OPException{}

class FauxObject
{
  public function __call($name, $params)
  {
    return func_get_args();
  }

  public function __get($key)
  {
    return func_get_args();
  }

  public function __set($key, $value)
  {
    return func_get_args();
  }
}

/*function getBaseDir($file = null)
{
  if($file === null)
    $file = __FILE__;

  if(dirname($file) === $file)
    return false; // we didn't find a 'src' direcory

  echo "checking $file\n";
  $dir = basename($file);
  if($dir === 'src')
    return $file;
  return getBaseDir(dirname($file));
}*/
