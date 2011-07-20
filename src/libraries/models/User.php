<?php
class User
{
  private static $user;
  public static function getNextPhotoId()
  {
    $user = self::getUserRecord();    
    if(!isset($user['lastPhotoId']))
      $user['lastPhotoId'] = '';
    $nextIntId = base_convert($user['lastPhotoId'], 31, 10) + 1;
    $nextId = base_convert($nextIntId, 10, 31);
    self::update(array('lastPhotoId' => $nextId));
    return $nextId;
  }

  public static function getNextActionId()
  {
    $user = self::getUserRecord();    
    if(!isset($user['lastActionId']))
      $user['lastActionId'] = '';
    $nextIntId = base_convert($user['lastActionId'], 31, 10) + 1;
    $nextId = base_convert($nextIntId, 10, 31);
    self::update(array('lastActionId' => $nextId));
    return $nextId;
  }

  // this automatically creates and/or updates the record as needed
  public static function getUserRecord()
  {
    // we cache the user entry per request
    if(self::$user)
      return self::$user;

    $res = getDb()->getUser();
    // onerror return false
    if(!$res)
      return false;

    self::$user = $res;
    return self::$user;
  }

  private static function getDefaultAttributes()
  {
    return array('lastPhotoId' => '', 'lastActionId' => '');
  }

  private static function update($params)
  {
    getDb()->postUser(1, $params);
  }
}
