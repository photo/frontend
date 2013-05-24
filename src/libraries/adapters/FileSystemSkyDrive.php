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
    
    //getLogger()->warn("Calling _constuct");
    //getLogger()->warn("skyDriveClientID " . ($utilityObj->decrypt($this->config->credentials->skyDriveClientID)));
    //getLogger()->warn("skyDriveClientSecret " . ($utilityObj->decrypt($this->config->credentials->skyDriveClientSecret)));
    //getLogger()->warn("skyDriveRefreshToken " . ($utilityObj->decrypt($this->config->credentials->skyDriveRefreshToken)));
            
    $session = getSession();
    
    if (!$session->get('skyDrive'))
    {
      //getLogger()->warn("not in session, creating a new one");
      
      $this->skyDrive = new SkyDriveAPI(array(
                                'client_id' => $utilityObj->decrypt($this->config->credentials->skyDriveClientID),
                                'redirect_uri' => $callback,
                                'client_secret' => $utilityObj->decrypt($this->config->credentials->skyDriveClientSecret),
                                'refresh_token' => $utilityObj->decrypt($this->config->credentials->skyDriveRefreshToken),
                                )
                              );

      $session->set('skyDrive', serialize($this->skyDrive));
      
    } else {
      //getLogger()->warn("Already set");
      //getLogger()->warn(print_r($session->get("skyDrive"),1));  
      $this->skyDrive = unserialize($session->get('skyDrive'));
    }  

  
    if ($this->skyDrive->isAccessTokenExpired())
    {
      $accessToken = $this->skyDrive->refreshAccessToken();
      $this->skyDrive->setAccessToken($accessToken);
      getSession()->set('skyDriveAccessToken', $accessToken['access_token']);
    }
                                 
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
   * Get photo will copy the photo to a temporary file.
   *
   */
  public function getPhoto($filename)
  {
    getLogger()->warn("Calling getPhoto");  
  }

  public function putPhoto($localFile, $remoteFile, $dateTaken)
  {
    getLogger()->warn("Calling putPhoto");
  }

  public function putPhotos($files)
  {
    getLogger()->warn("Calling putPhotos " . print_r($files,1));
    
    //$queue = $this->getBatchRequest();
    foreach($files as $file)
    {

      list($localFile, $remoteFileArr) = each($file);
      $remoteFile = $remoteFileArr[0];
      $dateTaken = $remoteFileArr[1];
      getLogger()->warn("Calling putPhotos " . $remoteFile);      

      $remoteFile = $this->normalizePath($remoteFile);
      
      $this->skyDrive->upload($localFile, basename($remoteFile), "folder.ca04824622699b37.CA04824622699B37!121");

    }
    $responses = '';
    //$responses = $this->fs->batch($queue)->send();
    //if(!$responses->areOK())
    //{
    //  foreach($responses as $resp)
    //    getLogger()->crit(var_export($resp, 1));
    //}
    //return $responses->areOK();    
    return true;
  }

  /**
    * Get the hostname for the remote filesystem to be used in constructing public URLs.
    * @return string
    */
  public function getHost()
  {
    return $this->host;
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
    return $this->root . $path;
  }

  public function getRoot()
  {
    return $this->root;
  }
}
