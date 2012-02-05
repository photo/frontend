<?php
class EpiConfig
{
  const FILE = 'EpiConfig_File';
  const MYSQL = 'EpiConfig_MySql';
  private static $employ;
  protected static $instances;
  protected $config;
  public function __construct()
  {
    $this->config = new stdClass;
  }

  public function get($key = null)
  {
    if(!empty($key))
      return isset($this->config->$key) ? $this->config->$key : null;
    else
      return $this->config;
  }

  public function set($key, $val)
  {
    if(isset($this->config->$key) && is_object($this->config->$key))
      $this->config->$key = (object)array_merge((array)$this->config->$key, (array)$val);
    else
      $this->config->$key = $val;
  }

  /*
   * @param  $const
   * @params optional
   */
  public static function employ()
  {
    if(func_num_args() === 1)
      self::$employ = func_get_arg(0);

    return self::$employ;
  }

  /*
   * EpiConfig::getInstance
   */
  public static function getInstance()
  {
    $params = func_get_args();
    $hash   = md5(implode('.', $params));
    if(isset(self::$instances[$hash]))
      return self::$instances[$hash];

    $type = array_shift($params);
    if(!file_exists($file = dirname(__FILE__) . "/{$type}.php"))
      EpiException::raise(new EpiConfigTypeDoesNotExistException("EpiConfig type does not exist: ({$type}).  Tried loading {$file}", 404));

    require_once $file;
    self::$instances[$hash] = new $type($params);
    self::$instances[$hash]->hash = $hash;
    return self::$instances[$hash];
  }
}

function getConfig()
{
  $employ = EpiConfig::employ();
  $class = array_shift($employ);
  if($class && class_exists($class))
    return EpiConfig::getInstance($class);
  elseif(class_exists(EpiConfig::FILE))
    return EpiConfig::getInstance(EpiConfig::FILE);
  elseif(class_exists(EpiConfig::MYSQL))
    return EpiConfig::getInstance(EpiConfig::MYSQL);
  else
    EpiException::raise(new EpiConfigTypeDoesNotExistException('Could not determine which cache handler to load', 404));
}

class EpiConfigException extends EpiException {}
