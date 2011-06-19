<?php
interface Image
{
  public function __construct($filename);
  public function scale($width, $height, $maintainAspectRatio);
  public function write($outputFile);
}

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
