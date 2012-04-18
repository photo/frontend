<?php
/**
 * RemoteStorage adapter that extends much of the FileSystemLocal logic
 *
 * This class defines the functionality defined by FileSystemInterface for a plain Filesystem.
 * @author Hub Figuiere <hub@figuiere.net>
 * @author Jaisen Mathai <jaisen@jmathai.com>
 */

class FileSystemLocalRemoteStorage extends FileSystemLocal implements FileSystemInterface
{
  private $root;
  private $urlBase;
  private $remoteStorage;
  public function __construct()
  {
    parent::__construct();
    $this->remoteStorage = new RemoteStorage('https://owncube.com/apps/remoteStorage/WebDAV.php/michiel/remoteStorage/pictures', 'cmVtb3RlU3RvcmFnZTo0ZjhiZjkxOWQwY2Vm');
    $fsConfig = getConfig()->get('localfs');
    $this->root = $fsConfig->fsRoot;
    $this->host = $fsConfig->fsHost;
  }

  public function deletePhoto($photo)
  {
    foreach($photo as $key => $value)
    {
      if(strncmp($key, 'path', 4) === 0) {
        if(!$this->remoteStorage->deleteItem($value))
          return false;
       }
    }
    getLogger()->warn("all deleted on remoteStorage, now deleting locally:");
    $ret = parent::deletePhoto($photo);
    getLogger()->warn(var_export($ret, true));
    return $ret;
  }

  /**
    * Gets diagnostic information for debugging.
    *
    * @return array
    */
  public function diagnostics()
  {
    return parent::diagnostics();
  }

  /**
    * Executes an upgrade script
    *
    * @return void
    */
  public function executeScript($file, $filesystem)
  {
    if($filesystem == 'dropbox')
      echo file_get_contents($file);
    else
      parent::executeScript($file, $filesystem);
  }

  /**
   * Get photo will copy the photo to a temporary file.
   *
   */
  public function getPhoto($filename)
  {
    return parent::getPhoto($filename);
  }

  public function putPhoto($localFile, $remoteFile)
  {
    $parentStatus = true;
    if(strpos($remoteFile, '/original/') === false)
      $parentStatus = parent::putPhoto($localFile, $remoteFile);

    return $this->remoteStorage->pushItem($localFile, $remoteFile) && $parentStatus;
  }

  public function putPhotos($files)
  {
    $parentFiles = array();
    foreach($files as $file)
    {
      list($local, $remote) = each($file);
      if(strpos($remote, '/original/') === false)
        $parentFiles[] = $file;
      if(!$this->remoteStorage->pushItem($local, $remote))
        return false;
    }

    return parent::putPhotos($parentFiles);
  }

  /**
    * Get the hostname for the remote filesystem to be used in constructing public URLs.
    * @return string
    */
  public function getHost()
  {
    return $this->host;
  }

  public function initialize($isEditMode)
  {
    return parent::initialize($isEditMode);
  }

  /**
    * Identification method to return array of strings.
    *
    * @return array
    */
  public function identity()
  {
    return array_merge(array('remotestorage'), parent::identity());
  }

  public function normalizePath($path)
  {
    return parent::normalizePath($path);
  }
}
