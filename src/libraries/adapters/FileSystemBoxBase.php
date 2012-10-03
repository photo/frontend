<?php
/**
 * Box adapter that extends much of the FileSystemLocal logic
 *
 * This class defines the functionality defined by FileSystemInterface for a plain Filesystem.
 * @author Hub Figuiere <hub@figuiere.net>
 * @author Jaisen Mathai <jaisen@jmathai.com>
 */
class FileSystemBoxBase
{
  const statusCreated = 'create_ok';
  const statusFolderExists = 's_folder_exists';
  const statusUploadOk = 'upload_ok';
  const statusFileInfoOk = 's_get_file_info';

  private $config, $parent, $box, $boxFolderId, $metaDataMap = array();
  public function __construct($parent, $config = null, $params = null)
  {
    $this->config = !is_null($config) ? $config : getConfig()->get();
    $this->directoryMask = 'Y_m_F';
    $utilityObj = new Utility;
    // TODO encrypt
    $this->box = new Box_Rest_Client($this->config->credentials->boxKey);
    $this->box->auth_token = $this->config->credentials->boxToken;
    $this->boxFolderId = $this->config->box->boxFolderId;
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

  public function getFileUrl($photo)
  {
    $fileId = null;
    if(isset($photo['extraFileSystem']['boxFileId']) && !empty($photo['extraFileSystem']['boxFileId']))
      $fileId = $photo['extraFileSystem']['boxFileId'];
    else
      return false;

    try
    {
      $file = $this->box->file($fileId);
      if(empty($file))
      {
        getLogger()->warn(sprintf('Could not retrieve file %s from Box with file id %s', $photo['id'], $fileId));
        return false;
      }

      return $file->download_url($this->box);
    }
    catch(Box_Rest_Client_Exception $e)
    {
      getLogger()->crit(sprintf('Could not get Box file (%s) URL (%s). Message: %s', $fileId, $photo['id'], $e->getMessage()));
      return false;
    }
  }

  public function getMetaData($localFile)
  {
    if(isset($this->metaDataMap[$localFile]))
      return $this->metaDataMap[$localFile];

    return null;
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

  public function putPhoto($localFile, $remoteFile)
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
      $photoObj = new Photo;
      $exif = $photoObj->readExif($localFile);
      $directory = urlencode(date($this->directoryMask, $exif['dateTaken']));
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
      list($localFile, $remoteFile) = each($file);
      if(strpos($remoteFile, '/original/') !== false && file_exists($localFile))
      {
        $photoObj = new Photo;
        $exif = $photoObj->readExif($localFile);
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
    // TODO confirm is this is the best way to do this
    //  it maybe a noop but calling get_file_info might be faster
    try
    {
      $newFolder = new Box_Client_Folder();
      $newFolder->attr('name',$directory);
      $newFolder->attr('parent_id', $this->boxFolderId);
      $newFolder->attr('share',0);
      $status = $this->box->create($newFolder);
      if($status !== self::statusCreated && $status !== self::statusFolderExists)
      {
        getLogger()->warn(sprintf('Box API returned an unexpected response of (%s) from folder create call', $status));
        return false;
      }
    }
    catch(Box_Rest_Client_Exception $e)
    {
      getLogger()->warn('Box exception from folder create call', $e);
      return false;
    }

    try
    {
      // The way Box_Rest_Client works it uses the file's name as the display name
      $file = new Box_Client_File($localFile, $destinationName);
      $file->attr('folder_id', $newFolder->attr('folder_id'));
      $result = $this->box->upload($file, array('new_copy' => '1'), true);
      if($result === self::statusUploadOk)
      {
        $this->metaDataMap[$localFile] = array('boxFileId' => $file->attr('id'));
        getLogger()->info(sprintf('Successfully stored file (%s) on Box.', $destinationName));
        return true;
      }
      else
      {
        getLogger()->crit('Could not put file on Box.', $e);
        return false;
      }
    }
    catch(Box_Rest_Client_Exception $e)
    {
      getLogger()->warn('Box exception from upload call', $e);
      return false;
    }
  }
}
