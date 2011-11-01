<?php
/**
 * Dropbox adapter that extends much of the FileSystemLocal logic
 *
 * This class defines the functionality defined by FileSystemInterface for a plain Filesystem.
 * @author Hub Figuiere <hub@figuiere.net>
 * @author Jaisen Mathai <jaisen@jmathai.com>
 */
class FileSystemS3Dropbox extends FileSystemS3 implements FileSystemInterface
{
  private $root;
  private $urlBase;
  private $dropbox;

  public function __construct()
  {
    parent::__construct();
    $this->dropbox = new FileSystemDropboxBase($this);
  }

  public function deletePhoto($id)
  {
    return $this->dropbox->deletePhoto($id) && parent::deletePhoto($id);
  }

  /**
    * Gets diagnostic information for debugging.
    *
    * @return array
    */
  public function diagnostics()
  {
    return array_merge($this->dropbox->diagnostics(), parent::diagnostics());
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
    * Get the hostname for the remote filesystem to be used in constructing public URLs.
    * @return string
    */
  /*public function getHost()
  {
    return $this->host;
  }*/

  public function initialize()
  {
    return $this->dropbox->initialize() && parent::initialize();
  }

  /**
    * Identification method to return array of strings.
    *
    * @return array
    */
  public function identity()
  {
    return array_merge(array('dropbox'), parent::identity());
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
    return $this->dropbox->putPhoto($localFile, $remoteFile) && parent::putPhoto($localFile, $remoteFile);
  }

  public function putPhotos($files)
  {
    return $this->dropbox->putPhotos($files) && parent::putPhotos($files);
  }

  public function normalizePath($path)
  {
    return parent::normalizePath($path);
  }
}
