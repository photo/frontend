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
    $this->directoryMask = 'Y_m_F';
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
    getLogger()->warn("Calling deletePhoto: " . print_r($photo,1));
    foreach($photo as $key => $value)
    {
      if(strncmp($key, 'path', 4) === 0) 
      {
        $session = getSession();
        $fileCookieName = substr($value, 1);
        if (!$session->get($fileCookieName)) 
        {    
          $parent = $this->getFolderId(dirname($value));
          $filename = $this->skyDrive->getFileByName(basename($value), $parentID = $parent);
        } else {
          $filename = $session->get($fileCookieName);
        }      
        getLogger()->warn(print_r($filename, 1));
        $response = $this->skyDrive->deleteFileByID($filename->id);
        getLogger()->warn(print_r($response, 1));
      }        
    }

    return true;
  }

  public function downloadPhoto($photo)
  {
    getLogger()->warn("Calling downloadPhoto: " . print_r($photo,1));
    //$fp = fopen($photo['pathOriginal'], 'r');
    //return $fp;
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
    getLogger()->warn("Calling getPhoto: " . $filename);

    $rootFolder = $this->skyDrive->getMyRoot();
    $skyDriveFolder = $this->skyDrive->getFileByName($this->skyDriveFolder, $parentID = $rootFolder->id);

    $parent = $this->getFolderId(dirname($filename));
    getLogger()->warn("Parent: " . $parent);            
    $photo = $this->skyDrive->getFileByName(basename($filename), $parentID = $parent);
    getLogger()->warn("Photo: " . print_r($photo, 1));      
    
    getLogger()->warn("getPhoto photo is : " . print_r($photo,1));    
    getLogger()->warn("Photo: " . $filename . " ID: " . $photo->id);

    $tmpname = '/tmp/'.uniqid('opme', true);
    $fp = fopen($tmpname, 'w+');    
    //$res = $this->skyDrive->download($fileID = $photo->id, $returnDownloadLink = true);
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
    getLogger()->warn("Calling putPhoto $localFile $remoteFile");
    
    $shell_output = shell_exec("file $localFile");
    getLogger()->warn("$shell_output");
    
    $rootFolder = $this->skyDrive->getMyRoot();
    $skyDriveFolder = $this->skyDrive->getFileByName($this->skyDriveFolder, $parentID = $rootFolder->id);

    $parent = $skyDriveFolder->id;

    // Strip Beginning Slash
    if ($remoteFile[0] == "/")
      $path = substr($remoteFile, 1);
    else
      $path = $remoteFile;

    $folders = preg_split("/\//", dirname($path));
    foreach ($folders as $folder) {
      $response = $this->skyDrive->getFileByName($folder, $parentID = $parent);
      //getLogger()->warn("Folder " . $folder . " inside putPhoto foreach: " . print_r($response,1));

      // if $response says folder doesn't exist we should create it        
      if (is_null($response)) {
        //getLogger()->warn("Creating $folder");
        $response = $this->skyDrive->createFolder($parent, $folder, $folder);        
        //getLogger()->warn(print_r($response, 1));          
      }
                
      //getLogger()->warn(print_r($response, 1));
      $parent = $response->id;
    }

    $response = $this->skyDrive->upload($localFile, basename($remoteFile), $parent);
    getLogger()->warn(print_r($response, 1));
    
    return true;    
  }

  public function putPhotos($files)
  {
    getLogger()->warn("Calling putPhotos " . print_r($files,1));
    
    $rootFolder = $this->skyDrive->getMyRoot();
    $skyDriveFolder = $this->skyDrive->getFileByName($this->skyDriveFolder, $parentID = $rootFolder->id);

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

      $folders = preg_split("/\//", dirname($path));
      foreach ($folders as $folder)
      {
        $response = $this->skyDrive->getFileByName($folder, $parentID = $parent);
        //getLogger()->warn("Calling putPhotos inside foreach: " . print_r($response,1));

        // if $response says folder doesn't exist we should create it        
        if (is_null($response)) {
          //getLogger()->warn("Creating $folder");
          $response = $this->skyDrive->createFolder($parent, $folder, $folder);        
        }          
        $parent = $response->id;
      }
      
      $normalized_remoteFile = $this->normalizePath($remoteFile);
        
      $response = $this->skyDrive->upload($localFile, basename($normalized_remoteFile), $parent);
      //getLogger()->warn(print_r($response,1));
              
    }
    $responses = '';
    
    // Need to fix this so it only returns true if it was successfull 
    return true;
  }

  /**
    * Get the hostname for the remote filesystem to be used in constructing public URLs.
    * @return string
    */
  public function getHost()
  {
    $utilityObj = new Utility;  
    getLogger()->warn("Calling getHost");
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
    getLogger()->warn("Calling initialize");
    
    $skyDriveFolder = $this->skyDriveFolder;
    $rootFolder = $this->skyDrive->getMyRoot();

    // Check if folder exists    
    $response = $this->skyDrive->getFileByName($skyDriveFolder, $parentID = $rootFolder->id);

    // if $response says folder doesn't exist we should create it        
    if (is_null($response)) {
      getLogger()->warn("Creating $folder");
      $response = $this->skyDrive->createFolder($parent, $folder, $folder);        
      getLogger()->warn(print_r($response, 1));          
    }
    
    $parent = $response->id;
            
    // Once it exists, create the original directory
    $response = $this->skyDrive->createFolder($parent, "original", "original");     
        
    // if we can't create, then return false'
    // return false;
    
    // set configuration to be the folderID
    getConfig()->set('skydrive', $parent);
    
    /* Just return true for now until we figure out what to initialize */  
    return true;
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
    getLogger()->warn("Calling normalizePath");    
    return $this->root . $path;
  }

  public function getRoot()
  {
    getLogger()->warn("Calling getRoot");      
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

    $rootFolder = $this->skyDrive->getMyRoot();
    $skyDriveFolder = $this->skyDrive->getFileByName($this->skyDriveFolder, $parentID = $rootFolder->id);
    $parent = $skyDriveFolder->id;

    $folders = preg_split("/\//", $path);
    foreach ($folders as $folder)
    {
      $response = $this->skyDrive->getFileByName($folder, $parentID = $parent);
      $parent = $response->id;
    } 
    return $parent;  
  }
  
  public function view($path)
  {
    getLogger()->warn("Calling view");
    getLogger()->warn($path);
    $session = getSession();    

    if (!$session->get($path)) 
    {    
      $parent = $this->getFolderId(dirname($path));
      $response = $this->skyDrive->getFileByName(basename($path), $parentID = $parent);
      getLogger()->warn("response: " . print_r($response, 1));
      $photo = $response->picture;
      $photo2 = $this->skyDrive->download($response->id, $returnDownloadLink = true);
      $session->set($path, $photo);
    } else {
      $photo = $session->get($path);
    }    
    getLogger()->warn("Photo from object: " . $photo);
    getLogger()->warn("Photo from download: " . $photo2);
    
    header("Location: $photo");

  }  
}
