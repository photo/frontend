<?php
class DatabaseProvider
{
  public function __construct($type, $opts = null)
  {
    switch($type)
    {
      case 'AWSS3':
        break;
    }
    
    throw new Exception(404, 'DataProvider does not exist');
    //throw new DataProviderDoesNotExistException();
  }
}

function getDb($type, $opts)
{
  static $database;
  if(isset($database))
    return $database;

  $databases = new DatabaseProvider($type, $opts);
  return $database;
}
