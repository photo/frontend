<?php
interface FileSystemInterface
{
  public function deletePhoto($id);
  public function getPhoto($filename);
  public function putPhoto($localFile, $remoteFile);
  public function putPhotos($files);
  //private function normalizePhoto($raw);
}

function getFs()
{
  static $filesystem, $type, $opts;
  // load configs only once
  if(!$type)
    $type = getConfig()->get('systems')->fileSystem;
  if(!$opts)
    $opts = getConfig()->get('credentials');

  if($filesystem)
    return $filesystem;

  switch($type)
  {
    case 'S3':
      $filesystem = new FileSystemS3($opts);
      break;
  }
  
  if($filesystem)
    return $filesystem;

  throw new Exception(404, "FileSysetm Provider {$type} does not exist");
}
