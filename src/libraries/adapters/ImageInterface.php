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

