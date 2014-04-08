<?php
/**
 * Media model.
 * Parent class for media types, i.e. Photo & Video
 *
 * @author Jaisen Mathai <jaisen@jmathai.com>
 */
abstract class Media extends BaseModel
{
  const typePhoto = 'photo';
  const typeVideo = 'video';

  public function __construct()
  {
    parent::__construct();
  }

  public function getMediaType($filename)
  {
    $type = get_mime_type($filename);
    switch ($type)
    {
      case 'image/gif':
      case 'image/jpeg':
      case 'image/pjpeg':
      case 'image/png':
        return self::typePhoto;
      case 'video/mpeg':
      case 'video/mp4':
      case 'video/ogg':
      case 'video/quicktime':
      case 'video/webm':
        return self::typeVideo;
    }
    return false;
  }

  public function isValidMimeType($filename)
  {
    $media_type = $this->getMediaType($filename);
    return in_array($media_type, array(self::typePhoto, self::typeVideo));
  }

  public function prepareAttributes($attributes, $localFile, $name)
  {
    // make sure the defaults are set (this method sets to null if not already set)
    $attributes = $this->requireDefaults($attributes);
    // we remove attributes which aren't valid
    //  gh-1428 this needs to be done before the later methods that modify $attributes 
    $attributes = $this->whitelistAttributes($attributes);

    // set all of the date and tag parameters
    $attributes = $this->setMediaSpecificAttributes($attributes, $localFile);
    $attributes = $this->setDateAttributes($attributes);
    $attributes = $this->setTagAttributes($attributes);

    if(!empty($name) && (!isset($attributes['filenameOriginal']) || empty($attributes['filenameOriginal'])))
      $attributes['filenameOriginal'] = $name;

    $attributes['owner'] = $this->owner;
    $attributes['actor'] = $this->getActor();

    if($this->isUploadedFile($localFile));
      $attributes['size'] = intval(filesize($localFile)/1024);

    foreach($attributes as $key => $val)
      $attributes[$key] = $this->trim($val);
    return $attributes;
  }

  public function requireDefaults($attributes)
  {
    $defaults = array(
      'appId' => $this->config->application->appId, 
      'host' => $this->fs->getHost(), 
      'title'=>null, 
      'description'=>null, 
      'tags'=>null, 
      'latitude'=>null, 
      'longitude'=>null,
      'views' => 0,
      'status' => 1,
      'permission' => 0, // TODO
      'license' => ''
    );
    return array_merge($defaults, $attributes);
  }

  protected function setDateAttributes($attributes)
  {
    if(!isset($attributes['dateTaken']) || empty($attributes['dateTaken']))
      $attributes['dateTaken'] = time();

    if(!isset($attributes['dateUploaded']) || empty($attributes['dateUploaded']))
      $attributes['dateUploaded'] = time();

    $attributes['dateTakenDay'] = date('d', $attributes['dateTaken']);
    $attributes['dateTakenMonth'] = date('m', $attributes['dateTaken']);
    $attributes['dateTakenYear'] = date('Y', $attributes['dateTaken']);
    $attributes['dateUploadedDay'] = date('d', $attributes['dateUploaded']);
    $attributes['dateUploadedMonth'] = date('m', $attributes['dateUploaded']);
    $attributes['dateUploadedYear'] = date('Y', $attributes['dateUploaded']);

    return $attributes;
  }

  protected function setExifAttributes($attributes, $localFile, $mediaType)
  {
    if($mediaType !== self::typePhoto)
      return $attributes;

    $allowAutoRotate = isset($attributes['allowAutoRotate']) ? $attributes['allowAutoRotate'] : '1';
    $exif = $this->readExif($localFile, $allowAutoRotate);
    
    // gh-1428 map exif to whitelisted attributes
    $exifMap = array('width' => 'width', 'height' => 'height', 'cameraMake' => 'exifCameraMake', 'cameraModel' => 'exifCameraModel',
      'FNumber' => 'exifFNumber', 'exposureTime' => 'exifExposureTime', 'ISO' => 'exifISOSpeed', 'focalLength' => 'exifFocalLength', 'latitude' => 'latitude', 'longitude' => 'longitude');
    foreach($exifMap as $paramName => $mapName)
    {
      // do not clobber if already in $attributes #1011
      if(isset($exif[$paramName]) && !isset($attributes[$mapName]))
        $attributes[$mapName] = $exif[$paramName];
    }

    return $attributes;
  }

