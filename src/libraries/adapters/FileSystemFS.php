<?php
/** 
 * Plain FS implementation for FileSystemInterface
 *
 * This class defines the functionality defined by FileSystemInterface for a plain Filesystem.
 * @author Hub Figuiere <hub@figuiere.net>
 */
class FileSystemFS implements FileSystemInterface
{
  private $root;

  public function __construct($opts)
  {
    $this->root = getConfig()->get('fs')->fsRoot;
    if(!file_exists($this->root)) {
      mkdir($this->root, 0775, true);
    }
  }

  public function deletePhoto($id)
  {
    $photo = getDb()->getPhoto($id);
    foreach($photo as $key => $value)
    {
      if(strncmp($key, 'path', 4) === 0) {
        $path = self::normalizePath($value);
      }
      $ret = unlink($path);
    }
    return $ret;
  }

  public function getPhoto($filename)
  {
    $filename = self::normalizePath($filename);
    if(file_exists($filename)) {
      return $filename;
    }
    return false;
  }

  public function putPhoto($localFile, $remoteFile)
  {
    $remoteFile = self::normalizePath($remoteFile);
    return copy($localFile, $remoteFile);
  }

  public function putPhotos($files)
  {
    foreach($files as $file)
    {
      list($localFile, $remoteFile) = each($file);
      $res = $self->putPhot($localFile, $remoteFile);
      if(!$res)
        return false;
    }
    return true;
  }

  public function initialize()
  {
    return file_exists($this->root);
  }

  private function normalizePath($path)
  {
    return $root . $path;
  }

}


?>