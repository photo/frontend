<?php
/**
 * AppDotNet adapter that extends much of the FileSystemLocal logic
 *
 * This class defines the functionality defined by FileSystemInterface for a plain Filesystem.
 * @author Jaisen Mathai <jaisen@jmathai.com>
 */
class FileSystemAppDotNetBase
{
  private $config, $parent, $metaDataMap;
  public function __construct($parent, $config = null, $params = null)
  {
    $this->config = !is_null($config) ? $config : getConfig()->get();
    $utilityObj = new Utility;
    $this->adn = new AppDotNet(null, null);
    $this->adn->setAccessToken($this->config->credentials->adnToken);
    $this->parent = $parent;
  }

  public function deletePhoto($photo)
  {
    $fileId = null;
    if(isset($photo['extraFileSystem']['adnFileId']) && !empty($photo['extraFileSystem']['adnFileId']))
      $fileId = $photo['extraFileSystem']['adnFileId'];
    else
      return false;

    try
    {
      $this->adn->deleteFile($fileId);
      return true;
    }
    catch(AppDotNetException $e)
    {
      getLogger()->crit(sprintf('Could not delete photo (%s). Message: %s', $id, $e->getMessage()));
      return false;
    }
  }

  public function getFileUrl($photo)
  {
    $fileId = null;
    if(isset($photo['extraFileSystem']['adnFileId']) && !empty($photo['extraFileSystem']['adnFileId']))
      $fileId = $photo['extraFileSystem']['adnFileId'];
    else
      return false;

    try
    {
      $file = $this->adn->getFile($fileId);
      if(empty($file))
      {
        getLogger()->warn(sprintf('Could not retrieve file %s from ADN with file id %s', $photo['id'], $fileId));
        return false;
      }

      return $file['url'];
    }
    catch(AppDotNetException $e)
    {
      getLogger()->crit(sprintf('Could not get ADN file (%s) URL (%s). Message: %s', $fileId, $photo['id'], $e->getMessage()));
      return false;
    }
  }

  public function getFilePointer($photo)
  {
    $url = $this->getFileUrl($photo);
    error_log($url);
    $fp = fopen($url, 'r');
    if(!$fp)
    {
      getLogger()->warn(sprintf('Could not load photo %s from dateTaken location. %s', $photo['id'], $url));
      return false;
    }

    return $fp;
  }

  public function getMetaData($localFile)
  {
    if(isset($this->metaDataMap[$localFile]))
      return $this->metaDataMap[$localFile];

    return null;
  }

//public function diagnostics()
//{
//  $diagnostics = array();
//  $utilityObj = new Utility;
//  try
//  {
//    $queryDropboxFolder = $this->dropbox->getMetaData($this->dropboxFolder);
//    if(isset($queryDropboxFolder['is_deleted']) && $queryDropboxFolder['is_deleted'] == 1)
//      $diagnostics[] = $utilityObj->diagnosticLine(false, 'The specified Dropbox directory has been deleted.');
//    else
//      $diagnostics[] = $utilityObj->diagnosticLine(true, 'The Dropbox directory exists and looks okay.');
//  }
//  catch(Dropbox_Exception_NotFound $e)
//  {
//    $diagnostics[] = $utilityObj->diagnosticLine(false, 'Could not get meta data for your Dropbox Directory.');
//  }
//  catch(Dropbox_Exception $e)
//  {
//    $diagnostics[] = $utilityObj->diagnosticLine(false, 'An unknown error occured when trying to connect to Dropbox.');
//  }
//  return $diagnostics;
//}

//public function initialize($isEditMode)
//{
//  $dropboxStatus = false;
//  $folderDoesNotExist = false;
//  try
//  {
//    $queryDropboxFolder = $this->dropbox->getMetaData($this->dropboxFolder);
//    if(isset($queryDropboxFolder['is_deleted']) && $queryDropboxFolder['is_deleted'] == 1)
//      $folderDoesNotExist = true;
//    else
//      $dropboxStatus = true;
//  }
//  catch(Dropbox_Exception_NotFound $e)
//  {
//    $folderDoesNotExist = true;
//  }
//  catch(Exception $e)
//  {
//    getLogger()->crit('Call to getMetaData failed during initialize.', $e);
//    return false;
//  }

//  if($folderDoesNotExist)
//  {
//    try
//    {
//      $createFolderResponse = $this->dropbox->createFolder($this->dropboxFolder);
//      $dropboxStatus = true;
//    }
//    catch(Dropbox_Exception $e)
//    {
//      getLogger()->crit('Could not create folder.', $e);
//    }
//  }

//  return $dropboxStatus;
//}

  public function putPhoto($localFile, $remoteFile, $dateTaken)
  {
    if(!file_exists($localFile))
    {
      getLogger()->warn("The photo {$localFile} does not exist so putPhoto failed");
      return false;
    }

    if(strpos($remoteFile, '/original/') !== false)
    {
      if(!$this->putFileInDirectory($localFile, basename($remoteFile)))
        return false;
    }
    return true;
  }

  public function putPhotos($files)
  {
    foreach($files as $file)
    {
      list($localFile, $remoteFileArr) = each($file);
      $remoteFile = $remoteFileArr[0];
      $dateTaken = $remoteFileArr[1];
      if(strpos($remoteFile, '/original/') !== false && file_exists($localFile))
      {
        if(!$this->putFileInDirectory($localFile, basename($remoteFile)))
          return false;
      }
    }
    return true;
  }

  private function putFileInDirectory($localFile, $destinationName)
  {
    try
    {
      $file = $this->adn->createFile($localFile, array('metadata' => 'com.trovebox', 'name' => $destinationName));
      $this->metaDataMap[$localFile] = array('adnFileId' => $file['id']);
      getLogger()->info(sprintf('Successfully stored file (%s) on ADN.', $destinationName));
      return true;
    }
    catch(AppDotNetException $e)
    {
      getLogger()->crit('Could not put file on ADN.', $e);
    }
    return false;
  }
}

