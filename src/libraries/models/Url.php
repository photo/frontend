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
    return $utilityObj->returnValue(sprintf('/photos/album-%s/list', $utilityObj->safe($id, false)), $write);
  }

  public function albumsView($write = true)
  {
    $utilityObj = new Utility;
    return $utilityObj->returnValue('/albums/list', $write);
  }

  public function manage($write = true)
  {
    $utilityObj = new Utility;
    return $utilityObj->returnValue('/manage', $write);
  }

  public function manageAlbums($write = true)
  {
    $utilityObj = new Utility;
    return $utilityObj->returnValue('/manage/albums', $write);
  }

  public function manageApps($write = true)
  {
    $utilityObj = new Utility;
    return $utilityObj->returnValue('/manage/apps', $write);
  }

  public function manageGroups($write = true)
  {
    $utilityObj = new Utility;
    return $utilityObj->returnValue('/manage/groups', $write);
  }

  public function managePhotos($write = true)
  {
    $utilityObj = new Utility;
    return $utilityObj->returnValue('/manage/photos', $write);
  }

  public function manageSettings($write = true)
  {
    $utilityObj = new Utility;
    return $utilityObj->returnValue('/manage/settings', $write);
  }

  public function resourceMap($id, $write = true)
  {
    $utilityObj = new Utility;
    return $utilityObj->returnValue(sprintf('/s/%s', $id), $write);
  }

  public function photoDelete($id, $write = true)
  {
    $utilityObj = new Utility;
    return $utilityObj->returnValue(sprintf('/photo/%s/delete', $utilityObj->safe($id, false)), $write);
  }

  public function photoDownload($photo, $write = true)
  {
    $utilityObj = new Utility;
    return $utilityObj->returnValue($photo['pathDownload'], $write);
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
    $noOptionsStr = '/p/%s';
    $optionsStr = '/p/%s/%s';
    if(isset($_SERVER['REDIRECT_URL']) && preg_match('#^/photo/#', $_SERVER['REDIRECT_URL']))
    {
      $noOptionsStr = '/photo/%s/view';
      $optionsStr = '/photo/%s/%s/view';
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
    if($utilityObj->enableBetaFeatures())
      return $utilityObj->returnValue('/photos/upload/beta', $write);
    else
      return $utilityObj->returnValue('/photos/upload', $write);
  }

  public function setup($write = true)
  {
    $utilityObj = new Utility;
    return $utilityObj->returnValue('/setup?edit', $write);
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
      $ret[] = sprintf('<a href="%s">%s</a>', $this->photosView("tags-{$tag}", false), $utilityObj->safe($tag, false));

    return $utilityObj->returnValue(implode(', ', $ret), $write);
  }

  public function userLogout($write = true)
  {
    $utilityObj = new Utility;
    return $utilityObj->returnValue('/user/logout', $write);
  }

  public function userManage($write = true)
  {
    $utilityObj = new Utility;
    return $utilityObj->returnValue('/manage', $write);
  }

  public function userSettings($write = true)
  {
    return $this->userManage($write);
  }
}
