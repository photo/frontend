<?php
class ImageGraphicsMagick implements Image
{
  public $image;
  public function __construct($filename, $outputFile = null)
  {
    $this->image = new Gmagick($filename);
    if($outputFile !== null)
      $this->setOutputFile($outputFile);
    else
      $this->setOutputFile($filename);
  }

  public function scale($width, $height, $maintainAspectRatio = true)
  {
    $this->image->scaleimage(intval($width), intval($height), $maintainAspectRatio); 
  }

  public function setOutputFile($filename)
  {
    $this->image->SetImageFilename($filename);
  }

  public function write($outputFile = null)
  {
    $this->image->write($outputFile);
  }
}
