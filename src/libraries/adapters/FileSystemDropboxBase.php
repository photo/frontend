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
  private $parent;
  public function __construct($parent)
  {
    $this->directoryMask = 'Y_m_F';
    $oauth = new Dropbox_OAuth_PHP(Utility::decrypt(getConfig()->get('credentials')->dropboxKey), Utility::decrypt(getConfig()->get('credentials')->dropboxSecret));
    $oauth->setToken(array(
      'token' => Utility::decrypt(getConfig()->get('credentials')->dropboxToken),
      'token_secret' => Utility::decrypt(getConfig()->get('credentials')->dropboxTokenSecret)
    ));
    $this->dropbox = new Dropbox_API($oauth, Dropbox_API::ROOT_SANDBOX);
    $this->dropboxFolder = getConfig()->get('dropbox')->dropboxFolder;
    $this->parent = $parent;
  }

  public function deletePhoto($id)
  {
    $photo = getDb()->getPhoto($id);
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

  public function diagnostics()
  {
    $diagnostics = array();
    try
    {
      $queryDropboxFolder = $this->dropbox->getMetaData($this->dropboxFolder);
      if(isset($queryDropboxFolder['is_deleted']) && $queryDropboxFolder['is_deleted'] == 1)
        $diagnostics[] = Utility::diagnosticLine(false, 'The specified Dropbox directory has been deleted.');
      else
        $diagnostics[] = Utility::diagnosticLine(true, 'The Dropbox directory exists and looks okay.');
    }
    catch(Dropbox_Exception_NotFound $e)
    {
      $diagnostics[] = Utility::diagnosticLine(false, 'Could not get meta data for your Dropbox Directory.');
    }
    catch(Dropbox_Exception $e)
    {
      $diagnostics[] = Utility::diagnosticLine(false, 'An unknown error occured when trying to connect to Dropbox.');
    }
    return $diagnostics;
  }

  public function initialize()
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

    if($folderDoesNotExist)
    {
      try
      {
        $createFolderResponse = $this->dropbox->createFolder($this->dropboxFolder);
        $dropboxStatus = true;
      }
      catch(Dropbox_Exception $e)
      {
        getLogger()->crit(sprintf('Could not create folder. Message: %s', $e->getMessage()));
      }
    }

    return $dropboxStatus;
  }

  public function putPhoto($localFile, $remoteFile)
  {
    if(strpos($remoteFile, '/original/') !== false)
    {
      $exif = Photo::readExif($localFile);
      $directory = urlencode(date($this->directoryMask, $exif['dateTaken']));
      if(!$this->putFileInDirectory($directory, $localFile, basename($remoteFile)))
        return false;
    }
    return true;
  }

  public function putPhotos($files)
  {
    foreach($files as $file)
    {
      list($localFile, $remoteFile) = each($file);
      if(strpos($remoteFile, '/original/') !== false)
      {
        $exif = Photo::readExif($localFile);
        $directory = urlencode(date($this->directoryMask, $exif['dateTaken']));
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

    if($createDirectory)
    {
      try
      {
        getLogger()->info(sprintf('Creating dropbox directory %s', $destinationDirectory));
        $this->dropbox->createFolder($destinationDirectory);
      }
      catch(Dropbox_Exception $e)
      {
        getLogger()->info('Failed creating dropbox directory. Message: ' . $e->getMessage());
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
      getLogger()->crit(sprintf('Could not put file on dropbox. Message: %s', $e->getMessage()));
    }
    return false;
  }
}
