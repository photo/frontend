<?php
/**
 * SkyDrive FS implementation for FileSystemInterface
 *
 * This class defines the functionality defined by FileSystemInterface for a plain Filesystem.
 * @author Gareth J. Greenaway <gareth@wiked.org>
 */
class FileSystemSkyDriveBase
{
  private $config, $parent, $root;
  
  public function __construct($parent, $config = null, $params = null)
  {
    $this->config = !is_null($config) ? $config : getConfig()->get();
    $this->directoryMask = 'Ym';
    $utilityObj = new Utility;

    $qs = '';
    $callback = sprintf('%s://%s%s%s', $utilityObj->getProtocol(false), getenv('HTTP_HOST'), '', $qs);                
    $session = getSession();
    
    if (!$session->get('skyDrive'))
    {
      
      $this->skyDrive = new SkyDriveAPI(array(
                                'client_id' => $utilityObj->decrypt($this->config->credentials->skyDriveClientID),
                                'redirect_uri' => $callback,
                                'client_secret' => $utilityObj->decrypt($this->config->credentials->skyDriveClientSecret),
                                'refresh_token' => $utilityObj->decrypt($this->config->credentials->skyDriveRefreshToken),
                                )
                              );

      $session->set('skyDrive', serialize($this->skyDrive));
      
    } else {
      $this->skyDrive = unserialize($session->get('skyDrive'));
    }  

    if ($this->skyDrive->isAccessTokenExpired())
    {
      $accessToken = $this->skyDrive->refreshAccessToken();
      $this->skyDrive->setAccessToken($accessToken);
      $session->set('skyDriveAccessToken', $accessToken['access_token']);
    }

    $this->skyDriveFolder = $this->config->skydrive->skyDriveFolder;
    $this->parent = $parent;
                                     
  }
  
  /**
    * Deletes a photo (and all generated versions) from the file system.
    * To get a list of all the files to delete we first have to query the database and find out what versions exist.
    *
    * @param string $id ID of the photo to delete
    * @return boolean
    */
  public function deletePhoto($photo)
  {
    $session = getSession();  
    foreach($photo as $key => $value)
    {
      if(strncmp($key, 'path', 4) === 0) 
      {
        $fileCookieName = substr($value, 1);
        if (!$session->get($fileCookieName)) 
        {    
          $parent = $this->getFolderId(dirname($value));
          $filename = $this->skyDrive->getFileByName(basename($value), $parentID = $parent);
        } else {
          $filename = $session->get($fileCookieName);
        }      
        $response = $this->skyDrive->deleteFileByID($filename->id);
      }        
    }

    return true;
  }

  public function downloadPhoto($photo)
  {
    $filename = basename($photo['pathOriginal']);
    $directory = urlencode(date($this->directoryMask, $photo['dateTaken']));
    $path = "/original/" . $directory;
        
    $parent = $this->getFolderId($path);
    if (is_null($parent))
    {
      $directory = urlencode(date($this->directoryMask, $photo['dateUploaded']));
      $path = "/original/" . $directory;
      $parent = $this->getFolderId($path);            
    }
        
    $photoObj = $this->skyDrive->getFileByName($filename, $parentID = $parent);
    $res = $this->skyDrive->download($fileID = $photoObj->id, $returnDownloadLink = true);
    
    // Switch https to http so we can use fopen
    $res = preg_replace("/https/", "http", $res);

    $fp = fopen($res, 'r');
    return $fp;
  }

  /**
    * Gets diagnostic information for debugging.
    *
    * @return array
    */
  public function diagnostics()
  {
    $utilityObj = new Utility;
    $diagnostics = array();
    if(is_writable($this->root))
      $diagnostics[] = $utilityObj->diagnosticLine(true, 'File system is writable.');
    else
      $diagnostics[] = $utilityObj->diagnosticLine(false, 'File system is NOT writable.');

    $ch = curl_init(sprintf('%s://%s/', trim($utilityObj->getProtocol(false)), $this->host));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    $result = curl_exec($ch);
    $resultCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    if($resultCode == '403')
      $diagnostics[] = $utilityObj->diagnosticLine(true, 'Photo path correctly returns 403.');
    else
      $diagnostics[] = $utilityObj->diagnosticLine(false, sprintf('Photo path returns %d instead of 403.', $resultCode));

    return $diagnostics;
  }

  /**
    * Executes an upgrade script
    *
    * @return void
    */
  public function executeScript($file, $filesystem)
  {
  }

  /**
    * Retrieves a photo from the remote file system as specified by $filename.
    * This file is stored locally and the path to the local file is returned.
    *
    * @param string $filename File name on the remote file system.
    * @return mixed String on success, FALSE on failure.
    */
  /**
   * Get photo will copy the photo to a temporary file.
   *
   */
  public function getPhoto($filename)
  {
    $rootFolder = $this->skyDrive->getMyRoot();
    $skyDriveFolder = $this->skyDrive->getFileByName($this->skyDriveFolder, $parentID = $rootFolder->id);
    $parent = $this->getFolderId(dirname($filename));
    $photo = $this->skyDrive->getFileByName(basename($filename), $parentID = $parent);

    $tmpname = '/tmp/'.uniqid('opme', true);
    $fp = fopen($tmpname, 'w+');    
    $res = $photo->picture;
    
    // Temp Download
    $ch = curl_init(); 
    curl_setopt($ch, CURLOPT_FILE, $fp);
    curl_setopt($ch, CURLOPT_HEADER, 0); 
    curl_setopt($ch, CURLOPT_URL, $res);
    //curl_setopt($ch, CURLOPT_VERBOSE, TRUE);    
    curl_exec($ch);
    curl_close($ch);
    
    fclose($fp);
    return $tmpname;
  }

