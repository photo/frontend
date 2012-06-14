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
