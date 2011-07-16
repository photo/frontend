<?php
class ImageGraphicsMagick implements Image
{
  public $image;
  public function __construct($filename)
  {
    $this->image = new Gmagick($filename);
  }

  public function scale($width, $height, $maintainAspectRatio = true)
  {
    if($maintainAspectRatio)
      $this->image->scaleImage(intval($width), intval($height), true);
    else
      $this->image->cropThumbnailImage(intval($width), intval($height));
  }

  public function greyscale()
  {
    $this->image->modulateImage(100, 0, 100);
  }

  public function write($outputFile)
  {
    $this->image->write($outputFile);
  }
}
