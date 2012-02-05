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

  public function loadString($iniAsString)
  {
    $config = parse_ini_string($iniAsString, true);
    $this->mergeConfig($config);
  }

  protected function mergeConfig($config)
  {
    foreach($config as $key => $value)
    {
      if(!is_array($value))
      {
        $this->config->$key = $value;
      }
      else
      {
        if(!isset($this->config->$key))
          $this->config->$key = new stdClass;
        foreach($value as $innerKey => $innerValue)
          $this->config->$key->$innerKey = $innerValue;
      }
    }
  }

  /*
   * EpiConfig::getInstance
   */
  public static function getInstance()
  {
    $params = func_get_args();
    $hash   = md5(json_encode($params));
    if(isset(self::$instances[$hash]))
      return self::$instances[$hash];

    $type = $params[0];
    if(!file_exists($file = dirname(__FILE__) . "/{$type}.php"))
      EpiException::raise(new EpiConfigTypeDoesNotExistException("EpiConfig type does not exist: ({$type}).  Tried loading {$file}", 404));

    self::$instances[$hash] = new $type($params[1]);
    self::$instances[$hash]->hash = $hash;
    return self::$instances[$hash];
  }
}

function getConfig()
{
  $employ = EpiConfig::employ();
  $class = array_shift($employ);
  if($class && class_exists($class))
    return EpiConfig::getInstance($class, $employ);
  elseif(class_exists(EpiConfig::FILE))
    return EpiConfig::getInstance(EpiConfig::FILE, $employ);
  elseif(class_exists(EpiConfig::MYSQL))
    return EpiConfig::getInstance(EpiConfig::MYSQL, $employ);
  else
    EpiException::raise(new EpiConfigTypeDoesNotExistException('Could not determine which cache handler to load', 404));
}

class EpiConfigException extends EpiException {}
