<?php
class EpiSession
{
  const MEMCACHED = 'EpiSession_Memcached';
  const APC = 'EpiSession_Apc';
  const PHP = 'EpiSession_Php';

  // Name of session cookie
  const COOKIE = 'EpiSession';
  private static $instances, $employ;
  private function __construct(){}

  /*
   * @param  type  required
   * @params optional
   */
  public static function getInstance()
  {
    $params = func_get_args();
    $hash   = md5(json_encode($params));
    if(isset(self::$instances[$hash]))
      return self::$instances[$hash];

    $type = $params[0];
    if(!isset($params[1]))
      $params[1] = array();
    self::$instances[$hash] = new $type($params[1]);
    self::$instances[$hash]->hash = $hash;
    return self::$instances[$hash];
  }

  /*
   * @param  $const
   * @params optional
   */
  public static function employ(/*$const*/)
  {
    if(func_num_args() === 1)
      self::$employ = func_get_arg(0);
    elseif(func_num_args() > 1)
      self::$employ = func_get_args();

    return self::$employ;
  }
}

interface EpiSessionInterface
{
  public function get($key = null);
  public function set($key = null, $value = null);
}

if(!function_exists('getSession'))
{
  function getSession()
  {
    $employ = EpiSession::employ();
    $class = array_shift($employ);
    if($employ && class_exists($class))
      return EpiSession::getInstance($class, $employ);
    elseif(class_exists(EpiSession::PHP))
      return EpiSession::getInstance(EpiSession::PHP);
    elseif(class_exists(EpiSession::APC))
      return EpiSession::getInstance(EpiSession::APC);
    elseif(class_exists(EpiSession::MEMCACHED))
      return EpiSession::getInstance(EpiSession::MEMCACHED);
    else
      EpiException::raise(new EpiSessionException('Could not determine which session handler to load', 404));
  }
}
