<?php
/**
 * Interface for Maps.
 *
 * This defines the interface Maps to allow using different providers
 * @author Hubert Figuiere <hub@figuiere.net>
 */
interface MapsInterface
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
function getMaps()
{
  static $type;
  $maps = getConfig()->get('maps');
  if(!$type && isset($maps->service))
    $type = $maps->service;

  try
  {
    switch($type)
    {
      case 'osm':
        return new MapsOsm();
        break;
      case 'google':
        return new MapsGoogle();
        break;
    }
  }
  catch(OPInvalidMapsException $e)
  {
    getLogger()->warn("Invalid mapping exception thrown for {$type}");
    return false;
  }
}
