<?php
/**
 * RemoteStorage adapter that extends much of the FileSystemLocal logic
 *
 * This class defines the functionality defined by FileSystemInterface for a plain Filesystem.
 * @author Hub Figuiere <hub@figuiere.net>
 * @author Jaisen Mathai <jaisen@jmathai.com>
 */

class RemoteStorage {
  private $picturesBaseUrl;
  private $token;
  function __construct($setPicturesBaseUrl, $setToken) {
    $this->picturesBaseUrl = $setPicturesBaseUrl;
    $this->token = $setToken;
  }
  function doCurl($verb, $remotePath, $dataFile=null) {
    getLogger()->warn("doCurl {$verb} {$this->picturesBaseUrl}{$remotePath} {$dataFile}");
    $ch = curl_init($this->picturesBaseUrl.$remotePath);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Authorization: Bearer '.$this->token));
    curl_setopt($ch, CURLOPT_HEADER, 0);
    if($verb=='PUT') {
      curl_setopt($ch, CURLOPT_PUT, 1);
      $fp = fopen($dataFile, 'r');
      if($fp) {
        curl_setopt($ch, CURLOPT_INFILE, $fp);
        curl_setopt($ch, CURLOPT_INFILESIZE, filesize($dataFile));
      } else {
        return false;
      }
    } else if($verb=='GET') {
      $fp = fopen($dataFile, 'w');
      if($fp) {
        curl_setopt($ch, CURLOPT_FILE, $fp);
      } else {
        return false;
      }
    } else {
      curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $verb);
    }
    $result = curl_exec($ch);
    $resultCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    var_dump($result);
    var_dump($resultCode);
    return $result;
  }
  function deleteItem($remotePath) {
    getLogger()->warn("deleteItem {$remotePath}");
    $result = $this->doCurl('DELETE', $remotePath);
    return $result;
  }
  function fetchItem($remotePath, $localPath) {
    getLogger()->warn("fetchItemSync {$remotePath} {$localPath}");
    $result = $this->doCurl('GET', $remotePath, $localPath);
    return $result;
  }
  function pushItem($localPath, $remotePath) {
    getLogger()->warn("pushItemSync {$localPath} {$remotePath}");
    $result = $this->doCurl('PUT', $remotePath, $localPath);
    return $result;
  }
}
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
    return $this->remoteStorage->deleteItem($photo) && parent::deletePhoto($photo);
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
