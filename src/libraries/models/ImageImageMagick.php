<?php
class ImageImageMagick implements Image
{
  public $image;
  public function __construct($filename)
  {
    $this->image = new Imagick($filename);
  }

  public function scale($width, $height, $maintainAspectRatio = true)
  {
    $this->image->scaleImage(intval($width), intval($height), $maintainAspectRatio);
  }

  public function greyscale()
  {
    $this->image->modulateImage(100, 0, 100);
  }

  public function write($outputFile)
  {
    $this->image->writeImage($outputFile);
  }
}

