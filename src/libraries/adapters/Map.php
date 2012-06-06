<?php
/**
 * Interface for Map.
 *
 * This defines the interface Maps to allow using different providers
 * @author Hubert Figuiere <hub@figuiere.net>
 */
interface MapInterface
{
  public function __construct();
  public function linkUrl($latitude, $longitude, $zoom);
  public function staticMap($latitude, $longitude, $zoom, $size, $type = 'roadmap');
}

/**
  * The public interface for instantiating an maps object.
  * This returns the appropriate type of object by reading the config.
  * Accepts a set of params that must include a type and targetType
  *
  * @return object A maps object that implements MapsInterface
  */
function getMap()
{
  static $type;
  $map = getConfig()->get('map');
  if(!$type && isset($map->service))
    $type = $map->service;

  try
  {
    switch($type)
    {
      case 'osm':
        return new MapOsm();
        break;
      case 'google':
        return new MapGoogle();
        break;
    }
  }
  catch(OPInvalidMapException $e)
  {
    getLogger()->warn("Invalid mapping exception thrown for {$type}");
    return false;
  }
}
