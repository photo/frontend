<?php
class MySqlMockHelper
{
  public static function getPhoto($params = null)
  {
    $photo = array(
      'id' => 'foo',
      'key' => 'keyish',
      'exif' => '{"foo":"bar"}',
      'extra' => '{"foo":"bar"}',
      'tags' => 'one,two,three',
      'groups' => 'one,two,three'
    );

    if(!empty($params))
    {
      foreach($params as $key => $value)
        $photo->{$key} = $value;
    }
    return $photo;
  }

  public static function getPhotos($count = 1, $params = null)
  {
    $photos = array();
    for($i=0; $i<$count; $i++)
      $photos[] = self::getPhoto($params);

    return $photos;
  }

  public static function getShareToken($override = array())
  {
    $params = array_merge(array(
      'id' => 'foo',
      'owner' => 'owner@owner.com',
      'actor' => 'actor@actor.com',
      'type' => 'album',
      'data' => 'data',
      'dateExpires' => time()+100,
      'groups' => 'one,two,three'
    ), $override);

    return $params;
  }

  public static function getTag($params = null)
  {
    $tag = array(
      'id' => 'foo',
      'exif' => '{"foo":"bar"}',
      'extra' => '{"foo":"bar"}',
      'tags' => 'one,two,three',
      'groups' => 'one,two,three'
    );

    if(!empty($params))
    {
      foreach($params as $key => $value)
        $tag->{$key} = $value;
    }
    return $tag;
  }

  public static function getTags($count = 1, $params = null)
  {
    $tags = array();
    for($i=0; $i<$count; $i++)
      $tags[] = self::getTag($params);

    return $tags;
  }

  public static function getUser($params = null)
  {
    $tag = array(
      'id' => 'foo',
      'lastPhotoId' => 'abc',
      'extra' => '{"foo":"bar"}'
    );

    if(!empty($params))
    {
      foreach($params as $key => $value)
        $tag->{$key} = $value;
    }
    return $tag;
  }
}
