<?php
/**
 * Abstract for the Image models.
 *
 * This defines the interface for any model that modifies and manipulates an image.
 * Currently supports ImageMagick and GraphicsMagick.
 * @author Jaisen Mathai <jaisen@jmathai.com>
 */
abstract class ImageAbstract
{
  protected $config, $filename;
  abstract public function init();
  abstract public function load($filename);
  abstract public function scale($width, $height, $maintainAspectRatio = true);
  abstract public function greyscale();
  abstract public function write($outputFile);
  abstract public function setCompressionQuality($quality);

  public function __construct()
  {
    $this->config = getConfig()->get();
    $this->init();
  }

  public function rotate($degrees)
  {
    if(isset($this->config->modules->exiftran) && !empty($this->config->modules->exiftran) && is_executable($this->config->modules->exiftran))
    {
      $option = null;
      if($degrees == 90)
        $option = '-9';
      elseif($degrees == 180)
        $option = '-1';
      elseif($degrees == 270)
        $option = '-2';

      if($option !== null)
        $this->execute(sprintf('%s -i %s %s', $this->config->modules->exiftran, $option, $this->filename));

      return true;
    }

    return false;
  }

  protected function execute($command)
  {
    exec($command);
  }
}
