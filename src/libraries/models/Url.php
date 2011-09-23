<?php
class Url
{
  public static function actionCreate($id, $type, $write = true)
  {
    return Utility::returnValue(sprintf('/action/%s/%s/create', Utility::safe($id, false), $type), $write);
  }

  public static function actionDelete($id, $write = true)
  {
    return Utility::returnValue(sprintf('/action/%s/delete', Utility::safe($id, false)), $write);
  }

  public static function photoDelete($id, $write = true)
  {
    return Utility::returnValue(sprintf('/photo/%s/delete', Utility::safe($id, false)), $write);
  }

  public static function photoEdit($id, $write = true)
  {
    return Utility::returnValue(sprintf('/photo/%s/edit', Utility::safe($id, false)), $write);
  }

  public static function photoUpdate($id, $write = true)
  {
    return Utility::returnValue(sprintf('/photo/%s/update', Utility::safe($id, false)), $write);
  }

  public static function photoUrl($photo, $key, $write = true)
  {
    return Utility::returnValue($photo[sprintf('path%s', $key)], $write);
  }

  public static function photoView($id, $options = null, $write = true)
  {
    if(empty($options))
      return Utility::returnValue(sprintf('/photo/%s/view', Utility::safe($id, false)), $write);
    else
      return Utility::returnValue(sprintf('/photo/%s/view/%s', Utility::safe($id, false), $options), $write);
  }

  public static function photosView($options = null, $write = true)
  {
    if(empty($options))
      return Utility::returnValue('/photos/view', $write);
    else
      return Utility::returnValue(sprintf('/photos/view/%s', $options), $write);
  }
  
  public static function photoUpload($write = true)
  {
    return Utility::returnValue('/photo/upload', $write);
  }
  
  public static function photosUpload($write = true)
  {
    return Utility::returnValue('/photos/upload', $write);
  }

  public static function tagsView($write = true)
  {
    return Utility::returnValue('/tags/view', $write);
  }

  public static function userLogout($write = true)
  {
    return Utility::returnValue('/user/logout', $write);
  }
}
