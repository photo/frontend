<?php
class Photo
{
  public static function delete($id)
  {
    // TODO, validation
    $fs = getFs();
    $db = getDb();
    $fileStatus = $fs->deletePhoto($id);
    $dataStatus = $db->deletePhoto($id);
    return $fileStatus && $dataStatus;
  }

  public static function generateCustomKey($width, $height, $options = null)
  {
    return sprintf('path%s', self::generateFragment($width, $height, $options));
  }

  public static function generateFragment($width, $height, $options)
  {
    $fragment = "{$width}x{$height}";
    if(!empty($options))
      $fragment .= "x{$options}";
    return $fragment;
  }

  public static function generateFragmentReverse($options)
  {
    $options = explode('x', $options);
    $width = array_shift($options);
    $height = array_shift($options);
    $options = implode('x', $options);
    return array('width' => $width, 'height' => $height, 'options' => $options);
  }

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

  public static function generatePaths($photoName)
  {
    // TODO, normalize the name
    $photoName = time() . '-' . preg_replace('/[^a-zA-Z0-9.-_]/', '-', $photoName);
    return array(
      'pathOriginal' => sprintf('/original/%s/%s', date('Ym'), $photoName),
      'pathBase' => sprintf('/base/%s/%s', date('Ym'), $photoName)
    );
  }

  public static function generateUrlPublic($photo, $width, $height, $options = null)
  {
    $key = self::generateCustomKey($width, $height, $options);
    if(isset($photo[$key]))
      return sprintf('http://%s%s', getFs()->getHost(), $photo[$key]);
    else
      return self::generateUrlInternal($photo['id'], $width, $height, $options);
  }

  public static function generateUrlInternal($id, $width, $height, $options = null)
  {
    $fragment = self::generateFragment($width, $height, $options);
    $hash = self::generateHash($id, $width, $height, $options);
    return sprintf('/photo/%s/create/%s/%s.jpg', $id, $hash, $fragment);
  }

  public static function generateImage($id, $hash, $width, $height, $options = null)
  {
    if(!self::validateHash($hash, $id, $width, $height, $options))
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
    $resDb = getDb()->addAttribute($id, array($key => $customPath));
    if($resFs && $resDb)
      return $filename;

    return false;
  }

  public static function normalize($id, $appId, $data)
  {
    return array_merge($data, array('id' => $id, 'appId' => $appId));
  }

  public static function update($id, $attributes = array())
  {
    if(empty($attributes))
      return $id;

    $data = self::normalize($id, getConfig()->get('application')->appId, $attributes);
    $status = getDb()->postPhoto($id, $data);
    if(!$status)
      return false;

    return $id;
  }

  public static function upload($localFile, $name, $attributes = array())
  {
    $fs = getFs();
    $db = getDb();
    // TODO, needs to be a lookup
    $id = base_convert(rand(1,1000), 10, 35);
    if(is_uploaded_file($localFile))
    {
      $paths = Photo::generatePaths($name);
      // resize the base image before uploading
      $localFileCopy = "{$localFile}-copy}";
      copy($localFile, $localFileCopy);

      $exiftran = getConfig()->get('modules')->exiftran;
      if(is_executable($exiftran))
        exec($cmd = sprintf('%s -ai %s', getConfig()->get('modules')->exiftran, escapeshellarg($localFileCopy)));

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
        $exif = self::readExif($localFile);
        $attributes = array_merge(
          $attributes, 
          self::getDefaultAttributes(),
          array(
            'hash' => sha1_file($localFile),
            'size' => 0, // TODO
            'tags' => '', // TODO
            'exifCameraMake' => @$exif['cameraMake'],
            'exifCameraModel' => @$exif['cameraModel'],
            'width' => @$exif['width'],
            'height' => @$exif['height'],
            'dateTaken' => @$exif['dateTaken'],
            'dateTakenDay' => date('d', @$exif['dateTaken']),
            'dateTakenMonth' => date('m', @$exif['dateTaken']),
            'dateTakenYear' => date('Y', @$exif['dateTaken']),
            'dateUploaded' => time(),
            'dateUploadedDay' => date('d', time()),
            'dateUploadedMonth' => date('m', time()),
            'dateUploadedYear' => date('Y', time()),
            'pathOriginal' => $paths['pathOriginal'], 
            'pathBase' => $paths['pathBase']
          )
        );
        $stored = $db->putPhoto($id, $attributes);
        unlink($localFile);
        unlink($localFileCopy);
        if($stored)
          return $id;
      }
    }

    return false;
  }

  private static function generateCustomUrl($basePath, $width, $height, $options)
  {
    $fragment = self::generateFragment($width, $height, $options);
    $customPath = preg_replace('#^/base/#', '/custom/', $basePath);
    $customName = substr($customPath, 0, strrpos($customPath, '.'));
    return "{$customName}_{$fragment}.jpg";
  }

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

  private static function validateHash(/*$hash, $args1, $args2, ...*/)
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

  private static function readExif($image)
  {
    $exif = @exif_read_data($image);
    if(!$exif)
      return null;

    $size = getimagesize($image);
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
