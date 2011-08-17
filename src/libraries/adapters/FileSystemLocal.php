<?php
/** 
 * Plain FS implementation for FileSystemInterface
 *
 * This class defines the functionality defined by FileSystemInterface for a plain Filesystem.
 * @author Hub Figuiere <hub@figuiere.net>
 */
class FileSystemLocal implements FileSystemInterface
{
  private $root;
  private $urlBase;

  public function __construct($opts)
  {
    $config = getConfig()->get('localfs');
    $this->root = $config->fsRoot;
    if(!file_exists($this->root)) {
      mkdir($this->root, 0775, true);
    }
    $this->host = $config->fsHost;
  }

  public function deletePhoto($id)
  {
    $photo = getDb()->getPhoto($id);
    foreach($photo as $key => $value)
    {
      if(strncmp($key, 'path', 4) === 0) {
        $path = self::normalizePath($value);
        $ret = unlink($path);
      }
    }
    return $ret;
  }

  /**
   * Get photo will copy the photo to a temporary file.
   * 
   */
  public function getPhoto($filename)
  {
    $filename = self::normalizePath($filename);
    if(file_exists($filename)) {
      $tmpname = '/tmp/'.uniqid('opme', true);
      copy($filename, $tmpname);
      return $tmpname;
    }
    return false;
  }

  public function putPhoto($localFile, $remoteFile)
  {
    $remoteFile = self::normalizePath($remoteFile);
    // create all the directories to the file
    $dirname = dirname($remoteFile);
    if(!file_exists($dirname)) {
      mkdir($dirname, 0775, true);
    }
    //
    return copy($localFile, $remoteFile);
  }

  public function putPhotos($files)
  {
    foreach($files as $file)
    {
      list($localFile, $remoteFile) = each($file);
      $res = $this->putPhoto($localFile, $remoteFile);
      if(!$res)
        return false;
    }
    return true;
  }

  /**
    * Get the hostname for the remote filesystem to be used in constructing public URLs.
    * @return string
    */
  public function getHost()
  {
    return $this->host;
  }

  public function initialize()
  {
    return file_exists($this->root);
  }

  private function normalizePath($path)
  {
    return $this->root . $path;
  }
}
