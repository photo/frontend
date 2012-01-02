<?php
class MySqlMockHelper
{
  public static function getPhoto($params = null)
  {
    $photo = array(
      'id' => 'foo',
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
