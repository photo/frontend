<?php
/**
 * Photo model.
 *
 * Something related to photos, the guts are in this file.
 * Upload, update, delete and generate, oh my!
 * @author Jaisen Mathai <jaisen@jmathai.com>
 */
class Photo extends Media
{
  public function __construct($params = null)
  {
    parent::__construct();
    if(isset($params['utility']))
      $this->utility = $params['utility'];
    else
      $this->utility = new Utility;

    if(isset($params['url']))
      $this->url = $params['url'];
    else
      $this->url = new Url;

    if(isset($params['image']))
      $this->image = $params['image'];
    else
      $this->image = getImage();

    if(isset($params['user']))
      $this->user = $params['user'];
    else
      $this->user = new User;

    if(isset($params['config']))
      $this->config = $params['config'];
  }

  /**
    * Adds the urls for the photo.
    * pathWxH is a resource.
    * photoWxH is an enumerated array [path, width, height]
    *
    * @param array $photo the photo object
    * @param array $options Options for the photo such as crop (CR) and greyscale (BW)
    * @param string $protocol http or https
    * @return array
    */
  public function addApiUrls($photo, $sizes, $token=null, $filterOpts=null, $protocol=null)
  {
    if($protocol === null)
      $protocol = $this->utility->getProtocol(false);

    foreach($sizes as $size)
    {
      $options = $this->generateFragmentReverse($size);
      $fragment = $this->generateFragment($options['width'], $options['height'], $options['options']);
      $path = $this->generateUrlPublic($photo, $options['width'], $options['height'], $options['options'], $protocol);
      $photo["path{$size}"] = $path;
      if(strstr($fragment, 'xCR') === false)
      {
        $dimensions = $this->getRealDimensions($photo['width'], $photo['height'], $options['width'], $options['height']);
        $photo["photo{$fragment}"] = array($path, intval($dimensions['width']), intval($dimensions['height']));
      }
      else
      {
        $photo["photo{$fragment}"] = array($path, intval($options['width']), intval($options['height']));
      }
    }

    $photo['pathBase'] = $this->generateUrlBaseOrOriginal($photo, 'base');
    // the original needs to be conditionally included
    if($this->config->site->allowOriginalDownload == 1 || $this->user->isAdmin())
    {
      $photo['pathOriginal'] = $this->generateUrlBaseOrOriginal($photo, 'original');
      $photo['pathDownload'] = $this->generateUrlDownload($photo, $token);
    }
    elseif(isset($photo['pathOriginal']))
    {
      unset($photo['pathOriginal']);
    }

    $photo['url'] = $this->getPhotoViewUrl($photo, $filterOpts);
    return $photo;
  }

  /**
    * Delete a photo from the remote database and remote filesystem.
    * This deletes the original photo and all versions.
    *
    * @param string $id ID of the photo
    * @return boolean
    */
  public function delete($id)
  {
    // TODO, validation
    // TODO, do not delete record from db - mark as deleted
    $photo = $this->db->getPhoto($id);
    if(!$photo)
      return false;

    $fileStatus = $this->fs->deletePhoto($photo);
    $dataStatus = $this->db->deletePhoto($photo);
    return $fileStatus && $dataStatus;
  }

  /**
    * Delete the source files of a photo from the remote filesystem.
    * This deletes the original photo and all versions.
    * Database entries are left in tact.
    * Typically used for migration.
    *
    * @param string $id ID of the photo
    * @return boolean
    */
  public function deleteSourceFiles($id)
  {
    // TODO, validation
    $photo = $this->db->getPhoto($id);
    if(!$photo)
      return false;

    $fileStatus = $this->fs->deletePhoto($photo);
    if(!$fileStatus)
      $this->logger->warn(sprintf('Could not delete source photo from file system (%s)', $photo['id']));

    $dbStatus = $this->db->deletePhotoVersions($photo);
    if(!$dbStatus)
      $this->logger->warn(sprintf('Could not delete source photo from database (%s)', $photo['id']));

    return $fileStatus && $dbStatus;
  }

