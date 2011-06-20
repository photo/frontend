<?php
class ApiController extends BaseController
{
  public static function photoDelete($id)
  {
    $status = Photo::delete($id);
    if($status)
      return self::success('Photo deleted successfully', $id);
    else
      return self::error('Photo deletion failure', false);
  }

  public static function photoDynamicUrl($id, $width, $height, $options = null)
  {
    return self::success('Url generated successfully', Photo::generateUrlInternal($id, $width, $height, $options));
  }

  /*public static function photoDynamic($id, $hash, $width, $height, $options = null)
  {
    $photo = Photo::generateImage($id, $hash, $width, $height, $options);
    return self::success('', $photo);
  }*/

  public static function photoUpload()
  {
    $attributes = $_POST;
    if(isset($attributes['returnOptions']))
    {
      $returnOptions = $attributes['returnOptions'];
      unset($attributes['returnOptions']);
    }

    $photoId = Photo::upload($_FILES['photo']['tmp_name'], $_FILES['photo']['name'], $attributes);

    if(isset($returnOptions))
    {
      $options = Photo::generateFragmentReverse($returnOptions);
      $hash = Photo::generateHash($photoId, $options['width'], $options['height'], $options['options']);
      $returnPhotoSuccess = Photo::generateImage($photoId, $hash, $options['width'], $options['height'], $options['options']);
    }

    if($photoId)
    {
      $photo = getDb()->getPhoto($photoId);
      if($returnPhotoSuccess)
        $photo['requestedUrl'] = $photo["path{$returnOptions}"];
      return self::success('yay', $photo);
    }

    return self::error('File upload failure', false);
  }

  public static function photos()
  {
    $db = getDb();
    $photos = $db->getPhotos();
    return self::success('yay', $photos);
  }
}
