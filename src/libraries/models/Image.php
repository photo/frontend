<?php
interface Image
{
  public function __construct($filename);
  public function scale($width, $height, $maintainAspectRatio);
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
  }

}
