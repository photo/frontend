<?php
/**
 * Box adapter that extends much of the FileSystemLocal logic
 *
 * This class defines the functionality defined by FileSystemInterface for a plain Filesystem.
 * @author Jaisen Mathai <jaisen@jmathai.com>
 */
class FileSystemBoxBase
{
  const statusCreatedOk = 'create_ok';
  const statusFolderExists = 's_folder_exists';
  const statusUploadOk = 'upload_ok';
  const statusFileInfoOk = 's_get_file_info';
  const statusDeleteOk = 's_delete_node';
  const statusGetAccountInfoOk = 'get_account_info_ok';

  private $config, $parent, $box, $boxFolderId, $metaDataMap = array();
  public function __construct($parent, $config = null, $params = null)
  {
    $this->directoryMask = 'Y-m-F';
    $this->config = !is_null($config) ? $config : getConfig()->get();
    $utilityObj = new Utility;
    // TODO encrypt
    $this->box = new Box_Rest_Client($utilityObj->decrypt($this->config->credentials->boxKey));
    $this->box->auth_token = $utilityObj->decrypt($this->config->credentials->boxToken);
    $this->boxFolderId = $this->config->box->boxFolderId;
    $this->parent = $parent;
  }

  public function deletePhoto($photo)
  {
    $fileId = null;
    if(isset($photo['extraFileSystem']['boxFileId']) && !empty($photo['extraFileSystem']['boxFileId']))
      $fileId = $photo['extraFileSystem']['boxFileId'];
    else
      return false;

    try
    {

      $resp = $this->box->post('delete',array('target' => 'file', 'target_id' => $fileId));
      if($resp['status'] !== self::statusDeleteOk)
      {
        getLogger()->crit(sprintf('Could not delete photo (%s).', $photo['id']));
        return false;
      }

      return true;
    }
    catch(Box_Rest_Client_Exception $e)
    {
      getLogger()->crit(sprintf('Could not delete photo (%s). Message: %s', $id, $e->getMessage()));
      return false;
    }
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
      $queryBoxAccount = $this->box->get('get_account_info');
      if($queryBoxAccount['status'] === self::statusGetAccountInfoOk)
      {
        $queryBoxFolder = $this->box->folder($this->boxFolderId);
        $diagnostics[] = $utilityObj->diagnosticLine(true, 'The Box was connected to successfully.');
        $diagnostics[] = $utilityObj->diagnosticLine(true, sprintf('Total space available: %s.', $queryBoxAccount['user']['space_amount']));
        $diagnostics[] = $utilityObj->diagnosticLine(true, sprintf('Total space used: %s.', $queryBoxAccount['user']['space_used']));
        if($queryBoxFolder->attr('id') !== null)
          $diagnostics[] = $utilityObj->diagnosticLine(true, 'The default folder for uploads is okay.');
        else
          $diagnostics[] = $utilityObj->diagnosticLine(false, 'The default folder for uploads encountered a problem. Uploads may not work.');
      }
      else
      {
        $diagnostics[] = $utilityObj->diagnosticLine(false, 'Could not connect to the box account.');
      }
    }
    catch(Box_Rest_Client_Exception $e)
    {
      $diagnostics[] = $utilityObj->diagnosticLine(false, 'An unexpected error occurred when accessing your Box account.');
    }
    return $diagnostics;
  }

  public function initialize($isEditMode)
  {
    // TODO have to implement the initialization flow
    return false;
    /*
    try
    {
      // create a folder named Photo[-n]
      $cnt = 0;
      while(true)
      {
        $newFolder = new Box_Client_Folder();
        if($cnt === 0)
          $newFolder->attr('name','Photos');
        else
          $newFolder->attr('name',sprintf('Photos-%s', $cnt));
        $newFolder->attr('parent_id', 0);
        $newFolder->attr('share',0);
        $resp = $this->box->create($newFolder);
        if($resp['status'] === self::statusCreatedOk)
          break;
        $cnt++;
      }
    }
    catch(Box_Rest_Client_Exception $e)
    {
      return false;
    }
    */
  }

  public function putPhoto($localFile, $remoteFile, $dateTaken)
  {
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
    // TODO confirm is this is the best way to do this
    //  it maybe a noop but calling get_file_info might be faster
    try
    {
      $newFolder = new Box_Client_Folder();
      $newFolder->attr('name',$directory);
      $newFolder->attr('parent_id', $this->boxFolderId);
      $newFolder->attr('share',0);
      $status = $this->box->create($newFolder);
      if($status !== self::statusCreatedOk && $status !== self::statusFolderExists)
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
