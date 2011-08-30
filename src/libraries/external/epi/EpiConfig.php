<?php
class EpiConfig
{
  private static $instance;
  private $config;
  public function __construct()
  {
    $this->config = new stdClass;
  }

  public function load(/*$file, $file, $file, $file...*/)
  {
    $args = func_get_args();
    foreach($args as $file)
    {
      // Prepend config directory if the path doesn't start with . or /
      if($file[0] != '.' && $file[0] != '/')
        $file = Epi::getPath('config') . "/{$file}";

      if(!file_exists($file))
      {
        EpiException::raise(new EpiConfigException("Config file ({$file}) does not exist"));
        break; // need to simulate same behavior if exceptions are turned off
      }

      $parsed_array = parse_ini_file($file, true);
      foreach($parsed_array as $key => $value)
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
    $this->config->$key = $val;
  }

  /*
   * EpiConfig::getInstance
   */
  public static function getInstance()
  {
    if(self::$instance)
      return self::$instance;

    self::$instance = new EpiConfig;
    return self::$instance;
  }  
}

function getConfig()
{
  return EpiConfig::getInstance();
}

class EpiConfigException extends EpiException {}
