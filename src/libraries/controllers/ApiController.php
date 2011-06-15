<?php
class ApiController extends BaseController
{
  public static function photoUpload()
  {
    $fs = getFs(getConfig()->get('systems')->fileSystem, getConfig()->get('credentials'));
    $localFile = $_FILES['photo']['tmp_name'];
    if(is_uploaded_file($localFile))
    {
      $uploaded = $fs->putFile($localFile, rand(0,10000).'.txt');
      if($uploaded)
        return self::success('yay', $uploaded);
    }

    return self::error('boo', $uploaded);
  }

  public static function photos()
  {
    $db = getDb(getConfig()->get('systems')->database, getConfig()->get('credentials'));
    $photos = $db->getPhotos();
    return self::success('yay', $photos);
  }
}
