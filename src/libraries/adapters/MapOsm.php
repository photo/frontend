<?php
class MapOsm implements MapInterface
{
  /**
    * Constructor
    */
  public function __construct() {}


  public function linkUrl($latitude, $longitude, $zoom)
  {
    return "http://www.openstreetmap.org/?lat={$latitude}&lon={$longitude}&zoom={$zoom}&layers=M";
  }

  public function staticMap($latitude, $longitude, $zoom, $size, $type = 'roadmap')
  {
    return "http://staticmap.openstreetmap.de/staticmap.php?center={$latitude},{$longitude}&zoom={$zoom}&size={$size}&markers={$latitude},{$longitude},ol-marker";
  }
}