  protected function setIptcAttributes($attributes, $localFile, $mediaType)
  {
    if($mediaType !== self::typePhoto)
      return $attributes;

    $tags = '';
    if(isset($attributes['tags']))
      $tags = $attributes['tags'];

    $iptc = $this->readIptc($localFile);
    foreach($iptc as $iptckey => $iptcval)
    {
      if(empty($iptcval))
        continue;

      if($iptckey == 'tags')
        $attributes['tags'] = implode(',', array_unique(array_merge((array)explode(',', $tags), $iptcval)));
      else if(!isset($attributes[$iptckey])) // do not clobber if already in $attributes #1011
        $attributes[$iptckey] = $iptcval;
    }

    return $attributes;
  }

  protected function setMediaSpecificAttributes($attributes, $localFile)
  {
    if(!$this->isUploadedFile($localFile))
      return $attributes;

    $mediaType = $this->getMediaType($localFile);
    switch($mediaType)
    {
      case self::typePhoto:
        $attributes = $this->setExifAttributes($attributes, $localFile, $mediaType);
        $attributes = $this->setIptcAttributes($attributes, $localFile, $mediaType);
        break;
      case self::typeVideo:
        $attributes['video'] = true;
        break;
    }

    return $attributes;
  }

  protected function setPathAttributes($attributes, $paths)
  {
    return array_merge(
      $attributes,
      array(
        'pathOriginal' => $paths['pathOriginal'],
        'pathBase' => isset($paths['pathBase']) ? $paths['pathBase'] : ''
      )
    );

  }

  protected function setTagAttributes($attributes)
  {
    $tagObj = new Tag;

    $attributes = $this->setDateTagsByAttributes($attributes);
    if(isset($attributes['tags']) && !empty($attributes['tags']))
    {
      $attributes['tags'] = $tagObj->sanitizeTagsAsString($attributes['tags']);
      $tagsArray = (array)explode(',', $attributes['tags']);
      sort($tagsArray);
      $attributes['tags'] = implode(',', $tagsArray);
    }

    return $attributes;
  }

  protected function setDateTagsByAttributes($attributes)
  {
    if($this->config->photos->autoTagWithDate != 1)
      return $attributes;

    // in (Photo|Video)::update we might change dateTaken which means we have to remove old year/month tags
    if(isset($attributes['tags']) && !empty($attributes['tags']))
    {
      $dateNames = array('January','February','March','April','May','June','July','August','September','October','November','December');

      $tagsArray = (array)explode(',', $attributes['tags']);
      $tagsArray = array_diff($tagsArray, $dateNames);
      foreach($tagsArray as $k => $t)
      {
        if(preg_match('/^(19|20|21)[0-9]{2}$/', $t))
          unset($tagsArray[$k]);
      }
      $attributes['tags'] = implode(',', $tagsArray);
    }

    $dateTags = sprintf('%s,%s', date('F', $attributes['dateTaken']), date('Y', $attributes['dateTaken']));
    // TODO see if there's a shortcut for this
    if(!isset($attributes['tags']) || empty($attributes['tags']))
      $attributes['tags'] = $dateTags;
    else
      $attributes['tags'] .= ",{$dateTags}";

    return $attributes;
  }

  protected function trim($value)
  {
    if(gettype($value) !== 'string')
      return $value;

    return preg_replace('/^([ \r\n]+)|(\s+)$/', '', $value);
  }

  protected function whitelistAttributes($attributes)
  {
    $returnAttrs = array();
    $matches = array(
      'actor' => 1,
      'albums'=>1,
      'altitude' => 1,
      'appId' => 1,
      'dateTaken' => 1,
      'dateUploaded' => 1,
      'description' => 1,
      'filenameOriginal' => 1, /* TODO remove in 1.5.0, only used for upgrade */
      'host' => 1,
      'hash' => 1,
      'height' => 1,
      'groups' => 1,
      'id' => 1,
      'key' => 1,
      'latitude' => 1,
      'license' => 1,
      'longitude' => 1,
      'owner' => 1,
      'pathBase' => 1,
      'pathOriginal' => 1,
      'permission' => 1,
      'rotation'=>1,
      'size' => 1,
      'status' => 1,
      'tags' => 1,
      'title' => 1,
      'views' => 1,
      'width' => 1,
    );
    $patterns = array('exif.*','date.*','extra.*','video.*');
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

  protected function isUploadedFile($localFile)
  {
    // add a bypass for when the `photo` parameter is passed in as a URL
    //  in this case `is_uploaded_file` will return true since we fetch and store
    //  that file using curl
    // we're using strstr since the file is prefixed with the temp directory
    // we are prefixing the local file with this constant to handle this
    // see gh-1465 for details
    if(strstr($localFile, 'opme-via-url'))
      return true;
    return is_uploaded_file($localFile);
  }
}
