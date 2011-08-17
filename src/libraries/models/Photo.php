<?php
/**
 * Photo model.
 *
 * Something related to photos, the guts are in this file.
 * Upload, update, delete and generate, oh my!
 * @author Jaisen Mathai <jaisen@jmathai.com>
 */
class Photo
{
  /**
    * Delete a photo from the remote database and remote filesystem.
    * This deletes the original photo and all versions.
    *
    * @param string $id ID of the photo
    * @return boolean 
    */
  public static function delete($id)
  {
    // TODO, validation
    // TODO, do not delete record from db - mark as deleted
    $fs = getFs();
    $db = getDb();
    $fileStatus = $fs->deletePhoto($id);
    $dataStatus = $db->deletePhoto($id);
    return $fileStatus && $dataStatus;
  }

  /**
    * Versions of a photo are stored by a deterministic key in the database.
    * Given $width, $height and $options this method returns that key.
    *
    * @param int $width Width of the photo to generate
    * @param int $height Height of the photo to generate
    * @param string $options Options for the photo such as crop (CR) and greyscale (BW)
    * @return string 
    */
  public static function generateCustomKey($width, $height, $options = null)
  {
    return sprintf('path%s', self::generateFragment($width, $height, $options));
  }

  /**
    * Generates the "fragment" for the photo version.
    * This fragment is used in the file name as well as the database key.
    *
    * @param int $width Width of the photo to generate
    * @param int $height Height of the photo to generate
    * @param string $options Options for the photo such as crop (CR) and greyscale (BW)
    * @return string 
    */
  public static function generateFragment($width, $height, $options)
  {
    $fragment = "{$width}x{$height}";
    if(!empty($options))
      $fragment .= "x{$options}";
    return $fragment;
  }

  /**
    * Does the opposite of self::generateFragment.
    * Given a string fragment this will return it's parts as an array.
    * The $options must start with a width and height (i.e. 800x600)
    *
    * @param string $options Options for the photo such as crop (CR) and greyscale (BW)
    * @return array 
    */
  public static function generateFragmentReverse($options)
  {
    $options = explode('x', $options);
    $width = array_shift($options);
    $height = array_shift($options);
    $options = implode('x', $options);
    return array('width' => $width, 'height' => $height, 'options' => $options);
  }

  /**
    * When a custom version of a photo needs to be generated it must be accompanied by a hash.
    * This method generates that hash based on a secret and normalization of parameters.
    * The method takes any number of arguments and processes them all.
    *
    * @param string $param1 any parameter value
    * ...
    * @param string $paramN any parameter value
    * @return string 
    */
  public static function generateHash(/*$args1, $args2, ...*/)
  {
    $args = func_get_args();
    foreach($args as $k => $v)
    {
      if(strlen($v) == 0)
        unset($args[$k]);
    }
    $args[] = getConfig()->get('secrets')->secret;
    return substr(sha1(implode('.', $args)), 0, 5);
  }

  /**
    * Generates the default paths given a photo name.
    * These paths will also be the initial versions of the photo that are stored in the file system and database.
    *
    * @param string $photoName File name of the photo
    * @return array 
    */
  public static function generatePaths($photoName)
  {
    $photoName = time() . '-' . preg_replace('/[^a-zA-Z0-9.-_]/', '-', $photoName);
    return array(
      'pathOriginal' => sprintf('/original/%s/%s', date('Ym'), $photoName),
      'pathBase' => sprintf('/base/%s/%s', date('Ym'), $photoName)
    );
  }

  /**
    * Photo urls are either to existing files on the remote filesystem or a call back to this server to generate it.
    * The requested photo is looked up in the database and if it exists is returned.
    * If it does not exist then a URL which will generate, store and return it when called is returned.
    *
    * @param array $photo The photo object as returned from the database.
    * @param int $width The width of the requested photo.
    * @param int $height The height of the requested photo.
    * @param string $options Optional options to be applied on the photo
    * @return mixed string URL on success, FALSE on failure
    */
  public static function generateUrlPublic($photo, $width, $height, $options = null)
  {
    $key = self::generateCustomKey($width, $height, $options);
    if(isset($photo[$key]))
      return $photo[$key];
    elseif(isset($photo['id']))
      return "http://{$_SERVER['HTTP_HOST']}".self::generateUrlInternal($photo['id'], $width, $height, $options);
    else
      return false;
  }

