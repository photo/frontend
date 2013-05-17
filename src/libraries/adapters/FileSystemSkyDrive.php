<?php
/**
 * SkyDrive FS implementation for FileSystemInterface
 *
 * This class defines the functionality defined by FileSystemInterface for a plain Filesystem.
 * @author Gareth J. Greenaway <gareth@wiked.org>
 */
class FileSystemSkyDrive implements FileSystemInterface
{
  private $config;
  private $root;

  public function __construct($config = null, $params = null)
  {
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
  }

  /**
   * Get photo will copy the photo to a temporary file.
   *
   */
  public function getPhoto($filename)
  {
  }

  public function putPhoto($localFile, $remoteFile, $dateTaken)
  {
  }

  public function putPhotos($files)
  {
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
