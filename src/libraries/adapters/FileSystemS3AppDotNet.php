<?php
/**
 * AppDotNet adapter that extends much of the FileSystemS3 logic
 *
 * This class defines the functionality defined by FileSystemInterface for a plain Filesystem.
 * @author Jaisen Mathai <jaisen@jmathai.com>
 */
class FileSystemS3AppDotNet extends FileSystemS3 implements FileSystemInterface
{
  private $root;
  private $urlBase;
  private $adn;

  public function __construct()
  {
    parent::__construct();
    $this->adn = new FileSystemAppDotNetBase($this);
  }

  public function deletePhoto($photo)
  {
    return $this->adn->deletePhoto($photo) && parent::deletePhoto($photo);
  }

  public function downloadPhoto($photo)
  {
    return $this->adn->getFilePointer($photo);
  }

  /**
    * Gets diagnostic information for debugging.
    *
    * @return array
    */
  public function diagnostics()
  {
    return array_merge($this->adn->diagnostics(), parent::diagnostics());
  }

  /**
    * Executes an upgrade script
    *
    * @return void
    */
  public function executeScript($file, $filesystem)
  {
    if($filesystem == 'adn')
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

  public function putPhoto($localFile, $remoteFile, $dateTaken)
  {
    $parentStatus = true;
    if(strpos($remoteFile, '/original/') === false)
      $parentStatus = parent::putPhoto($localFile, $remoteFile, $dateTaken);

    return $this->adn->putPhoto($localFile, $remoteFile, $dateTaken) && $parentStatus;
  }

  public function putPhotos($files)
  {
    $parentFiles = array();
    foreach($files as $file)
    {
      list($localFile, $remoteFileArr) = each($file);
      $remoteFile = $remoteFileArr[0];
      $dateTaken = $remoteFileArr[1];
      if(strpos($remoteFile, '/original/') === false)
        $parentFiles[] = $file;
    }
    return $this->adn->putPhotos($files) && parent::putPhotos($parentFiles);
  }

  /**
    * Get the hostname for the remote filesystem to be used in constructing public URLs.
    * @return string
    */
  /*public function getHost()
  {
    return $this->host;
  }*/

  /**
    * Return any meta data which needs to be stored in the photo record
    * @return array
    */
  public function getMetaData($localFile)
  {
    return $this->adn->getMetaData($localFile);
  }

  public function initialize($isEditMode)
  {
    return $this->adn->initialize($isEditMode) && parent::initialize($isEditMode);
  }

  /**
    * Identification method to return array of strings.
    *
    * @return array
    */
  public function identity()
  {
    return array_merge(array('adn'), parent::identity());
  }

  public function normalizePath($path)
  {
    return parent::normalizePath($path);
  }
}


