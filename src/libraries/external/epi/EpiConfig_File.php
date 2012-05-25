<?php
class EpiConfig_File extends EpiConfig
{
  public function __construct()
  {
    parent::__construct();
  }

  public function getString($file)
  {
    $file = $this->getFilePath($file);

    if(!file_exists($file))
    {
      EpiException::raise(new EpiConfigException("Config file ({$file}) does not exist"));
      return; // need to simulate same behavior if exceptions are turned off
    }
    return file_get_contents($file);
  }

  public function exists($file)
  {
    $file = $this->getFilePath($file);
    return file_exists($file);
  }

  public function isAlias($file)
  {
    $file = $this->getFilePath($file);
    if(!file_exists($file))
      return null;

    return is_link($file);
  }

  public function load(/*$file, $file, $file, $file...*/)
  {
    $args = func_get_args();
    foreach($args as $file)
    {
      $confAsIni = $this->getString($file);
      $config = parse_ini_string($confAsIni, true);
      $this->mergeConfig($config);
    }
  }

  public function write($file, $string)
  {
    $this->createDirectoryIfNotExists(dirname($file));
    $created = @file_put_contents($file, $string);
    return $created != false;
  }

  private function createDirectoryIfNotExists($dir)
  {
    // if directory exists and it's writable return true
    if(is_dir($dir) && is_writable($dir))
      return true;

    // try to do a recursive write
    return mkdir($dir, 0600, true);
  }

  private function getFilePath($file)
  {
    // Prepend config directory if the path doesn't start with . or /
    if($file[0] != '.' && $file[0] != '/')
      $file = Epi::getPath('config') . "/{$file}";

    return $file;
  }
}
