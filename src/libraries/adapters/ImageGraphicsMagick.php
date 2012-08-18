<?php
/**
  * ImageGraphicsMagick model
  *
  * This implements imagine manipulation funcationality as defined by the Image interface.
  * Requires PHP GraphicsMagick extension.
  * The file is not written to disk until explicitly called.
  * @author Jaisen Mathai <jaisen@jmathai.com>
  */
class ImageGraphicsMagick extends ImageAbstract
{
  /**
    * Private instance variable that holds an Gmagick object
    * @access private
    * @var object
    */
  private $image;

  /**
    * Quasi constructor
    */
  public function init() {}

  /**
    * Loads an image from a file path
    *
    * @param string $filename Full path to the file which will be manipulated
    * @return ImageGraphicsMagick
    */
  public function load($filename)
  {
    try
    {
      $this->filename = $filename;
      $this->image = new Gmagick($filename);
      return $this;
    }
    catch(GmagickException $e)
    {
      OPException::raise(new OPInvalidImageException('Could not create jpeg with GraphicsMagick library'));
    }
  }

  /**
    * Scale an image by $width and $height.
    * By default the aspect ratio is maintained but overridden by the 3rd parameter.
    *
    * @param int $width Width of the resulting image
    * @param int $height Height of the resulting image
    * @param boolean $maintainAspectRatio When false it will crop to exact $width and $height else it will scale using "best fit"
    * @return void
    */
  public function scale($width, $height, $maintainAspectRatio = true)
  {
    if($maintainAspectRatio)
      $this->image->scaleImage(intval($width), intval($height), true);
    else
      $this->image->cropThumbnailImage(intval($width), intval($height));
  }

  /**
    * Greyscale an image
    *
    * @return void
    */
  public function greyscale()
  {
    $this->image->modulateImage(100, 0, 100);
  }

  /**
   * Set compression quality
   */
  public function setCompressionQuality($quality)
  {
    if (method_exists($this->image, 'setCompressionQuality')) {
      $this->image->setCompressionQuality((int) $quality);
    }
  }

  /**
    * Save modifications to the image to the file system
    *
    * @param string $outputFile The file to write the modifications to.
    * @return void
    */
  public function write($outputFile)
  {
    $this->image->write($outputFile);
  }
}
