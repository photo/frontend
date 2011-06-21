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
    $this->image->scaleimage(intval($width), intval($height), $maintainAspectRatio); 
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
