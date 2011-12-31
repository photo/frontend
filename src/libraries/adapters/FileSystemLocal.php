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

  public function __construct($config = null, $params = null)
  {
    if(is_null($config))
      $config = getConfig()->get();
    if(!is_null($params) && isset($params['db']))
      $this->db = $params['db'];
    else
      $this->db = getDb();

    $this->root = $config->localfs->fsRoot;
    $this->host = $config->localfs->fsHost;
  }

  public function deletePhoto($photo)
  {
    foreach($photo as $key => $value)
    {
      if(strncmp($key, 'path', 4) === 0) {
        $path = self::normalizePath($value);
        if(!@unlink($path))
          return false;
      }
    }
    return true;
  }

  /**
    * Gets diagnostic information for debugging.
    *
    * @return array
    */
  public function diagnostics()
  {
    $diagnostics = array();
    if(is_writable($this->root))
      $diagnostics[] = Utility::diagnosticLine(true, 'File system is writable.');
    else
      $diagnostics[] = Utility::diagnosticLine(false, 'File system is NOT writable.');

    $ch = curl_init(sprintf('%s://%s/', trim(Utility::getProtocol(false)), $this->host));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    $result = curl_exec($ch);
    $resultCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    if($resultCode == '403')
      $diagnostics[] = Utility::diagnosticLine(true, 'Photo path correctly returns 403.');
    else
      $diagnostics[] = Utility::diagnosticLine(false, sprintf('Photo path returns %d instead of 403.', $resultCode));

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
    $filename = self::normalizePath($filename);
    if(file_exists($filename)) {
      $tmpname = tempnam(getConfig()->get('paths')->temp, 'opme');
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
    getLogger()->info(sprintf('Copying from %s to %s', $localFile, $remoteFile));
    return copy($localFile, $remoteFile);
  }

  public function putPhotos($files)
  {
    foreach($files as $file)
    {
      list($localFile, $remoteFile) = each($file);
      $res = self::putPhoto($localFile, $remoteFile);
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