  /**
    * This is the method called if a version of a requested photo does not exist.
    * It simply returns a url pointing back to the server which will generate the photo when called.
    *
    * @param string $id The id of the photo.
    * @param int $width The width of the requested photo.
    * @param int $height The height of the requested photo.
    * @param string $options Optional options to be applied on the photo
    * @return string 
    */
  // TODO make private and called via an API in the photo controller
  public static function generateUrlInternal($id, $width, $height, $options = null)
  {
    $fragment = self::generateFragment($width, $height, $options);
    $hash = self::generateHash($id, $width, $height, $options);
    return sprintf('/photo/%s/create/%s/%s.jpg', $id, $hash, $fragment);
  }

  /**
    * Generate a version of the photo as specified by the width, height and options.
    * This method requres the $hash ve validated to keep random versions of images to be created.
    * The photo is generated, uploaded to the remote file system and added to the database.
    * Operations are done in place on a downloaded version of the base photo and this file name is returned.
    *
    * @param string $id The id of the photo.
    * @param int $width The width of the requested photo.
    * @param int $height The height of the requested photo.
    * @param string $options Optional options to be applied on the photo
    * @return mixed string on success, false on failure
    */
  // TODO change name to generate()
  public static function generateImage($id, $hash, $width, $height, $options = null)
  {
    if(!self::isValidateHash($hash, $id, $width, $height, $options))
      return false;

    $photo = getDb()->getPhoto($id);
    $filename = getFs()->getPhoto($photo['pathBase']);
    $image = getImage($filename);
    $maintainAspectRatio = true;
    if(!empty($options))
    {
      $optionsArray = (array)explode('x', $options);
      foreach($optionsArray as $option)
      {
        switch($option)
        {
          case 'BW':
            $image->greyscale();
            break;
          case 'CR':
            $maintainAspectRatio = false;
            break;
        }
      }
    }

    $image->scale($width, $height, $maintainAspectRatio);

    $image->write($filename);
    $customPath = self::generateCustomUrl($photo['pathBase'], $width, $height, $options);
    $key = self::generateCustomKey($width, $height, $options);
    $resFs = getFs()->putPhoto($filename, $customPath);
    $resDb = getDb()->postPhoto($id, array($key => $customPath));
    if($resFs && $resDb)
      return $filename;

    return false;
  }

  /**
    * Update the attributes of a photo in the database.
    *
    * @param string $id The id of the photo.
    * @param array $attributes The attributes to save
    * @return mixed string on success, false on failure
    */
  public static function update($id, $attributes = array())
  {
    if(empty($attributes))
      return $id;

    if(isset($attributes['tags']) && !empty($attributes['tags']))
      $attributes['tags'] = Tag::sanitizeTagsAsString($attributes['tags']);

    $status = getDb()->postPhoto($id, $attributes);
    if(!$status)
      return false;

    return $id;
  }

