<?php
class DatabaseProvider
{
  public static function init($type, $opts = null)
  {
    switch($type)
    {
      case 'simpleDb':
        return new DatabaseProviderSimpleDb($opts);
        break;
    }
    
    throw new Exception(404, 'DataProvider does not exist');
    //throw new DataProviderDoesNotExistException();
  }
}

function getDb($type, $opts)
{
  static $database;
  if($database)
    return $database;

  $database = DatabaseProvider::init($type, $opts);
  return $database;
}
