<?php
/**
 * CX adapter that extends much of the FileSystemLocal logic
 *
 * This class defines the functionality defined by FileSystemInterface for a plain Filesystem.
 * @author Jaisen Mathai <jaisen@jmathai.com>
 */
class FileSystemCXBase
{
  private $config, $parent, $cx, $boxFolderId, $metaDataMap = array();
  public function __construct($parent, $config = null, $params = null)
  {
    $this->directoryMask = 'Y/m-F';
    $this->config = !is_null($config) ? $config : getConfig()->get();
    $utilityObj = new Utility;
    $this->cx = new CloudExperience($utilityObj->decrypt($this->config->credentials->cxKey), $utilityObj->decrypt($this->config->credentials->cxSecret));
    $this->cx->setAccessToken($utilityObj->decrypt($this->config->credentials->cxToken));
    $this->parent = $parent;
  }

  public function deletePhoto($photo)
  {
    $filePath = null;
    if(isset($photo['extraFileSystem']['cxFilePath']) && !empty($photo['extraFileSystem']['cxFilePath']))
      $filePath = $photo['extraFileSystem']['cxFilePath'];
    else
      return false;

    $resp = $this->cx->post(sprintf('/files/delete/%s', $filePath));
    return $resp['status'] == 'ok';
  }

  public function getFileUrl($photo)
  {
    $filePath = null;
    if(isset($photo['extraFileSystem']['cxFilePath']) && !empty($photo['extraFileSystem']['cxFilePath']))
      $filePath = $photo['extraFileSystem']['cxFilePath'];
    else
      return false;

    $url = $this->cx->getUrl(sprintf('/data/self:/%s', $filePath), true);
    return $url;
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

    // TODO replace with search API
    $this->cx->post(sprintf('/files/mkdir/self:/Photos/%s', $directory));
    $params = array(
      'file_content_type' => 'image/jpg',
      'file' => sprintf('@%s', $localFile)
    );
    $photoPath = sprintf('Photos/%s/%s', $directory, $destinationName);
    $res = $this->cx->upload(sprintf('/data/self:/%s', $photoPath), $params);
    if(!isset($res['id']))
    {
      getLogger()->crit(sprintf('Could not save file on CX : %s', var_export($res, 1)));
      return false;
    }

    $this->metaDataMap[$localFile] = array('cxFileId' => $res['id'], 'cxFilePath' => $photoPath);
    return true;
  }
}