  /**
    * Uploads a new photo to the remote file system and database.
    *
    * @param string $localFile The local file system path to the photo.
    * @param string $name The file name of the photo.
    * @param array $attributes The attributes to save
    * @return mixed string on success, false on failure
    */
  public static function upload($localFile, $name, $attributes = array())
  {
    $fs = getFs();
    $db = getDb();
    $id = User::getNextPhotoId();
    if($id === false)
    {
      getLogger()->crit('Could not fetch next photo ID');
      return false;
    }
    $paths = Photo::generatePaths($name);
    $exiftran = getConfig()->get('modules')->exiftran;
    if(is_executable($exiftran))
      exec(sprintf('%s -ai %s', $exiftran, escapeshellarg($localFile)));

    // resize the base image before uploading
    $localFileCopy = "{$localFile}-copy}";
    copy($localFile, $localFileCopy);


    $baseImage = getImage($localFileCopy);
    $baseImage->scale(getConfig()->get('photos')->baseSize, getConfig()->get('photos')->baseSize);
    $baseImage->write($localFileCopy);
    $uploaded = $fs->putPhotos(
      array(
        array($localFile => $paths['pathOriginal']),
        array($localFileCopy => $paths['pathBase'])
      )
    );
    if($uploaded)
    {
      getLogger()->info("Photo ({$id}) successfully stored on the file system");
      $exif = self::readExif($localFile);
      $defaults = array('title', 'description', 'tags', 'latitude', 'longitude');
      foreach($defaults as $default)
      {
        if(!isset($attributes[$default]))
          $attributes[$default] = null;
      }

      if(isset($attributes['tags']) && !empty($attributes['tags']))
        $attributes['tags'] = Tag::sanitizeTagsAsString($attributes['tags']);

      $dateUploaded = time();
      $dateTaken = @$exif['dateTaken'];
      $attributes = array_merge(
        self::getDefaultAttributes(),
        array(
          'hash' => sha1_file($localFile),
          'size' => intval(filesize($localFile)/1024),
          'exifCameraMake' => @$exif['cameraMake'],
          'exifCameraModel' => @$exif['cameraModel'],
          'width' => @$exif['width'],
          'height' => @$exif['height'],
          'dateTaken' => $dateTaken,
          'dateTakenDay' => date('d', $dateTaken),
          'dateTakenMonth' => date('m', $dateTaken),
          'dateTakenYear' => date('Y', $dateTaken),
          'dateUploaded' => $dateUploaded,
          'dateUploadedDay' => date('d', $dateUploaded),
          'dateUploadedMonth' => date('m', $dateUploaded),
          'dateUploadedYear' => date('Y', $dateUploaded),
          'pathOriginal' => $paths['pathOriginal'], 
          'pathBase' => $paths['pathBase']
        ),
        $attributes
      );
      $stored = $db->putPhoto($id, $attributes);
      unlink($localFile);
      unlink($localFileCopy);
      print $stored;
      if($stored)
      {
        getLogger()->info("Photo ({$id}) successfully stored to the database");
        return $id;
      }
    }

    getLogger()->info("Photo ({$id}) could NOT be stored to the file system");
    return false;
  }

  /**
    * Generates a path for a custom version of a photo.
    * This defines in a deterministic way what the URL for this version of the photo will be.
    *
    * @param string $basePath Path to the base version of the photo from the database.
    * @param int $width The width of the desired photo version.
    * @param int $height The height of the desired photo version.
    * @param string $options The options for the desired photo version.
    * @return string The path to be used for this photo.
    */
  private static function generateCustomUrl($basePath, $width, $height, $options)
  {
    $fragment = self::generateFragment($width, $height, $options);
    $customPath = preg_replace('#^/base/#', '/custom/', $basePath);
    $customName = substr($customPath, 0, strrpos($customPath, '.'));
    return "{$customName}_{$fragment}.jpg";
  }

  /**
    * The default attributes for a new photo.
    *
    * @return array Default values for a new photo
    */
  private static function getDefaultAttributes()
  {
    return array(
      'appId' => getConfig()->get('application')->appId,
      'host' => getFs()->getHost(), 
      'views' => 0,
      'status' => 1,
      'permission' => 0, // TODO
      'creativeCommons' => 'BY-NC'
    );
  }

  /**
    * Validates the hash component of the generate new photo request.
    *
    * @param $hash The hash to validate
    * @param $param2 One of the options
    * ...
    * @param $paramN One of the options
    * @return boolean 
    */
  private static function isValidateHash(/*$hash, $args1, $args2, ...*/)
  {
    $args = func_get_args();
    foreach($args as $k => $v)
    {
      if(strlen($v) == 0)
        unset($args[$k]);
    }
    $args[] = getConfig()->get('secrets')->secret;
    $hash = array_shift($args);
    return (substr(sha1(implode('.', $args)), 0, 5) == $hash);
  }

  /**
    * Reads exif data from a photo.
    *
    * @param $photo Path to the photo.
    * @return array 
    */
  private static function readExif($photo)
  {
    $exif = @exif_read_data($photo);
    if(!$exif)
      return null;

    $size = getimagesize($photo);
    $dateTaken = $exif['FileDateTime'];
    if(array_key_exists('DateTime', $exif))
    {
      $dateTime = explode(' ', $exif['DateTime']);
      $date = explode(':', $dateTime[0]);
      $time = explode(':', $dateTime[1]);
      $dateTaken = @mktime($time[0], $time[1], $time[2], $date[1], $date[2], $date[0]);
    }

    return array('dateTaken' => $dateTaken, 'width' => $size[0], 'height' => $size[1],
      'cameraModel' => @$exif['Model'], 'cameraMake' => @$exif['Make']);
  }
}
