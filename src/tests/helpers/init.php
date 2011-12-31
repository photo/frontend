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
