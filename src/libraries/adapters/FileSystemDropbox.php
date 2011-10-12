<?php
/**
 * Dropbox adapter that extends much of the FileSystemLocal logic
 *
 * This class defines the functionality defined by FileSystemInterface for a plain Filesystem.
 * @author Hub Figuiere <hub@figuiere.net>
 * @author Jaisen Mathai <jaisen@jmathai.com>
 */
class FileSystemDropbox extends FileSystemLocal implements FileSystemInterface
{
  private $root;
  private $urlBase;
  private $dropbox;
  private $dropboxFolder;
  private $directoryMask;

  public function __construct()
  {
    parent::__construct();
    $fsConfig = getConfig()->get('localfs');
    $this->root = $fsConfig->fsRoot;
    $this->host = $fsConfig->fsHost;
    $this->directoryMask = 'Y_m_F';
    $oauth = new Dropbox_OAuth_PHP(Utility::decrypt(getConfig()->get('credentials')->dropboxKey), Utility::decrypt(getConfig()->get('credentials')->dropboxSecret));
    $oauth->setToken(array(
      'token' => Utility::decrypt(getConfig()->get('credentials')->dropboxToken),
      'token_secret' => Utility::decrypt(getConfig()->get('credentials')->dropboxTokenSecret)
    ));
    $this->dropbox = new Dropbox_API($oauth);
    $this->dropboxFolder = getConfig()->get('dropbox')->dropboxFolder;
  }

  public function deletePhoto($id)
  {
    $photo = getDb()->getPhoto($id);
    $exif = Photo::readExif(parent::normalizePath($photo['pathOriginal']));
    $directory = urlencode(date($this->directoryMask, $exif['dateTaken']));
    try
    {
      $deleteStatus = $this->dropbox->delete(sprintf('%s/%s/%s', $this->dropboxFolder, $directory, basename($photo['pathOriginal'])));
    }
    catch(Dropbox_Exception $e)
    {
      getLogger()->crit(sprintf('Could not delete photo (%s). Message: %s', $id, $e->getMessage()));
      return false;
    }
    return parent::deletePhoto($id);
  }

  /**
   * Get photo will copy the photo to a temporary file.
   *
   */
  public function getPhoto($filename)
  {
    return parent::getPhoto($filename);
  }

  public function putPhoto($localFile, $remoteFile)
  {
    if(strpos($remoteFile, '/original/') !== false)
    {
      $exif = Photo::readExif($localFile);
      $directory = urlencode(date($this->directoryMask, $exif['dateTaken']));
      $this->putFileInDirectory($directory, $localFile, basename($remoteFile));
    }
    return parent::putPhoto($localFile, $remoteFile);
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
        $this->putFileInDirectory($directory, $localFile, basename($remoteFile));
      }
    }
    return parent::putPhotos($files);
  }

  /**
    * Get the hostname for the remote filesystem to be used in constructing public URLs.
    * @return string
    */
  public function getHost()
  {
    return $this->host;
  }

  public function initialize()
  {
    $localStatus = parent::initialize();
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

    return $localStatus && $dropboxStatus;
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

    $this->dropbox->putFile(sprintf('%s/%s', $destinationDirectory, $destinationName), $localFile);
  }
}
