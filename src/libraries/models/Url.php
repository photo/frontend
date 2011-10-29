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
    $options = preg_replace('#/page-\d+#', '', $options);
    if(empty($options))
      return Utility::returnValue(sprintf('/photo/%s/view', Utility::safe($id, false)), $write);
    else
      return Utility::returnValue(sprintf('/photo/%s/view/%s', Utility::safe($id, false), $options), $write);
  }

  public static function photosView($options = null, $write = true)
  {
    if(empty($options))
      return Utility::returnValue('/photos/list', $write);
    else
      return Utility::returnValue(sprintf('/photos/%s/list', $options), $write);
  }

  public static function photoUpload($write = true)
  {
    return Utility::returnValue('/photos/upload', $write);
  }

  public static function photosUpload($write = true)
  {
    return Utility::returnValue('/photos/upload', $write);
  }

  public static function tagsView($write = true)
  {
    return Utility::returnValue('/tags/list', $write);
  }

  public static function tagsAsLinks($tags, $write = true)
  {
    $ret = array();
    foreach($tags as $tag)
      $ret[] = sprintf('<a href="%s">%s</a>', self::photosView("tags-{$tag}", false), Utility::safe($tag, false));

    return Utility::returnValue(implode(', ', $ret), $write);
  }

  public static function userLogout($write = true)
  {
    return Utility::returnValue('/user/logout', $write);
  }

  public static function userSettings($write = true)
  {
    return Utility::returnValue('/user/settings', $write);
  }
}
