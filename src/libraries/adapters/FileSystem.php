<?php
/**
 * Interface for the file system models.
 *
 * This defines the interface for any model that wants to interact with a remote file system.
 * @author Jaisen Mathai <jaisen@jmathai.com>
 */
interface FileSystemInterface
{
  public function deletePhoto($id);
  public function getPhoto($filename);
  public function putPhoto($localFile, $remoteFile);
  public function putPhotos($files);
  public function getHost();
  public function initialize();
}

/**
  * The public interface for instantiating a file system obect.
  * This returns the appropriate type of object by reading the config.
  * Accepts a set of params that must include a type and targetType
  *
  * @param string $type Optional type parameter which defines the type of file system.
  * @param array $opts Options which can be used by the file system adapter.
  * @return object A file system object that implements FileSystemInterface
  */
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
    case 'localfs':
      $filesystem = new FileSystemLocal($opts);
      break;
  }
  
  if($filesystem)
    return $filesystem;

  throw new Exception("FileSystem Provider {$type} does not exist", 404);
}
