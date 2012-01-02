<?php
require_once 'PHPUnit/Framework.php';

function arrayToObject($array)
{
  return json_decode(json_encode($array));
}

class ___L
{
  public function info() {}
  public function warn() {}
  public function crit() {}
}

function getLogger()
{
  return new ___L;
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
