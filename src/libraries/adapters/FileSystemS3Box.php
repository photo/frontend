<?php
/**
 * Box adapter that extends much of the FileSystemS3 logic
 *
 * This class defines the functionality defined by FileSystemInterface for a plain Filesystem.
 * @author Jaisen Mathai <jaisen@jmathai.com>
 */
class FileSystemS3Box extends FileSystemS3 implements FileSystemInterface
{
  private $root;
  private $urlBase;
  private $box;

  public function __construct()
  {
    parent::__construct();
    $this->box = new FileSystemBoxBase($this);
  }

  public function deletePhoto($photo)
  {
    return $this->box->deletePhoto($photo) && parent::deletePhoto($photo);
  }

  public function downloadPhoto($photo)
  {
    $url = $this->box->getFileUrl($photo);
    $fp = fopen($url, 'r');
    return $fp;
  }

  /**
    * Gets diagnostic information for debugging.
    *
    * @return array
    */
  public function diagnostics()
  {
    return array_merge($this->box->diagnostics(), parent::diagnostics());
  }

  /**
    * Executes an upgrade script
    *
    * @return void
    */
  public function executeScript($file, $filesystem)
  {
    if($filesystem == 'box')
      echo file_get_contents($file);
    else
      parent::executeScript($file, $filesystem);
  }

  /**
    * Gets meta data for a file
    *
    * @return array
    */
  public function getMetaData($localFile)
  {
    return $this->box->getMetaData($localFile);
  }

  /**
    * Get the hostname for the remote filesystem to be used in constructing public URLs.
    * @return string
    */
  /*public function getHost()
  {
    return $this->host;
  }*/

  public function initialize($isEditMode)
  {
    return $this->box->initialize($isEditMode) && parent::initialize($isEditMode);
  }

  /**
    * Identification method to return array of strings.
    *
    * @return array
    */
  public function identity()
  {
    return array_merge(array('box'), parent::identity());
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

    return $this->box->putPhoto($localFile, $remoteFile, $dateTaken) && $parentStatus;
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
    return $this->box->putPhotos($files) && parent::putPhotos($parentFiles);
  }

  public function normalizePath($path)
  {
    return parent::normalizePath($path);
  }
}
