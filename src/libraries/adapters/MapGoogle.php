<?php
class MapGoogle implements MapInterface
{

  /**
    * Constructor
    */
  public function __construct() {}

  public function linkUrl($latitude, $longitude, $zoom)
  {
    return "http://maps.google.com/maps?q={$latitude},{$longitude}&z={$zoom}";
  }

  public function staticMap($latitude, $longitude, $zoom, $size, $type = 'roadmap')
  {
    return "http://maps.googleapis.com/maps/api/staticmap?center={$latitude},{$longitude}&zoom={$zoom}&size={$size}&maptype={$type}&markers=color:gray%7C{$latitude},{$longitude}&sensor=false";
  }

}
