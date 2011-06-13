<?php
class FileSystemProvider
{
  public static function init($type, $opts = null)
  {
    switch($type)
    {
      case 'S3':
        return new FileSystemProviderS3($opts);
        break;
    }
    
    throw new Exception(404, "FileSysetm Provider {$type} does not exist");
    //throw new FileSystemProviderDoesNotExistException();
  }
}

function getFs($type, $opts)
{
  static $filesystem;
  if($filesystem)
    return $filesystem;

  $filesystem = FileSystemProvider::init($type, $opts);
  return $filesystem;
}

