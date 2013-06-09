<?php
/**
 * SkyDrive FS implementation for FileSystemInterface
 *
 * This class defines the functionality defined by FileSystemInterface for a plain Filesystem.
 * @author Gareth J. Greenaway <gareth@wiked.org>
 */
class FileSystemSkyDrive implements FileSystemInterface
{
  private $config;
  private $root;

  public function __construct($config = null, $params = null)
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

    $this->skyDriveFolder = $this->config->skyDrive->skyDriveFolder;
                                     
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
  }

  public function downloadPhoto($photo)
  {
    getLogger()->warn("Calling downloadPhoto: " . $photo);
    $fp = fopen($photo['pathOriginal'], 'r');
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
    getLogger()->warn("Calling getPhoto: " . $filename);

    $session = getSession();
    $fileCookieName = substr($filename, 1);
    if (!$session->get($fileCookieName)) 
    {    
      $parent = $this->getFolderId($fileCookieName);
      $photo = $this->skyDrive->getFileByName(basename($filename), $parentID = $parent);
      $session->set($fileCookieName, $photo);
    } else {
      $photo = $session->get($fileCookieName);
    }
    
    getLogger()->warn("getPhoto photo is : " . print_r($photo,1));    
    getLogger()->warn("Photo: " . $filename . " ID: " . $photo->id);

    $tmpname = '/tmp/'.uniqid('opme', true);
    $fp = fopen($tmpname, 'w+');    
    $res = $this->skyDrive->download($fileID = $photo->id, $returnDownloadLink = true);
    
    // Temp Download
    $ch = curl_init(); 
    curl_setopt($ch, CURLOPT_FILE, $fp);
    curl_setopt($ch, CURLOPT_HEADER, 0); 
    curl_setopt($ch, CURLOPT_URL, $res);
    //curl_setopt($ch, CURLOPT_VERBOSE, TRUE);    
    curl_exec($ch);
    curl_close($ch);
    
    fclose($fp);
    //return $res->isOK() ? $tmpname : false;
    return $tmpname;
  }

  public function putPhoto($localFile, $remoteFile, $dateTaken)
  {
    getLogger()->warn("Calling putPhoto");
    
    $path = substr(dirname($remoteFile), 1);
    $session = getSession();
    
    // Get the folderID
    $folderCookie = preg_replace("/\//", "/\_/", $path);
    if (!$session->get($folderCookie)) 
    {          
      $folders = preg_split("/\//", $path);
      
      $parent = $this->skyDriveFolder;
      foreach ($folders as $folder) {
        $response = $this->skyDrive->getFileByName($folder, $parentID = $parent);
        getLogger()->warn("Calling getPhoto inside foreach: " . print_r($response,1));

        // if $response says folder doesn't exist we should create it        
        if (is_null($response)) {
          getLogger()->warn("Creating $folder");
          $response = $this->skyDrive->createFolder($parent, $folder, $folder);        
          getLogger()->warn(print_r($response, 1));          
        }
                
        $parent = $response->id;
      }
      $parent = $session->set($folderCookie, $parent);
      
    } else {
        $parent = $session->get($folderCookie);    
    }
    $response = $this->skyDrive->upload($localFile, basename($remoteFile), $parent);
    getLogger()->warn(print_r($response, 1));
    
    return true;    
  }

  public function putPhotos($files)
  {
    getLogger()->warn("Calling putPhotos " . print_r($files,1));
    
    $session = getSession();
    foreach($files as $file)
    {
      list($localFile, $remoteFileArr) = each($file);
      $remoteFile = substr($remoteFileArr[0], 1);
      $dateTaken = $remoteFileArr[1];
      getLogger()->warn("Calling putPhotos " . $remoteFile);

      $folderCookie = preg_replace("/\//", "/\_/", dirname($remoteFile));
      if (!$session->get($folderCookie)) 
      {      
        $folders = preg_split("/\//", dirname($remoteFile));      
        $parent = $this->skyDriveFolder;      
        foreach ($folders as $folder) {
          $response = $this->skyDrive->getFileByName($folder, $parentID = $parent);
          getLogger()->warn("Calling getPhoto inside foreach: " . print_r($response,1));

          // if $response says folder doesn't exist we should create it        
          if (is_null($response)) {
            getLogger()->warn("Creating $folder");
            $response = $this->skyDrive->createFolder($parent, $folder, $folder);        
          }          
          $parent = $response->id;
        }
        $parent = $session->set($folderCookie, $parent);        
      } else {
        $parent = $session->get($folderCookie);
      }
      
      $normalized_remoteFile = $this->normalizePath($remoteFile);
        
      $response = $this->skyDrive->upload($localFile, basename($normalized_remoteFile), $parent);
      getLogger()->warn(print_r($response,1));
              
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
  
  public function getFolderId($path) 
  {
    $session = getSession();  
    $folderCookie = preg_replace("/\//", "/\_/", dirname($path));
    if (!$session->get($folderCookie)) {
      $folders = preg_split("/\//", dirname($path));
      $parent = $this->skyDriveFolder;
      foreach ($folders as $folder) {
        $response = $this->skyDrive->getFileByName($folder, $parentID = $parent);
        $parent = $response->id;
      }      
      $session->set($folderCookie, $parent);
    } else {
      $parent = $session->get($folderCookie);
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
      $parent = $this->getFolderId($path);
      $response = $this->skyDrive->getFileByName(basename($path), $parentID = $parent);
      $photo = $this->skyDrive->download($response->id, $returnDownloadLink = true);
      $session->set($path, $photo);
    } else {
      $photo = $session->get($path);
    }    
    getLogger()->warn($photo);
    
    header("Location: $photo");

  }  
}
