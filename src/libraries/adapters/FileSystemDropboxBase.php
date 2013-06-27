<?php
/**
 * Dropbox adapter that extends much of the FileSystemLocal logic
 *
 * This class defines the functionality defined by FileSystemInterface for a plain Filesystem.
 * @author Hub Figuiere <hub@figuiere.net>
 * @author Jaisen Mathai <jaisen@jmathai.com>
 */
class FileSystemDropboxBase
{
  private $config, $parent;
  public function __construct($parent, $config = null, $params = null)
  {
    $this->config = !is_null($config) ? $config : getConfig()->get();
    $this->directoryMask = 'Y_m_F';
    $utilityObj = new Utility;
    $oauth = new Dropbox_OAuth_PHP($utilityObj->decrypt($this->config->credentials->dropboxKey), $utilityObj->decrypt($this->config->credentials->dropboxSecret));
    $oauth->setToken(array(
      'token' => $utilityObj->decrypt($this->config->credentials->dropboxToken),
      'token_secret' => $utilityObj->decrypt($this->config->credentials->dropboxTokenSecret)
    ));
    $this->dropbox = new Dropbox_API($oauth, Dropbox_API::ROOT_SANDBOX);
    $this->dropboxFolder = $this->config->dropbox->dropboxFolder;
    $this->parent = $parent;
  }

  public function deletePhoto($photo)
  {
    $directory = urlencode(date($this->directoryMask, $photo['dateTaken']));
    try
    {
      $this->dropbox->delete(sprintf('%s/%s/%s', $this->dropboxFolder, $directory, basename($photo['pathOriginal'])));
    }
    catch(Dropbox_Exception_NotFound $e)
    {
      getLogger()->info('Photo does not exist on dropbox. Skipping delete operation.');
    }
    catch(Dropbox_Exception $e)
    {
      getLogger()->crit(sprintf('Could not delete photo (%s). Message: %s', $id, $e->getMessage()));
      return false;
    }
    return true;
  }

  public function getFileUrl($photo, $key = 'dateTaken')
  {
    $directory = urlencode(date($this->directoryMask, $photo[$key]));
    try
    {
      return $this->dropbox->getFileUrl(sprintf('%s/%s/%s', $this->dropboxFolder, $directory, basename($photo['pathOriginal'])));
    }
    catch(Exception $e)
    {
      getLogger()->crit(sprintf('Could not get Dropbox file URL using %s (%s). Message: %s', $key, $photo['id'], $e->getMessage()));
      return false;
    }
  }

  // Gh-1012
  //  Since we originally used dateUploaded this ensures backwards compatability
  public function getFilePointer($photo)
  {
    $url = $this->getFileUrl($photo, 'dateTaken');
    $fp = fopen($url, 'r');
    if(!$fp)
    {
      getLogger()->warn(sprintf('Could not load photo %s from dateTaken location. %s', $photo['id'], $url));
      $url = $this->getFileUrl($photo, 'dateUploaded');
      $fp = fopen($url, 'r');

      if(!$fp)
        getLogger()->warn(sprintf('Could not load photo %s from dateUploaded location. %s', $photo['id'], $url));
    }

    return $fp;
  }

  public function diagnostics()
  {
    $diagnostics = array();
    $utilityObj = new Utility;
    try
    {
      $queryDropboxFolder = $this->dropbox->getMetaData($this->dropboxFolder);
      if(isset($queryDropboxFolder['is_deleted']) && $queryDropboxFolder['is_deleted'] == 1)
        $diagnostics[] = $utilityObj->diagnosticLine(false, 'The specified Dropbox directory has been deleted.');
      else
        $diagnostics[] = $utilityObj->diagnosticLine(true, 'The Dropbox directory exists and looks okay.');
    }
    catch(Dropbox_Exception_NotFound $e)
    {
      $diagnostics[] = $utilityObj->diagnosticLine(false, 'Could not get meta data for your Dropbox Directory.');
    }
    catch(Dropbox_Exception $e)
    {
      $diagnostics[] = $utilityObj->diagnosticLine(false, 'An unknown error occured when trying to connect to Dropbox.');
    }
    return $diagnostics;
  }