  /**
    * Output the contents of the original photo
    * Gets a file pointer from the adapter
    *   which can be a local or remote file
    *
    * @param array $photo photo object as returned from the API (not the DB)
    * @return 
    */
  public function download($photo, $isAttachment = true)
  {
    $fp = $this->fs->downloadPhoto($photo);
    if(!$fp)
      return false;

    header('Content-Type: image/jpeg');
    if($isAttachment)
    {
      header('Content-Description: File Transfer');
      header('Content-Disposition: attachment; filename="'.$photo['filenameOriginal'].'"');
    }
    while($buffer = fgets($fp, 4096))
      echo $buffer;

    fclose($fp);
    return true;
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
  public function generateCustomKey($width, $height, $options = null)
  {
    return sprintf('path%s', $this->generateFragment($width, $height, $options));
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
  public function generateFragment($width, $height, $options = null)
  {
    $fragment = "{$width}x{$height}";
    if(!empty($options))
      $fragment .= "x{$options}";
    return $fragment;
  }

  /**
    * Generate a version of the photo as specified by the width, height and options.
    * This method requires the $hash be validated to keep random versions of images to be created.
    * The photo is generated, uploaded to the remote file system and added to the database.
    * Operations are done in place on a downloaded version of the base photo and this file name is returned.
    *
    * @param string $id The id of the photo.
    * @param int $width The width of the requested photo.
    * @param int $height The height of the requested photo.
    * @param string $options Optional options to be applied on the photo
    * @return mixed string on success, false on failure
    */
  public function generate($id, $hash, $width, $height, $options = null)
  {
    if(!$this->isValidHash($hash, $id, $width, $height, $options))
      return false;

    $photo = $this->db->getPhoto($id);
    if(!$photo)
    {
      $this->logger->crit(sprintf('Could not get photo from db in generate method (%s)', $id));
      return false;
    }

    $filename = $this->fs->getPhoto($photo['pathBase']);
    if(!$filename)
    {
      $this->logger->crit(sprintf('Could not get photo from fs in generate method %s', $photo['pathBase']));
      return false;
    }

    try
    {
      $this->image->load($filename);
    }
    catch(OPInvalidImageException $e)
    {
      $this->logger->crit('Could not get image from image adapter in generate method', $e);
      return false;
    }

    $maintainAspectRatio = true;
    if(!empty($options))
    {
      $optionsArray = (array)explode('x', $options);
      foreach($optionsArray as $option)
      {
        switch($option)
        {
          case 'BW':
            $this->image->greyscale();
            break;
          case 'CR':
            $maintainAspectRatio = false;
            break;
        }
      }
    }

    $this->image->scale($width, $height, $maintainAspectRatio);
    $this->image->write($filename, 'jpg');
    $customPath = $this->generateCustomUrl($photo['pathBase'], $width, $height, $options);
    $key = $this->generateCustomKey($width, $height, $options);
    $resFs = $this->fs->putPhoto($filename, $customPath, $photo['dateTaken']);
    $resDb = $this->db->postPhoto($id, array($key => $customPath));
    // TODO unlink $filename
    if($resFs && $resDb)
      return $filename;

    return false;
  }

  /**
    * Does the opposite of $this->generateFragment.
    * Given a string fragment this will return it's parts as an array.
    * The $options must start with a width and height (i.e. 800x600)
    *
    * @param string $options Options for the photo such as crop (CR) and greyscale (BW)
    * @return array
    */
  public function generateFragmentReverse($options)
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
    * @return mixed string on success, FALSE on error
    */
  public function generateHash(/*$args1, $args2, ...*/)
  {
    $args = func_get_args();
    if(count($args) === 0)
      return false;

    foreach($args as $k => $v)
    {
      if(strlen($v) == 0)
        unset($args[$k]);
    }
    $args[] = $this->config->secrets->secret;
    return substr(sha1(implode('.', $args)), 0, 5);
  }

  /**
    * Generates the default paths given a photo name.
    * These paths will also be the initial versions of the photo that are stored in the file system and database.
    * We need the prefix for the original to be different from the base
    * A random number between 1,000,000 and 9,999,999 is sufficient when paired with the filename
    *
    * @param string $photoName File name of the photo
    * @return array
    */
  public function generatePaths($photoName, $dateTaken)
  {
    $ext = substr($photoName, (strrpos($photoName, '.')+1));
    $rootName = preg_replace('/[^a-zA-Z0-9.-_]/', '-', substr($photoName, 0, (strrpos($photoName, '.'))));
    $baseName = sprintf('%s-%s.%s', $rootName, dechex(rand(1000000,9999999)), 'jpg');
    $originalName = sprintf('%s-%s.%s', $rootName, uniqid(), $ext);
    return array(
      'pathOriginal' => sprintf('/original/%s/%s', date('Ym', $dateTaken), $originalName),
      'pathBase' => sprintf('/base/%s/%s', date('Ym', $dateTaken), $baseName)
    );
  }

  /**
    * Obtain a public URL for the base photo.
    *
    * @param array $photo The photo object as returned from the database.
    * @param string $protocol Protocol for the URL
    * @return mixed string URL on success, FALSE on failure
    */
  public function generateUrlBaseOrOriginal($photo, $type = 'base', $protocol = null)
  {
    if(!$protocol)
      $protocol = $this->utility->getProtocol(false);

    // force a protocol if specified in the configs for assets
    //  we only do this for static assets
    //  assets which need to run through the API to be generated inherit the current protocol
    //  See #1236
    $assetProtocol = $protocol;
    if(!empty($this->config->site->assetProtocol))
      $assetProtocol = $this->config->site->assetProtocol;

    if($type === 'base')
      return "{$assetProtocol}://{$photo['host']}{$photo['pathBase']}";
    elseif($type === 'original')
      return "{$assetProtocol}://{$photo['host']}{$photo['pathOriginal']}";

  }

  /**
    * Obtain a public URL for the original photo.
    * If the user is the owner we return the URL to the static asset.
    * If the user is logged in but not the owner we route through the API host.
    *
    * @param array $photo The photo object as returned from the database.
    * @param string $protocol Protocol for the URL
    * @return mixed string URL on success, FALSE on failure
    */
  public function generateUrlDownload($photo, $token = null, $protocol = null)
  {
    if(!$protocol)
      $protocol = $this->utility->getProtocol(false);

    if($token)
      return sprintf('%s://%s/photo/%s/token-%s/download', $protocol, $this->utility->getHost(), $photo['id'], $token);
    else
      return sprintf('%s://%s/photo/%s/download', $protocol, $this->utility->getHost(), $photo['id']);
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
    * @param string $protocol Protocol for the URL
    * @return mixed string URL on success, FALSE on failure
    */
  public function generateUrlPublic($photo, $width, $height, $options = null, $protocol = null)
  {
    if(!$protocol)
      $protocol = $this->utility->getProtocol(false);

    // force a protocol if specified in the configs for assets
    //  we only do this for static assets
    //  assets which need to run through the API to be generated inherit the current protocol
    //  See #1236
    $assetProtocol = $protocol;
    if(!empty($this->config->site->assetProtocol))
      $assetProtocol = $this->config->site->assetProtocol;

    $key = $this->generateCustomKey($width, $height, $options);

    if(isset($photo[$key]))
      return "{$assetProtocol}://{$photo['host']}{$photo[$key]}";
    elseif(isset($photo['id']))
      return "{$protocol}://{$_SERVER['HTTP_HOST']}".$this->generateUrlInternal($photo['id'], $width, $height, $options); // TODO remove reference to HTTP_HOST
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
  public function generateUrlInternal($id, $width, $height, $options = null)
  {
    $fragment = $this->generateFragment($width, $height, $options);
    $hash = $this->generateHash($id, $width, $height, $options);
    return sprintf('/photo/%s/create/%s/%s.jpg', $id, $hash, $fragment);
  }

  /**
    * Returns all albums a photo belongs to
    *
    * @param string Photo id
    * @return array
    */
  public function getAlbumsForPhoto($id)
  {
    return $this->db->getPhotoAlbums($id);
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
  public function getRealDimensions($originalWidth, $originalHeight, $newWidth, $newHeight)
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
  protected function parseExifDate($exif, $key)
  {
    // gh-1335
    // rely on strtotime which handles the following formats which have been seen
    // 2013/01/01 00:00:00
    // 2013:01:01 00:00:00
    if(array_key_exists($key, $exif))
      return strtotime($exif[$key]);
    return false;
  }

  /**
    * Reads exif data from a photo.
    *
    * @param $photo Path to the photo.
    * @return array
    */
  protected function readExif($photo, $allowAutoRotate)
  {
    $exif = @exif_read_data($photo);
    if(!$exif)
      $exif = array();

    $size = getimagesize($photo);
    // DateTimeOriginal is the right thing. If it is not there
    // use DateTime which might be the date the photo was modified
    $parsedDate = $this->parseExifDate($exif, 'DateTimeOriginal');
    if($parsedDate === false) 
    {
      $parsedDate = $this->parseExifDate($exif, 'DateTime');    
      if($parsedDate === false)
      {
        if(array_key_exists('FileDateTime', $exif))
          $parsedDate = $exif['FileDateTime'];
        else
          $parsedDate = time();
      }
    }
    $dateTaken = $parsedDate;    

    $width = $size[0];
    $height = $size[1];

    // Since we stopped auto rotating originals we have to use the Orientation from
    //  exif and if this photo was autorotated
    // Gh-1031 Gh-1149
    if($this->autoRotateEnabled($allowAutoRotate) && isset($exif['Orientation']))
    {
      // http://recursive-design.com/blog/2012/07/28/exif-orientation-handling-is-a-ghetto/
      switch($exif['Orientation'])
      {
        case '6':
        case '8':
        case '5':
        case '7':
          $width = $size[1];
          $height = $size[0];
          break;
      }
    }

    $exif_array = array('dateTaken' => $dateTaken, 'width' => $width,
      'height' => $height, 'cameraModel' => @$exif['Model'],
      'cameraMake' => @$exif['Make'],
      'ISO' => @$exif['ISOSpeedRatings'],
      'Orientation' => @$exif['Orientation'],
      'exposureTime' => @$exif['ExposureTime']);

    if(isset($exif['GPSLongitude'])) {
      $exif_array['longitude'] = $this->getGps($exif['GPSLongitude'], $exif['GPSLongitudeRef']);
    }

    if(isset($exif['GPSLatitude'])) {
      $exif_array['latitude'] = $this->getGps($exif['GPSLatitude'], $exif['GPSLatitudeRef']);
    }

    $exif_array['FNumber'] = $this->frac2Num(@$exif['FNumber']);
    $exif_array['focalLength'] = $this->frac2Num(@$exif['FocalLength']);

    return $exif_array;
  }

  public function transform($id, $transformations)
  {
    $photo = $this->db->getPhoto($id);
    if(!$photo)
    {
      $this->logger->crit('Could not get photo from db in transform method');
      return false;
    }

    $filename = $this->fs->getPhoto($photo['pathBase']);
    if(!$filename)
    {
      $this->logger->crit('Could not get photo from fs in transform method');
      return false;
    }

    try
    {
      $this->image->load($filename);
    }
    catch(OPInvalidImageException $e)
    {
      $this->logger->crit('Could not get image from image adapter in transform method', $e);
      return false;
    }

    // update the file on the file system and update the db with the path
    $paths = $this->generatePaths($photo['filenameOriginal']);
    $updateFields = array('pathBase' => $paths['pathBase']);
    foreach($transformations as $trans => $value)
    {
      switch($trans)
      {
        case 'rotate':
          $this->image->rotate($value);
          $updateFields['rotation'] = intval(($photo['rotation'] + $value) % 360);
          break;
      }
    }

    $updateFs = $this->fs->putPhoto($filename, $paths['pathBase'], $photo['dateTaken']);
    $updateDb = $this->db->postPhoto($id, $updateFields);

    unlink($filename);

    // purge photoVersions
    $delVersionsResp = $this->db->deletePhotoVersions($photo);
    if(!$delVersionsResp)
      return false;
    
    return true;
  }

  /**
    * Update the attributes of a photo in the database.
    *
    * @param string $id The id of the photo.
    * @param array $attributes The attributes to save
    * @return mixed string on success, false on failure
    */
  public function update($id, $attributes = array())
  {
    if(empty($attributes))
      return $id;

    $tagObj = new Tag;

    // to preserve json columns we have to do complete writes
    $currentPhoto = $this->db->getPhoto($id);
    unset($currentPhoto['id'], $currentPhoto['albums']);
    $currentPhoto['tags'] = implode(',', $currentPhoto['tags']);
    $attributes = array_merge($currentPhoto, $attributes);

    $attributes = $this->prepareAttributes($attributes, null, null);

    if(isset($attributes['tags']) && !empty($attributes['tags']))
      $attributes['tags'] = $tagObj->sanitizeTagsAsString($attributes['tags']);

    // trim all the attributes
    foreach($attributes as $key => $val)
      $attributes[$key] = $this->trim($val);

    // since tags can be created adhoc we need to ensure they're here
    if(isset($attributes['tags']) && !empty($attributes['tags']))
      $tagObj->createBatch($attributes['tags']);

    $status = $this->db->postPhoto($id, $attributes);
    if(!$status)
      return false;

    return $id;
  }

  public function replace($id, $localFile, $name, $attributes = array())
  {
    // check if file type is valid
    if(!$this->utility->isValidMimeType($localFile))
    {
      $this->logger->warn(sprintf('Invalid mime type for %s', $localFile));
      return false;
    }

    $allowAutoRotate = isset($attributes['allowAutoRotate']) ? $attributes['allowAutoRotate'] : '1';
    $exif = $this->readExif($localFile, $allowAutoRotate);
    $iptc = $this->readIptc($localFile);

    if(isset($attributes['dateTaken']) && !empty($attributes['dateTaken']))
      $dateTaken = $attributes['dateTaken'];
    elseif(isset($exif['dateTaken']))
      $dateTaken = $exif['dateTaken'];
    else
      $dateTaken = time();

    $resp = $this->createAndStoreBaseAndOriginal($name, $localFile, $dateTaken, $allowAutoRotate);
    $paths = $resp['paths'];

    $attributes = array_merge($this->whitelistParams($attributes), $resp['paths']);

    if($resp['status'])
    {
      $this->logger->info("Photo ({$id}) successfully stored on the file system (replacement)");
      $fsExtras = $this->fs->getMetaData($localFile);

      if(!empty($fsExtras))
        $attributes['extraFileSystem'] = $fsExtras;
      $defaults = array('title', 'description', 'tags', 'latitude', 'longitude');
      foreach($iptc as $iptckey => $iptcval)
      {
        if(empty($iptcval))
          continue;

        if($iptckey == 'tags')
          $attributes['tags'] = implode(',', array_unique(array_merge((array)explode(',', $attributes['tags']), $iptcval)));
        else if(!isset($attributes[$iptckey])) // do not clobber if already in $attributes #1011
          $attributes[$iptckey] = $iptcval;
      }

      foreach($defaults as $default)
      {
        if(!isset($attributes[$default]))
          $attributes[$default] = null;
      }

      $exifParams = array('width' => 'width', 'height' => 'height', 'cameraMake' => 'exifCameraMake', 'cameraModel' => 'exifCameraModel', 
        'FNumber' => 'exifFNumber', 'exposureTime' => 'exifExposureTime', 'ISO' => 'exifISOSpeed', 'focalLength' => 'exifFocalLength', 'latitude' => 'latitude', 'longitude' => 'longitude');
      foreach($exifParams as $paramName => $mapName)
      {
        // do not clobber if already in $attributes #1011
        if(isset($exif[$paramName]) && !isset($attributes[$mapName]))
          $attributes[$mapName] = $exif[$paramName];
      }

      $attributes['dateTakenDay'] = date('d', $dateTaken);
      $attributes['dateTakenMonth'] = date('m', $dateTaken);
      $attributes['dateTakenYear'] = date('Y', $dateTaken);
      $attributes['hash'] = sha1_file($localFile);
      $attributes['size'] = intval(filesize($localFile)/1024);
      $attributes['host'] = $this->fs->getHost();
      $attributes['filenameOriginal'] = $name;

      $tagObj = new Tag;
      if(isset($attributes['tags']) && !empty($attributes['tags']))
        $tagObj->createBatch($attributes['tags']);

      $photo = $this->db->getPhoto($id);

      // normally we delete the existing photos
      // in some cases we may have already done this (migration)
      if(!isset($_POST['skipDeletes']) || empty($_POST['skipDeletes']))
      {
        $this->logger->info(sprintf('Purging photos in replace API for photo %s', $id));
        // purge photoVersions
        $delVersionsResp = $this->db->deletePhotoVersions($photo);
        if(!$delVersionsResp)
        {
          $this->logger->info('Could not purge photo versions from the database');
          return false;
        }

        // delete all photos from the original photo object (includes paths to existing photos)
        // check if skipDeleteOriginal is passed in and remove from $photo and $attributes to preserve
        if($skipOriginal === '1')
          unset($photo['pathOriginal']);

        $delFilesResp = $this->fs->deletePhoto($photo);
        if(!$delFilesResp)
        {
          $this->logger->info('Could not purge photo versions from the file system');
          return false;
        }
      }

      // trim all the attributes
      foreach($attributes as $key => $val)
        $attributes[$key] = $this->trim($val);

      // update photo paths / hash
      $updPathsResp = $this->db->postPhoto($id, $attributes);

      unlink($localFile);
      unlink($resp['localFileCopy']);

      if($updPathsResp)
        return true;
    }

    $this->logger->warn('Could not upload files for replacement');
    return false;
  }

  /**
    * Downloads a photo from a URL and stores it locally.
    *  Used in ApiShareController to attach a photo in an email.
    *
    * @param string $url URL of the photo to store locally
    * @return mixed file pointer on success, FALSE on failure
    */
  public function storeLocally($url)
  {
    $file = tempnam(sys_get_temp_dir(), 'opme-locally-');
    $fp = fopen($file, 'w');
    if(!$fp)
    {
      $this->logger->warn('Could not create file pointer to store photo locally');
      return false;
    }

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_FILE, $fp);
    $data = curl_exec($ch);
    $curl_errno = curl_errno($ch);
    $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    curl_close($ch);
    fclose($fp);

    if($curl_errno !== 0)
    {
      $this->logger->warn('Storing photo locally failed due to curl error');
      return false;
    }

    if($code != '200')
    {
      $this->logger->warn('Fetching of photo to store locally did not return a 200 HTTP response');
      return false;
    }

    return $file;
  }

  /**
    * Uploads a new photo to the remote file system and database.
    *
    * @param string $localFile The local file system path to the photo.
    * @param string $name The file name of the photo.
    * @param array $attributes The attributes to save
    * @return mixed string on success, false on failure
    */
  public function upload($localFile, $name, $attributes = array())
  {
    // check if file type is valid
    if(!$this->utility->isValidMimeType($localFile))
    {
      $this->logger->warn(sprintf('Invalid mime type for %s', $localFile));
      return false;
    }

    $id = $this->user->getNextId('photo');
    if($id === false)
    {
      $this->logger->crit('Could not fetch next photo ID');
      return false;
    }
    $tagObj = new Tag;
    $filenameOriginal = $name;

    $allowAutoRotate = isset($attributes['allowAutoRotate']) ? $attributes['allowAutoRotate'] : '1';
    $exif = $this->readExif($localFile, $allowAutoRotate);
    $iptc = $this->readIptc($localFile);

    if(isset($attributes['dateTaken']) && !empty($attributes['dateTaken']))
      $dateTaken = $attributes['dateTaken'];
    elseif(isset($exif['dateTaken']))
      $dateTaken = $exif['dateTaken'];
    else
      $dateTaken = time();

    $resp = $this->createAndStoreBaseAndOriginal($name, $localFile, $dateTaken, $allowAutoRotate);
    $paths = $resp['paths'];

    $attributes = $this->whitelistParams($attributes);

    if($resp['status'])
    {
      $this->logger->info("Photo ({$id}) successfully stored on the file system");
      $fsExtras = $this->fs->getMetaData($localFile);
      if(!empty($fsExtras))
        $attributes['extraFileSystem'] = $fsExtras;
      $defaults = array('title', 'description', 'tags', 'latitude', 'longitude');
      foreach($iptc as $iptckey => $iptcval)
      {
        if(empty($iptcval))
          continue;

        if($iptckey == 'tags')
          $attributes['tags'] = implode(',', array_unique(array_merge((array)explode(',', $attributes['tags']), $iptcval)));
        else if(!isset($attributes[$iptckey])) // do not clobber if already in $attributes #1011
          $attributes[$iptckey] = $iptcval;
      }

      foreach($defaults as $default)
      {
        if(!isset($attributes[$default]))
          $attributes[$default] = null;
      }

      $exifParams = array('width' => 'width', 'height' => 'height', 'cameraMake' => 'exifCameraMake', 'cameraModel' => 'exifCameraModel', 
        'FNumber' => 'exifFNumber', 'exposureTime' => 'exifExposureTime', 'ISO' => 'exifISOSpeed', 'focalLength' => 'exifFocalLength', 'latitude' => 'latitude', 'longitude' => 'longitude');
      foreach($exifParams as $paramName => $mapName)
      {
        // do not clobber if already in $attributes #1011
        if(isset($exif[$paramName]) && !isset($attributes[$mapName]))
          $attributes[$mapName] = $exif[$paramName];
      }

      if(isset($attributes['dateUploaded']) && !empty($attributes['dateUploaded']))
        $dateUploaded = $attributes['dateUploaded'];
      else
        $dateUploaded = time();

      if($this->config->photos->autoTagWithDate == 1)
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
        $attributes['tags'] = $tagObj->sanitizeTagsAsString($attributes['tags']);

    // since tags can be created adhoc we need to ensure they're here
      if(isset($attributes['tags']) && !empty($attributes['tags']))
        $tagObj->createBatch($attributes['tags']);

      $attributes['owner'] = $this->owner;
      $attributes['actor'] = $this->getActor();

      $attributes = array_merge(
        $this->getDefaultAttributes(),
        array(
          'hash' => sha1_file($localFile), // fallback if not in $attributes
          'size' => intval(filesize($localFile)/1024),
          'filenameOriginal' => $filenameOriginal,
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

      // trim all the attributes
      foreach($attributes as $key => $val)
        $attributes[$key] = $this->trim($val);

      $stored = $this->db->putPhoto($id, $attributes, $dateTaken);
      unlink($localFile);
      unlink($resp['localFileCopy']);
      if($stored)
      {
        $this->logger->info("Photo ({$id}) successfully stored to the database");
        return $id;
      }
      else
      {
        $this->logger->warn("Photo ({$id}) could NOT be stored to the database");
        return false;
      }
    }

    $this->logger->warn("Photo ({$id}) could NOT be stored to the file system");
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
  private function generateCustomUrl($basePath, $width, $height, $options)
  {
    $fragment = $this->generateFragment($width, $height, $options);
    $customPath = preg_replace('#^/base/#', '/custom/', $basePath);
    if(stristr($customPath, '.') === false)
      return "{$customPath}_{$fragment}.jpg";

    $customName = substr($customPath, 0, strrpos($customPath, '.'));
    return "{$customName}_{$fragment}.jpg";
  }

  /**
    * The default attributes for a new photo.
    *
    * @return array Default values for a new photo
    */
  private function getDefaultAttributes()
  {
    return array(
      'appId' => $this->config->application->appId,
      'host' => $this->fs->getHost(),
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
  private function getPhotoViewUrl($photo, $filterOpts=null)
  {
    return sprintf('%s://%s%s', $this->utility->getProtocol(false), $this->utility->getHost(false), $this->url->photoView($photo['id'], $filterOpts, false));
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
  private function isValidHash(/*$hash, $args1, $args2, ...*/)
  {
    $args = func_get_args();
    foreach($args as $k => $v)
    {
      if(strlen($v) == 0)
        unset($args[$k]);
    }
    $args[] = $this->config->secrets->secret;
    $hash = array_shift($args);
    return (substr(sha1(implode('.', $args)), 0, 5) == $hash);
  }

  private function frac2Num($frac)
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
  private function getGps($exifCoord, $hemi)
  {
    $degrees = count($exifCoord) > 0 ? $this->frac2Num($exifCoord[0]) : 0;
    $minutes = count($exifCoord) > 1 ? $this->frac2Num($exifCoord[1]) : 0;
    $seconds = count($exifCoord) > 2 ? $this->frac2Num($exifCoord[2]) : 0;

    $flip = ($hemi == 'W' or $hemi == 'S') ? -1 : 1;

    return $flip * ($degrees + $minutes / 60 + $seconds / 3600);
  }

  private function autoRotate($file, $allowAutoRotate = false)
  {
    if($this->autoRotateEnabled($allowAutoRotate))
    {
      exec(sprintf('%s -ai %s', $this->config->modules->exiftran, escapeshellarg($file)));
    }
  }

  protected function autoRotateEnabled($allowAutoRotate)
  {
    if($allowAutoRotate != '0' && is_executable($this->config->modules->exiftran))
      return true;
    return false;
  }

  protected function trim($string)
  {
    return preg_replace('/^([ \r\n]+)|(\s+)$/', '', $string);
  }

  private function createAndStoreBaseAndOriginal($name, $localFile, $dateTaken, $allowAutoRotate)
  {
    $paths = $this->generatePaths($name, $dateTaken);

    // resize the base image before uploading
    $localFileCopy = "{$localFile}-copy";
    $this->logger->info("Making a local copy of the uploaded image. {$localFile} to {$localFileCopy}");
    copy($localFile, $localFileCopy);

    $this->autoRotate($localFileCopy, $allowAutoRotate);
    
    $baseImage = $this->image->load($localFileCopy);
    if(!$baseImage)
    {
      $this->logger->warn('Could not load image, possibly an invalid image file.');
      return false;
    }
    $baseImage->scale($this->config->photos->baseSize, $this->config->photos->baseSize);
    $baseImage->write($localFileCopy, 'jpg');
    $uploaded = $this->fs->putPhotos(
      array(
        array($localFile => array($paths['pathOriginal'], $dateTaken)),
        array($localFileCopy => array($paths['pathBase'], $dateTaken))
      )
    );

    return array('status' => $uploaded, 'paths' => $paths, 'localFileCopy' => $localFileCopy);;
  }

  /**
    * Reads IPTC data from a photo.
    *
    * @param $photo Path to the photo.
    * @return array
    */
  private function readIptc($photo)
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

  private function whitelistParams($attributes)
  {
    $returnAttrs = array();
    $matches = array('id' => 1,'host' => 1,'appId' => 1,'title' => 1,'description' => 1,'key' => 1,'hash' => 1,'tags' => 1,'size' => 1,'photo'=>1,'height' => 1,
      'rotation'=>1,'altitude' => 1, 'latitude' => 1,'longitude' => 1,'views' => 1,'status' => 1,'permission' => 1,'albums'=>1,'groups' => 1,'license' => 1,
      'pathBase' => 1, 'pathOriginal' => 1, 'dateTaken' => 1, 'dateUploaded' => 1, 'filenameOriginal' => 1 /* TODO remove in 1.5.0, only used for upgrade */);
    $patterns = array('exif.*','date.*','extra.*');
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
