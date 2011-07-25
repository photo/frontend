<?php
/**
  * ImageImageMagick model
  *
  * This implements imagine manipulation funcationality as defined by the Image interface.
  * Requires PHP ImageMagick extension.
  * The file is not written to disk until explicitly called.
  * @author Jaisen Mathai <jaisen@jmathai.com>
  */
class ImageImageMagick implements Image
{
  /**
    * Private instance variable that holds an Imagick object
    * @access private
    * @var object
    */
  private $image;

  /**
    * Constructor which initializes the Imagick object.
    *
    * @param string $filename Full path to the file which will be manipulated
    * @return void 
    */
  public function __construct($filename)
  {
    $this->image = new Imagick($filename);
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
    * Save modifications to the image to the file system
    *
    * @param string $outputFile The file to write the modifications to.
    * @return void 
    */
  public function write($outputFile)
  {
    $this->image->writeImage($outputFile);
  }
}