  public function initialize($isEditMode)
  {
    $dropboxStatus = false;
    $folderDoesNotExist = false;
    try
    {
      $queryDropboxFolder = $this->dropbox->getMetaData($this->dropboxFolder);
      if(isset($queryDropboxFolder['is_deleted']) && $queryDropboxFolder['is_deleted'] == 1)
        $folderDoesNotExist = true;
      else
        $dropboxStatus = true;
    }
    catch(Dropbox_Exception_NotFound $e)
    {
      $folderDoesNotExist = true;
    }
    catch(Exception $e)
    {
      getLogger()->crit('Call to getMetaData failed during initialize.', $e);
      return false;
    }

    if($folderDoesNotExist)
    {
      try
      {
        $createFolderResponse = $this->dropbox->createFolder($this->dropboxFolder);
        $dropboxStatus = true;
      }
      catch(Dropbox_Exception $e)
      {
        getLogger()->crit('Could not create folder.', $e);
      }
    }

    return $dropboxStatus;
  }

  public function putPhoto($localFile, $remoteFile, $dateTaken)
  {
    if(isset($_POST['uploadSource']) && $_POST['uploadSource'] === 'dropbox')
      return true;

    if(!file_exists($localFile))
    {
      getLogger()->warn("The photo {$localFile} does not exist so putPhoto failed");
      return false;
    }

    if(strpos($remoteFile, '/original/') !== false)
    {
      $directory = urlencode(date($this->directoryMask, $dateTaken));
      if(!$this->putFileInDirectory($directory, $localFile, basename($remoteFile)))
        return false;
    }
    return true;
  }

  public function putPhotos($files)
  {
    if(isset($_POST['uploadSource']) && $_POST['uploadSource'] === 'dropbox')
      return true;

    foreach($files as $file)
    {
      list($localFile, $remoteFileArr) = each($file);
      $remoteFile = $remoteFileArr[0];
      $dateTaken = $remoteFileArr[1];
      if(strpos($remoteFile, '/original/') !== false && file_exists($localFile))
      {
        $directory = urlencode(date($this->directoryMask, $dateTaken));
        if(!$this->putFileInDirectory($directory, $localFile, basename($remoteFile)))
          return false;
      }
    }
    return true;
  }

  private function putFileInDirectory($directory, $localFile, $destinationName)
  {
    $createDirectory = false;
    $destinationDirectory = sprintf('%s/%s', $this->dropboxFolder, $directory);
    try
    {
      $this->dropbox->getMetaData($destinationDirectory);
      if(isset($queryDropboxFolder['is_deleted']) && $queryDropboxFolder['is_deleted'] == 1)
        $createDirectory = true;
    }
    catch(Dropbox_Exception_NotFound $e)
    {
      $createDirectory = true;
    }
    catch(Exception $e)
    {
      getLogger()->warn('Dropbox exception from getMetaData call', $e);
      return false;
    }

    if($createDirectory)
    {
      try
      {
        getLogger()->info(sprintf('Creating dropbox directory %s', $destinationDirectory));
        $this->dropbox->createFolder($destinationDirectory);
      }
      catch(Dropbox_Exception $e)
      {
        getLogger()->info('Failed creating dropbox directory.', $e);
        return false;
      }
    }

    try
    {
      $this->dropbox->putFile(sprintf('%s/%s', $destinationDirectory, $destinationName), $localFile);
      getLogger()->info(sprintf('Successfully stored file (%s) on dropbox.', $destinationName));
      return true;
    }
    catch(Dropbox_Exception $e)
    {
      getLogger()->crit('Could not put file on dropbox.', $e);
    }
    return false;
  }
}
