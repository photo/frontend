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
  public function __construct();
  public function load($filename);
  public function scale($width, $height, $maintainAspectRatio = true);
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
function getImage()
{
  static $type;
  $modules = getConfig()->get('modules');
  if(!$type && isset($modules->image))
    $type = $modules->image;

  try
  {
    switch($type)
    {
      case 'GraphicsMagick':
        return new ImageGraphicsMagick();
        break;
      case 'ImageMagick':
        return new ImageImageMagick();
        break;
      case 'GD':
        return new ImageGD();
        break;
    }
  }
  catch(OPInvalidImageException $e)
  {
    getLogger()->warn("Invalid image exception thrown for {$type}");
    return false;
  }
}
