<?php
class ApiController extends BaseController
{
  public static function photos()
  {
    $db = getDb(getConfig()->get('systems')->database, getConfig()->get('credentials'));
    $photos = $db->getPhotos();
    return self::success('yay', $photos);
  }
}