  public function putPhoto($localFile, $remoteFile, $dateTaken)
  { 
    $session = getSession();    
    $rootFolder = $this->skyDrive->getMyRoot();
    $skyDriveFolder = $this->skyDrive->getFileByName($this->skyDriveFolder, $parentID = $rootFolder->id);
    $parent = $skyDriveFolder->id;

    // Strip Beginning Slash
    if ($remoteFile[0] == "/")
      $path = substr($remoteFile, 1);
    else
      $path = $remoteFile;

    $folderCookie = dirname($path);
    if (!$session->get($folderCookie)) 
    {
      $folders = preg_split("/\//", dirname($path));
      foreach ($folders as $folder) {
        $response = $this->skyDrive->getFileByName($folder, $parentID = $parent);

        // if $response says folder doesn't exist we should create it        
        if (is_null($response))
          $response = $this->skyDrive->createFolder($parent, $folder, $folder);        

        $parent = $response->id;
      }
      $session->set($folderCookie, $parent);      
    } else {
      $parent = $session->get($folderCookie);    
    }

    $response = $this->skyDrive->upload($localFile, basename($remoteFile), $parent);
    
    if (isset($response->id))
      return true;
    else
      return false;
    
  }

  public function putPhotos($files)
  {
    
    $session = getSession();    
    $rootFolder = $this->skyDrive->getMyRoot();
    $skyDriveFolder = $this->skyDrive->getFileByName($this->skyDriveFolder, $parentID = $rootFolder->id);

    $responses = true;
    
    foreach($files as $file)
    {
      list($localFile, $remoteFileArr) = each($file);
      $remoteFile = substr($remoteFileArr[0], 1);
      $dateTaken = $remoteFileArr[1];

      $parent = $skyDriveFolder->id;

      // Strip Beginning Slash
      if ($remoteFile[0] == "/")
        $path = substr($remoteFile, 1);
      else
        $path = $remoteFile;

      $folderCookie = dirname($path);
      if (!$session->get($folderCookie)) 
      {
        $folders = preg_split("/\//", dirname($path));
        foreach ($folders as $folder)
        {
          $response = $this->skyDrive->getFileByName($folder, $parentID = $parent);

          // if $response says folder doesn't exist we should create it        
          if (is_null($response))
            $response = $this->skyDrive->createFolder($parent, $folder, $folder);        

          $parent = $response->id;
        }
        $session->set($folderCookie, $parent);
      } else {
        $parent = $session->get($folderCookie);
      }         
      
      $normalized_remoteFile = $this->normalizePath($remoteFile);
      $response = $this->skyDrive->upload($localFile, basename($normalized_remoteFile), $parent);

      if (!isset($response->id))
        $responses = false;        
                    
    }
    return $responses;
  }

  /**
    * Get the hostname for the remote filesystem to be used in constructing public URLs.
    * @return string
    */
  public function getHost()
  {
    $utilityObj = new Utility;  
    return sprintf('%s/skydrive', getenv('HTTP_HOST'));
  }

  /**
    * Return any meta data which needs to be stored in the photo record
    * @return array
    */
  public function getMetaData($localFile)
  {
    return array();
  }

  public function initialize($isEditMode)
  { 
    $skyDriveFolder = $this->skyDriveFolder;
    $rootFolder = $this->skyDrive->getMyRoot();

    // Check if folder exists    
    $response = $this->skyDrive->getFileByName($skyDriveFolder, $parentID = $rootFolder->id);

    // if $response says folder doesn't exist we should create it        
    if (is_null($response))
      $response = $this->skyDrive->createFolder($parent, $folder, $folder);        

    $parent = $response->id;
            
    // Once it exists, create the original directory
    $response = $this->skyDrive->createFolder($parent, "original", "original");     
        
    // if we can't create, then return false
    if (isset($response->id))
      return true;
    else
      return false;
  }

  /**
    * Identification method to return array of strings.
    *
    * @return array
    */
  public function identity()
  {
    return array('local');
  }

  public function normalizePath($path)
  {
    return $this->root . $path;
  }

  public function getRoot()
  {
    return $this->root;
  }
  
  /*
    Grab the folder Id for the path from SkyDrive
    store the ids of the pieces as cookies so subsequent look ups are faster
    Here be dragons....
  */
  public function getFolderId($path) 
  {
    $session = getSession();
            
    // Strip Beginning Slash
    if ($path[0] == "/")
      $path = substr($path, 1);

    $folderCookie = $path;
    if (!$session->get($folderCookie)) 
    {
      $skyDriveFolder = $this->skyDriveFolder;
      $rootFolder = $this->skyDrive->getMyRoot();

      // get the id of the skyDriveFolder   
      $response = $this->skyDrive->getFileByName($skyDriveFolder, $parentID = $rootFolder->id);
        
      $folders = preg_split("/\//", $path);
      $parent = $response->id;
      $previousFolder = NULL;
      foreach ($folders as $folder) 
      { 
        if (is_null($previousFolder))
          $parentFolderCookie = $folder;
        else
          $parentFolderCookie = $previousFolder . "/" . $folder;

        if (!$session->get($parentFolderCookie))
        {
          $response = $this->skyDrive->getFileByName($folder, $parentID = $parent);
          $parent = $response->id;
          $session->set($parentFolderCookie, $parent);
        } else {
          $parent = $session->get($parentFolderCookie);
        }
        $previousFolder = $folder;
      }      
      $session->set($folderCookie, $parent);
    } else {    
      $parent = $session->get($folderCookie);
    }
    return $parent;  
  }
}
