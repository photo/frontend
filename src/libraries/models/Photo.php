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

  public static function generatePaths($photoName)
  {
    // TODO, normalize the name
    $photoName = time() . preg_replace('/[^a-zA-Z0-9.-_]/', '-', $photoName);
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
    if(self::generateHash($id, $width, $height, $options) != self::validateHash($hash, $id, $width, $height, $options))
      return false;

    if(!empty($options))
    {
      $options = explode('x', $options);
      foreach($options as $option)
      { }
    }
    
    $photo = getDb()->getPhoto($id);
    $filename = getFs()->getPhoto($photo['pathBase']);

    $image = getImage($filename);
    $image->scale($width, $height, true);
    $image->write($filename);
    $customPath = self::generateCustomUrl($photo['pathBase'], $width, $height, $options);
    $key = self::generateCustomKey($width, $height, $options);
    $resFs = getFs()->putPhoto($filename, $customPath);
    $resDb = getDb()->addAttribute($id, array($key => $customPath));
    if($resFs && $resDb)
      return $filename;

    return false;
  }

  public static function normalize($id, $data)
  {
    return array_merge($data, array('id' => $id));
  }

  public static function upload($localFile, $name)
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
      unlink($localFile);
      unlink($localFileCopy);
      if($uploaded)
      {
        $stored = $db->putPhoto($id, array('host' => getFs()->getHost(), 'pathOriginal' => $paths['pathOriginal'], 'pathBase' => $paths['pathBase']));
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

  private static function generateFragment($width, $height, $options)
  {
    $fragment = "{$width}x{$height}";
    if(!empty($options))
      $fragment += "x{$options}";
    return $fragment;
  }

  private static function generateHash(/*$args1, $args2, ...*/)
  {
    $args = func_get_args();
    $args[] = getConfig()->get('secrets')->secret;
    return substr(sha1(implode('.', $args)), 0, 5);
  }

  private static function validateHash(/*$hash, $args1, $args2, ...*/)
  {
    $args = func_get_args();
    $args[] = getConfig()->get('secrets')->secret;
    $hash = array_shift($args);
    return (substr(sha1(implode('.', $args)), 0, 5) == $hash);
  }
}
