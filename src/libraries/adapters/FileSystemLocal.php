<?php
/**
 * Plain FS implementation for FileSystemInterface
 *
 * This class defines the functionality defined by FileSystemInterface for a plain Filesystem.
 * @author Hub Figuiere <hub@figuiere.net>
 */
class FileSystemLocal implements FileSystemInterface
{
  private $config;
  private $root;
  private $urlBase;

  public function __construct($config = null, $params = null)
  {
    if(is_null($config))
      $this->config = getConfig()->get();
    else
      $this->config = $config;

    if(!is_null($params) && isset($params['db']))
      $this->db = $params['db'];
    else
      $this->db = getDb();

    $this->root = $this->config->localfs->fsRoot;
    $this->host = $this->config->localfs->fsHost;
  }

  /**
    * Deletes a photo (and all generated versions) from the file system.
    * To get a list of all the files to delete we first have to query the database and find out what versions exist.
    *
    * @param string $id ID of the photo to delete
    * @return boolean
    */
  public function deletePhoto($photo)
  {
    foreach($photo as $key => $value)
    {
      if(strncmp($key, 'path', 4) === 0) {
        $path = $this->normalizePath($value);
        if(file_exists($path) && !@unlink($path))
          return false;
      }
    }
    return true;
  }

  public function downloadPhoto($photo)
  {
    $fp = fopen($photo['pathOriginal'], 'r');
    return $fp;
  }

  /**
    * Gets diagnostic information for debugging.
    *
    * @return array
    */
  public function diagnostics()
  {
    $utilityObj = new Utility;
    $diagnostics = array();
    if(is_writable($this->root))
      $diagnostics[] = $utilityObj->diagnosticLine(true, 'File system is writable.');
    else
      $diagnostics[] = $utilityObj->diagnosticLine(false, 'File system is NOT writable.');

    $ch = curl_init(sprintf('%s://%s/', trim($utilityObj->getProtocol(false)), $this->host));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    $result = curl_exec($ch);
    $resultCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    if($resultCode == '403')
      $diagnostics[] = $utilityObj->diagnosticLine(true, 'Photo path correctly returns 403.');
    else
      $diagnostics[] = $utilityObj->diagnosticLine(false, sprintf('Photo path returns %d instead of 403.', $resultCode));

    return $diagnostics;
  }

  /**
    * Executes an upgrade script
    *
    * @return void
    */
  public function executeScript($file, $filesystem)
  {
    if($filesystem != 'local')
      return;

    $status = include $file;
    return $status;
  }

  /**
   * Get photo will copy the photo to a temporary file.
   *
   */
  public function getPhoto($filename)
  {
    $filename = $this->normalizePath($filename);
    if(file_exists($filename)) {
      $tmpname = tempnam($this->config->paths->temp, 'opme');
      copy($filename, $tmpname);
      return $tmpname;
    }
    return false;
  }

  public function putPhoto($localFile, $remoteFile, $dateTaken)
  {
    if(!file_exists($localFile))
    {
      getLogger()->warn("The photo {$localFile} does not exist so putPhoto failed");
      return false;
    }

    $remoteFile = $this->normalizePath($remoteFile);
    // create all the directories to the file
    $dirname = dirname($remoteFile);
    if(!file_exists($dirname)) {
      mkdir($dirname, 0775, true);
    }
    getLogger()->info(sprintf('Copying from %s to %s', $localFile, $remoteFile));
    return copy($localFile, $remoteFile);
  }

  public function putPhotos($files)
  {
    foreach($files as $file)
    {
      list($localFile, $remoteFileArr) = each($file);
      $remoteFile = $remoteFileArr[0];
      $dateTaken = $remoteFileArr[1];
      $res = $this->putPhoto($localFile, $remoteFile, $dateTaken);
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

  /**
    * Return any meta data which needs to be stored in the photo record
    * @return array
    */
  public function getMetaData($localFile)
  {
    return array();
  }

  public function initialize($isEditMode)
  {
    if(!file_exists($this->root)) {
      @mkdir($this->root, 0775, true);
    }
    if(file_exists($this->root))
    {
      return true;
    }
    else
    {
      getLogger()->crit("Could not create {$this->root}");
      return false;
    }
  }

  /**
    * Identification method to return array of strings.
    *
    * @return array
    */
  public function identity()
  {
    return array('local');
  }

  public function normalizePath($path)
  {
    return $this->root . $path;
  }

  public function getRoot()
  {
    return $this->root;
  }
}
