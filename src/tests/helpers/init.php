<?php
echo 'HI';
date_default_timezone_set('America/Los_Angeles');
require_once 'PHPUnit/Autoload.php';
require_once dirname(__FILE__) . '/autoload.php';
require_once dirname(__FILE__) . '/mysql.php';
require_once dirname(__FILE__) . '/aws.php';
require_once dirname(__FILE__) . '/vfs.php';

// stub out exceptions
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

// utility function
function arrayToObject($array)
{
  return json_decode(json_encode($array));
}

// framework stubs for Epiphany
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

class FauxObject
{
  public function __construct($init = null)
  {
    if($init !== null)
    {
      foreach($init as $k => $v)
        $this->$k = $v;
    }
  }

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
