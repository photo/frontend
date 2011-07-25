<?php
/**
 * Interface for the Image models.
 *
 * This defines the interface for any model that modifies and manipulates an image.
 * Currently supports ImageMagick and GraphicsMagick.
 * @author Jaisen Mathai <jaisen@jmathai.com>
 */
interface ImageInterface
{
  public function __construct($filename);
  public function scale($width, $height, $maintainAspectRatio);
  public function greyscale();
  public function write($outputFile);
}

/**
  * The public interface for instantiating an image obect.
  * This returns the appropriate type of object by reading the config.
  * Accepts a set of params that must include a type and targetType
  *
  * @return object An image object that implements ImageInterface
  */
function getImage($image)
{
  static $type;
  if(!$type)
    $type = getConfig()->get('modules')->image;

  switch($type)
  {
    case 'GraphicsMagick':
      return new ImageGraphicsMagick($image);
      break;
    case 'ImageMagick':
      return new ImageImageMagick($image);
      break;
  }
}
