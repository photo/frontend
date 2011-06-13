<?php
class ApiController extends BaseController
{
  public static function photoUpload()
  {
    $fs = getFs(getConfig()->get('systems')->fileSystem, getConfig()->get('credentials'));
    $uploaded = $fs->putFile('../../.git/config', rand(0,10000).'.txt');
    if($uploaded)
      return self::success('yay', $uploaded);
    else
      return self::error('boo', $uploaded);
  }

  public static function photos()
  {
    $db = getDb(getConfig()->get('systems')->database, getConfig()->get('credentials'));
    $photos = $db->getPhotos();
    return self::success('yay', $photos);
  }
}
