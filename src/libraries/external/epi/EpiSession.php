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
    $hash   = md5(implode('.', $params));
    if(isset(self::$instances[$hash]))
      return self::$instances[$hash];

    $type = array_shift($params);
    self::$instances[$hash] = new $type($params);
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
    {
      self::$employ = func_get_arg(0);
    }

    return self::$employ;
  }
}

interface EpiSessionInterface
{
  public function get($key = null);
  public function set($key = null, $value = null);
}

function getSession()
{
  $employ = EpiSession::employ();
  if($employ && class_exists($employ))
    return EpiSession::getInstance($employ);
  elseif(class_exists(EpiSession::PHP))
    return EpiSession::getInstance(EpiSession::PHP);
  elseif(class_exists(EpiSession::APC))
    return EpiSession::getInstance(EpiSession::APC);
  elseif(class_exists(EpiSession::MEMCACHED))
    return EpiSession::getInstance(EpiSession::MEMCACHED);
  else
    EpiException::raise(new EpiSessionException('Could not determine which session handler to load', 404));
}
