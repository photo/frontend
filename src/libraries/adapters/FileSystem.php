<?php
/**
 * Interface for the file system models.
 *
 * This defines the interface for any model that wants to interact with a remote file system.
 * @author Jaisen Mathai <jaisen@jmathai.com>
 */
interface FileSystemInterface
{
  public function deletePhoto($photo);
  public function getPhoto($filename);
  public function putPhoto($localFile, $remoteFile);
  public function putPhotos($files);
  public function getHost();
  public function initialize($isEditMode);
  public function identity();
  public function executeScript($file, $filesysem);
  public function diagnostics();
  public function normalizePath($filename);
  // TODO enable this
  //public function inject($name, $value);
}

/**
  * The public interface for instantiating a file system obect.
  * This returns the appropriate type of object by reading the config.
  * Accepts a set of params that must include a type and targetType
  *
  * @param string $type Optional type parameter which defines the type of file system.
  * @return object A file system object that implements FileSystemInterface
  */
function getFs(/*$type*/)
{
  static $filesystem, $type;
  if($filesystem)
    return $filesystem;

  if(func_num_args() == 1)
    $type = func_get_arg(0);

  // load configs only once
  if(!$type)
    $type = getConfig()->get('systems')->fileSystem;

  switch($type)
  {
    case 'Local':
      $filesystem = new FileSystemLocal();
      break;
    case 'LocalDropbox':
      $filesystem = new FileSystemLocalDropbox();
      break;
    case 'S3':
      $filesystem = new FileSystemS3();
      break;
    case 'S3Dropbox':
      $filesystem = new FileSystemS3Dropbox();
      break;
  }

  if($filesystem)
    return $filesystem;

  throw new Exception("FileSystem Provider {$type} does not exist", 404);
}
