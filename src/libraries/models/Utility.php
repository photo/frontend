<?php
class Utility
{
  public static function dateLong($ts)
  {
    return date('l, F jS, Y \a\t g:ia', $ts);
  }

  public static function englishPermission($permission)
  {
    return $permission ? 'public' : 'private';
  }

  public static function englishTime($time, $prefix = null, $suffix = null)
  {
    if(empty($time))
      return '';

    $seconds = intval(time() - $time);
    $hours = intval($seconds / 3600);
    if($hours < 0)
      return '--';
    elseif($hours < 1)
      return "{$prefix} a few minutes ago {$suffix}";
    elseif($hours < 24)
      return "{$prefix} {$hours} hour" . self::plural($hours) . " ago {$suffix}";

    $days = intval($seconds / 86400);
    if($days <= 7)
      return "{$prefix} {$days} day" . self::plural($days) . " ago {$suffix}";

    $weeks = intval($days / 7);
    if($weeks <= 4)
      return "{$prefix} {$weeks} week" . self::plural($weeks) . " ago {$suffix}";

    $months = intval($days / 30);
    if($months < 12)
      return "{$prefix} {$months} month" . self::plural($months) . " ago {$suffix}";

    $years = intval($days / 365);
    return "{$prefix} {$years} year" . self::plural($years) . " ago {$suffix}";
  }

  public static function isActiveTab($label)
  {
    if(!isset($_GET['__route__']))
      return $label == 'home';

    $route = $_GET['__route__'];
    switch($label)
    {
      case 'home':
        if(preg_match('#^/$#', $route))
          return true;
        return false;
        break;
      case 'photo':
      case 'photos':
        if(!empty($route) && preg_match('#^/photo#', $route) && !preg_match('#^/photos/upload#', $route))
          return true;
        return false;
        break;
      case 'tags':
        if(!empty($route) && preg_match('#^/tags$#', $route))
          return true;
        return false;
        break;
      case 'upload':
        if(!empty($route) && preg_match('#^/photos/upload#', $route))
          return true;
        return false;
        break;
    }
  }

  public static function plural($int, $word = null)
  {
    if(empty($word))
      return $int > 1 ? 's' : '';
    else
      return $int > 1 ? "{$word}s" : $word;
  }

  public static function staticMapUrl($latitude, $longitude, $zoom, $size, $type = 'roadmap')
  {
    //http://maps.googleapis.com/maps/api/staticmap?center=Brooklyn+Bridge,New+York,NY&zoom=14&size=512x512&maptype=roadmap&markers=color:blue%7Clabel:S%7C40.702147,-74.015794&markers=color:green%7Clabel:G%7C40.711614,-74.012318&markers=color:red%7Ccolor:red%7Clabel:C%7C40.718217,-73.998284&sensor=false
    return "http://maps.googleapis.com/maps/api/staticmap?center={$latitude},{$longitude}&zoom={$zoom}&size={$size}&maptype={$type}&markers=color:gray%7C{$latitude},{$longitude}&sensor=false";
  }

  public static function tagsAsLinks($tags)
  {
    $ret = array();
    sort($tags);
    foreach($tags as $tag)
      $ret[] = '<a href="/photos/tags-'.$tag.'">'.$tag.'</a>';

    return implode(', ', $ret);
  }
}
