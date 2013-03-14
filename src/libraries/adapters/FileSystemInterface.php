<?php
/**
 * Interface for the file system models.
 *
 * This defines the interface for any model that wants to interact with a remote file system.
 * @author Jaisen Mathai <jaisen@jmathai.com>
 */
interface FileSystemInterface
{
  public function deletePhoto($photo);
  public function downloadPhoto($photo);
  public function getPhoto($filename);
  public function putPhoto($localFile, $remoteFile, $dateTaken);
  public function putPhotos($files);
  public function getHost();
  public function getMetaData($localFile);
  public function initialize($isEditMode);
  public function identity();
  public function executeScript($file, $filesysem);
  public function diagnostics();
  public function normalizePath($filename);
  // TODO enable this
  //public function inject($name, $value);
}
