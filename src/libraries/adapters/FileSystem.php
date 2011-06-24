<?php
interface FileSystemInterface
{
  public function deletePhoto($id);
  public function getPhoto($filename);
  public function putPhoto($localFile, $remoteFile);
  public function putPhotos($files);
  public function initialize();
  //private function normalizePhoto($raw);
}

function getFs(/*$type, $opts*/)
{
  static $filesystem, $type, $opts;
  if(func_num_args() == 2)
  {
    $type = func_get_arg(0);
    $opts = func_get_arg(1);
  }
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

  throw new Exception("FileSysetm Provider {$type} does not exist", 404);
}
