<?php
define('IS_UNIT_TEST', 1);
date_default_timezone_set('America/Los_Angeles');

// if using the phar of phpunit we don't need the pear files
//  see http://phpunit.de/getting-started.html
// else load it from pear
if(file_exists('PHPUnit/Autoload.php'))
  require_once 'PHPUnit/Autoload.php';

// set paths
$libraryDir = sprintf('%s/libraries', dirname(dirname(dirname(__FILE__))));
$pathsObj = new stdClass;
$pathsObj->adapters = sprintf('%s/adapters', $libraryDir);
$pathsObj->controllers = sprintf('%s/controllers', $libraryDir);
$pathsObj->external = sprintf('%s/external', $libraryDir);
$pathsObj->models = sprintf('%s/models', $libraryDir);

require_once dirname(dirname(dirname(__FILE__))) . '/libraries/functions.php';
require_once dirname(dirname(dirname(__FILE__))) . '/libraries/compatability.php';
spl_autoload_register('openphoto_autoloader');

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
if(!function_exists('getTemplate'))
{
  function getTemplate() { return new FauxObject; }
}
if(!function_exists('getDb'))
{
  function getDb() { return new FauxObject; }
}
if(!function_exists('getFs'))
{
  function getFs() { return new FauxObject; }
}

class EpiRoute
{
  const httpGet = 'GET';
  const httpPost = 'POST';
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
    return $key;
  }

  public function __set($key, $value)
  {
    return func_get_args();
  }
}
