<?php
interface DatabaseInterface
{
  public function __construct($opts);
  public function deletePhoto($id);
  public function getPhoto($id);
  public function getPhotos();
  //private function normalizePhoto($raw);
}

function getDb()
{
  static $database, $type, $opts;
  // load configs only once
  if(!$type)
    $type = getConfig()->get('systems')->database;
  if(!$opts)
    $opts = getConfig()->get('credentials');

  if($database)
    return $database;

  switch($type)
  {
    case 'simpleDb':
      $database = new DatabaseSimpleDb($opts);
      break;
  }
  
  if($database)
    return $database;

  throw new Exception(404, "DataProvider {$type} does not exist");
}
