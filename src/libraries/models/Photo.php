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
    * Adds the urls for the photo.
    * pathWxH is a static resource.
    * photoWxH is an enumerated array [path, width, height]
    *
    * @param array $photo the photo object
    * @param array $options Options for the photo such as crop (CR) and greyscale (BW)
    * @param string $protocol http or https
    * @return array
    */
  public static function addApiUrls($photo, $sizes, $protocol=null)
  {
    if($protocol === null)
      $protocol = Utility::getProtocol(false);

    foreach($sizes as $size)
    {
      $options = Photo::generateFragmentReverse($size);
      $fragment = self::generateFragment($options['width'], $options['height'], $options['options']);
      $path = self::generateUrlPublic($photo, $options['width'], $options['height'], $options['options'], $protocol);
      $photo["path{$size}"] = $path;
      if(strstr($fragment, 'xCR') === false)
      {
        $dimensions = self::getRealDimensions($photo['width'], $photo['height'], $options['width'], $options['height']);
        $photo["photo{$fragment}"] = array($path, $dimensions['width'], $dimensions['height']);
      }
      else
      {
        $photo["photo{$fragment}"] = array($path, $options['width'], $options['height']);
      }
    }
    $photo['url'] = self::getPhotoViewUrl($photo);
    return $photo;
  }

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
  public static function generate($id, $hash, $width, $height, $options = null)
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
    // TODO unlink $filename
    if($resFs && $resDb)
      return $filename;

    return false;
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
  public static function generateUrlPublic($photo, $width, $height, $options = null, $protocol = 'http')
  {
    $key = self::generateCustomKey($width, $height, $options);
    if(isset($photo[$key]))
      return "{$protocol}://{$photo['host']}{$photo[$key]}";
    elseif(isset($photo['id']))
      return "{$protocol}://{$_SERVER['HTTP_HOST']}".self::generateUrlInternal($photo['id'], $width, $height, $options);
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
    * Calculate the width and height of a scaled photo
    *
    * @param int $originalWidth The width of the original photo.
    * @param int $originalHeight The height of the original photo.
    * @param int $newWidth The width of the new photo.
    * @param int $newHeight The height of the new photo.
    * @return array
    */
  public static function getRealDimensions($originalWidth, $originalHeight, $newWidth, $newHeight)
  {
    if(empty($originalWidth) || empty($originalHeight))
      return array('width' => $newWidth, 'height' => $newHeight);

    $originalRatio = $originalWidth / $originalHeight;
    $newRatio = $newWidth / $newHeight;

    if($originalRatio <= $newRatio)
    {
      $height = $newHeight;
      $width = intval($newHeight / (1 / $originalRatio));
    }
    else
    {
      $width = $newWidth;
      $height = intval($newWidth / $originalRatio);
    }
    return array('width' => $width, 'height' => $height);
  }

  /** 
   * Parse the exif date
   *
   * @param $exif the exif block
   * @param $key the exif key to get the date from
   * @return the parsed date or false if not found
   */
  private static function parseExifDate($exif, $key)
  {
    if(array_key_exists($key, $exif))
    {
      $dateTime = explode(' ', $exif[$key]);
      $date = explode(':', $dateTime[0]);
      $time = explode(':', $dateTime[1]);
      return @mktime($time[0], $time[1], $time[2], $date[1], $date[2], $date[0]);
    }
    return false;
  }
  /**
    * Reads exif data from a photo.
    *
    * @param $photo Path to the photo.
    * @return array
    */
  public static function readExif($photo)
  {
    $exif = @exif_read_data($photo);
    if(!$exif)
      $exif = array();

    $size = getimagesize($photo);
    // DateTimeOriginal is the right thing. If it is not there
    // use DateTime which might be the date the photo was modified
    $parsedDate = self::parseExifDate($exif, 'DateTimeOriginal');
    if($parsedDate === false) 
    {
        $parsedDate = self::parseExifDate($exif, 'DateTime');    
	if($parsedDate === false)
	{
	    if(array_key_exists('FileDateTime', $exif))
	        $parsedDate = $exif['FileDateTime'];
            else
                $parsedDate = time();
        }
    }
    $dateTaken = $parsedDate;    

    $exif_array = array('dateTaken' => $dateTaken, 'width' => $size[0],
      'height' => $size[1], 'cameraModel' => @$exif['Model'],
      'cameraMake' => @$exif['Make'],
      'ISO' => @$exif['ISOSpeedRatings'],
      'exposureTime' => @$exif['ExposureTime']);

    if(isset($exif['GPSLongitude'])) {
      $exif_array['longitude'] = self::getGps($exif['GPSLongitude'], $exif['GPSLongitudeRef']);
    }

    if(isset($exif['GPSLatitude'])) {
      $exif_array['latitude'] = self::getGps($exif['GPSLatitude'], $exif['GPSLatitudeRef']);
    }

    $exif_array['FNumber'] = self::frac2Num(@$exif['FNumber']);
    $exif_array['focalLength'] = self::frac2Num(@$exif['FocalLength']);

    return $exif_array;
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

    $attributes = self::whitelistParams($attributes);
    if(isset($attributes['tags']) && !empty($attributes['tags']))
    {
      $attributes['tags'] = Tag::removeDuplicatesFromString($attributes['tags']);
      $attributes['tags'] = Tag::sanitizeTagsAsString($attributes['tags']);
    }
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
    $id = User::getNextId('photo');
    if($id === false)
    {
      getLogger()->crit('Could not fetch next photo ID');
      return false;
    }
    $attributes = self::whitelistParams($attributes);
    $paths = Photo::generatePaths($name);
    $exiftran = getConfig()->get('modules')->exiftran;
    if(is_executable($exiftran))
      exec(sprintf('%s -ai %s', $exiftran, escapeshellarg($localFile)));

    // resize the base image before uploading
    $localFileCopy = "{$localFile}-copy";
    getLogger()->info("Making a local copy of the uploaded image. {$localFile} to {$localFileCopy}");
    copy($localFile, $localFileCopy);

    $baseImage = getImage($localFileCopy);
    if(!$baseImage)
    {
      getLogger()->warn('Could not load image, possibly an invalid image file.');
      return false;
    }
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
      $iptc = self::readIptc($localFile);
      $defaults = array('title', 'description', 'tags', 'latitude', 'longitude');
      foreach($iptc as $iptckey => $iptcval)
      {
        if($iptckey == 'tags')
        {
          $iptcval = implode(',', $iptcval);
        }
        $attributes[$iptckey] = $iptcval;
      }
      foreach($defaults as $default)
      {
        if(!isset($attributes[$default]))
          $attributes[$default] = null;
      }

      if(isset($attributes['dateUploaded']) && !empty($attributes['dateUploaded']))
        $dateUploaded = $attributes['dateUploaded'];
      else
        $dateUploaded = time();

      if(isset($attributes['dateTaken']) && !empty($attributes['dateTaken']))
        $dateTaken = $attributes['dateTaken'];
      else
        $dateTaken = @$exif['dateTaken'];

      if(getConfig()->get('photos')->autoTagWithDate == 1)
      {
        $dateTags = sprintf('%s,%s', date('F', $dateTaken), date('Y', $dateTaken));
        if(!isset($attributes['tags']) || empty($attributes['tags']))
          $attributes['tags'] = $dateTags;
        else
          $attributes['tags'] .= ",{$dateTags}";
      }

      if(isset($exif['latitude']))
        $attributes['latitude'] = floatval($exif['latitude']);
      if(isset($exif['longitude']))
        $attributes['longitude'] = floatval($exif['longitude']);
      if(isset($attributes['tags']) && !empty($attributes['tags']))
      {
        $attributes['tags'] = Tag::removeDuplicatesFromString($attributes['tags']);
        $attributes['tags'] = Tag::sanitizeTagsAsString($attributes['tags']);
      }

      $attributes = array_merge(
        self::getDefaultAttributes(),
        array(
          'hash' => sha1_file($localFile),
          'size' => intval(filesize($localFile)/1024),
          'exifCameraMake' => @$exif['cameraMake'],
          'exifCameraModel' => @$exif['cameraModel'],
          'exifFNumber' => @$exif['FNumber'],
          'exifExposureTime' => @$exif['exposureTime'],
          'exifISOSpeed' => @$exif['ISO'],
          'exifFocalLength' => @$exif['focalLength'],
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
      if($stored)
      {
        if(isset($attributes['tags']) && !empty($attributes['tags']))
          Tag::updateTagCounts(array(), (array)explode(',', $attributes['tags']), $attributes['permission'], $attributes['permission']);

        getLogger()->info("Photo ({$id}) successfully stored to the database");
        return $id;
      }
      else
      {
        getLogger()->warn("Photo ({$id}) could NOT be stored to the database");
        return false;
      }
    }

    getLogger()->warn("Photo ({$id}) could NOT be stored to the file system");
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
      'license' => ''
    );
  }

  /**
    * Generate photo view url for the API
    *
    * @return array Default values for a new photo
    */
  private static function getPhotoViewUrl($photo)
  {
    return sprintf('%s://%s%s', Utility::getProtocol(false), $_SERVER['HTTP_HOST'], Url::photoView($photo['id'], null, false));
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

  private static function frac2Num($frac)
  {
    $parts = explode('/', $frac);

    if (count($parts) <= 0)
        return 0;

    if (count($parts) == 1)
        return $parts[0];
    // DIV/0
    if($parts[1] == 0)
        return 0;

    return floatval($parts[0]) / floatval($parts[1]);
  }

  /*** GPS Utils
   * from http://stackoverflow.com/questions/2526304/php-extract-gps-exif-data
   **/
  private static function getGps($exifCoord, $hemi)
  {
    $degrees = count($exifCoord) > 0 ? self::frac2Num($exifCoord[0]) : 0;
    $minutes = count($exifCoord) > 1 ? self::frac2Num($exifCoord[1]) : 0;
    $seconds = count($exifCoord) > 2 ? self::frac2Num($exifCoord[2]) : 0;

    $flip = ($hemi == 'W' or $hemi == 'S') ? -1 : 1;

    return $flip * ($degrees + $minutes / 60 + $seconds / 3600);
  }


  /**
    * Reads IPTC data from a photo.
    *
    * @param $photo Path to the photo.
    * @return array
    */
  private static function readIptc($photo)
  {
    $size = getimagesize($photo, $info);
    $iptc_array = array();
    if(isset($info['APP13']))
    {
      $iptc = iptcparse($info['APP13']);
      if(!empty($iptc))
      {
        // TODO deal with charset
        // TODO with alternates as both of these are arrays.
        // TODO eventually HTML-ify the description

        // get the title.
        // sometime (like from Adobe software) it is in 2#005 instead of 2#105
        // see https://github.com/openphoto/frontend/issues/260
        if(isset($iptc['2#105']))
          $iptc_array['title'] = $iptc['2#105'][0];
        else if(isset($iptc['2#005']))
          $iptc_array['title'] = $iptc['2#005'][0];

        if(isset($iptc['2#120']))
          $iptc_array['description'] = $iptc['2#120'][0];
        if(isset($iptc['2#025']))
          $iptc_array['tags'] = $iptc['2#025'];
      }
    }
    return $iptc_array;
  }

  private static function whitelistParams($attributes)
  {
    $returnAttrs = array();
    $matches = array('id' => 1,'host' => 1,'appId' => 1,'title' => 1,'description' => 1,'key' => 1,'hash' => 1,'tags' => 1,'size' => 1,'width' => 1,'photo'=>1,
      'height' => 1,'altitude' => 1, 'latitude' => 1,'longitude' => 1,'views' => 1,'status' => 1,'permission' => 1,'groups' => 1,'license' => 1,
      'dateTaken' => 1, 'dateUploaded' => 1);
    $patterns = array('exif.*' => 1,'date.*' => 1,'path.*' => 1);
    foreach($attributes as $key => $val)
    {
      if(isset($matches[$key]))
      {
        $returnAttrs[$key] = $val;
        continue;
      }

      foreach($patterns as $pattern)
      {
        if(preg_match("/^{$pattern}$/", $key))
        {
          $returnAttrs[$key] = $val;
          continue;
        }
      }
    }
    return $returnAttrs;
  }
}
