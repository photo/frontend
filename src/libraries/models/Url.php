<?php
class Url
{
  public function __construct() { }

  public function actionCreate($id, $type, $write = true)
  {
    $utilityObj = new Utility;
    return $utilityObj->returnValue(sprintf('/action/%s/%s/create', $utilityObj->safe($id, false), $type), $write);
  }

  public function actionDelete($id, $write = true)
  {
    $utilityObj = new Utility;
    return $utilityObj->returnValue(sprintf('/action/%s/delete', $utilityObj->safe($id, false)), $write);
  }

  public function albumView($id, $write = true)
  {
    $utilityObj = new Utility;
    return $utilityObj->returnValue(sprintf('/album/%s', $utilityObj->safe($id, false)), $write);
  }

  public function albumsView($id, $write = true)
  {
    $utilityObj = new Utility;
    return $utilityObj->returnValue('/albums/list', $write);
  }

  public function photoDelete($id, $write = true)
  {
    $utilityObj = new Utility;
    return $utilityObj->returnValue(sprintf('/photo/%s/delete', $utilityObj->safe($id, false)), $write);
  }

  public function photoEdit($id, $write = true)
  {
    $utilityObj = new Utility;
    return $utilityObj->returnValue(sprintf('/photo/%s/edit', $utilityObj->safe($id, false)), $write);
  }

  public function photoUpdate($id, $write = true)
  {
    $utilityObj = new Utility;
    return $utilityObj->returnValue(sprintf('/photo/%s/update', $utilityObj->safe($id, false)), $write);
  }

  public function photoUrl($photo, $key, $write = true)
  {
    $utilityObj = new Utility;
    return $utilityObj->returnValue($photo[sprintf('path%s', $key)], $write);
  }

  public function photoView($id, $options = null, $write = true)
  {
    $utilityObj = new Utility;
    $options = preg_replace('#/page-\d+#', '', $options);
    $noOptionsStr = '/photo/%s/view';
    $optionsStr = '/photo/%s/%s/view';
    if(isset($_SERVER['REDIRECT_URL']) && preg_match('#^/p/#', $_SERVER['REDIRECT_URL']))
    {
      $noOptionsStr = '/p/%s';
      $optionsStr = '/p/%s/%s';
    }
      
    if(empty($options))
      return $utilityObj->returnValue(sprintf($noOptionsStr, $utilityObj->safe($id, false)), $write);
    else
      return $utilityObj->returnValue(sprintf($optionsStr, $utilityObj->safe($id, false), $options), $write);
  }

  public function photosView($options = null, $write = true)
  {
    $utilityObj = new Utility;
    if(empty($options))
      return $utilityObj->returnValue('/photos/list', $write);
    else
      return $utilityObj->returnValue(sprintf('/photos/%s/list', $options), $write);
  }

  public function photoUpload($write = true)
  {
    $utilityObj = new Utility;
    return $utilityObj->returnValue('/photos/upload', $write);
  }

  public function photosUpload($write = true)
  {
    $utilityObj = new Utility;
    return $utilityObj->returnValue('/photos/upload', $write);
  }

  public function tagsView($write = true)
  {
    $utilityObj = new Utility;
    return $utilityObj->returnValue('/tags/list', $write);
  }

  public function tagsAsLinks($tags, $write = true)
  {
    $utilityObj = new Utility;
    $ret = array();
    foreach($tags as $tag)
      $ret[] = sprintf('<a href="%s">%s</a>', self::photosView("tags-{$tag}", false), $utilityObj->safe($tag, false));

    return $utilityObj->returnValue(implode(', ', $ret), $write);
  }

  public function userLogout($write = true)
  {
    $utilityObj = new Utility;
    return $utilityObj->returnValue('/user/logout', $write);
  }

  public function userSettings($write = true)
  {
    $utilityObj = new Utility;
    return $utilityObj->returnValue('/user/settings', $write);
  }
}
