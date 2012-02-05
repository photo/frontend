<?php
class EpiConfig_File extends EpiConfig
{
  public function __construct()
  {
    parent::__construct();
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

      $config = parse_ini_file($file, true);
      $this->mergeConfig($config);
    }
  }
}
