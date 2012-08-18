<?php
/**
  * ImageGD model
  *
  * This implements image manipulation functionality as defined by the Image interface.
  * Requires PHP GD library.
	* http://php.net/manual/en/book.image.php
  * The file is not written to disk until explicitly called.
  * @author Kevin Hornschemeier <khornschemeier@gmail.com>
  */
class ImageGD extends ImageAbstract
{
  /**
    * Private instance variable that holds a GD object
    * @access private
    * @var object
    */
  private $image;

	/**
    * Private instance variable that holds mime type, width and height of the image
    * @access private
    * @var string
    */
  private $type;
	private $width;
	private $height;
  private $quality = 90;

  /**
    * Quasi constructor
    */
  public function init() {}

  /**
    * Loads an image from a file path
    *
    * @param string $filename Full path to the file which will be manipulated
    * @return ImageGD
    */
  public function load($filename)
  {
    $this->type = get_mime_type($filename);
    if(preg_match('/png$/', $this->type))
      $this->image = imagecreatefrompng($filename);
    elseif(preg_match('/gif$/', $this->type))
      $this->image = @imagecreatefromgif($filename);
    else
      $this->image = @imagecreatefromjpeg($filename);

    if(!$this->image)
      OPException::raise(new OPInvalidImageException('Could not create image with GD library'));

    $this->filename = $filename;
		$this->width = imagesx($this->image);
		$this->height = imagesy($this->image);
    return $this;
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
    {
			// only scale if the destination image is smaller in at least one dimension
			if(!($this->width < $width && $this->height < $height))
			{
				// ratio > 1 is horizontal image
				$ratio = floatval($this->width / $this->height);
				if($ratio >= 1)
					$height = intval($width / $ratio);
				else
					$width = intval($height * $ratio);

				$dstImage = imagecreatetruecolor($width, $height);
				imagecopyresampled($dstImage, $this->image, 0, 0, 0, 0, $width, $height, $this->width, $this->height);
				$this->image = $dstImage;
				$this->width = imagesx($this->image);
				$this->height = imagesy($this->image);
			}
		}
    else
		{
			// check to see if the requested image is bigger than the original
			if($this->width < $width && $this->height < $height)
			{
				$srcX = 0;
				$srcY = 0;
				$width = $srcW = $this->width;
				$height = $srcH = $this->height;
			}
			else
			{
				$srcRatio = floatval($this->width / $this->height);
				$destRatio = floatval($width / $height);
				if($srcRatio > $destRatio) // crop the width
				{
					$srcW = intval($this->height * $destRatio);
					$srcH = $this->height;
					$srcX = intval(($this->width - $srcW) / 2);
					$srcY = 0;
				}
				else if($srcRatio < $destRatio) // crop the height
				{
					$srcW = $this->width;
					$srcH = intval($this->width / $destRatio);
					$srcX = 0;
					$srcY = intval(($this->height - $srcH) / 2);
				}
				else // aspect ratio matches
				{
					$srcX = 0;
					$srcY = 0;
					$srcW = $this->width;
					$srcH = $this->height;
				}
			}

			$dstImage = imagecreatetruecolor($width, $height);
			imagecopyresampled($dstImage, $this->image, 0, 0, $srcX, $srcY, $width, $height, $srcW, $srcH);
			$this->image = $dstImage;
			$this->width = imagesx($this->image);
			$this->height = imagesy($this->image);
		}
  }

  /**
    * Greyscale an image
    *
    * @return void
    */
  public function greyscale()
  {
    $this->image = imagefilter($this->image, IMG_FILTER_GRAYSCALE);
  }

  /**
   * Set compression quality
   */
  public function setCompressionQuality($quality)
  {
    $this->quality = (int) $quality;
  }

  /**
    * Save modifications to the image to the file system
    *
    * @param string $outputFile The file to write the modifications to.
    * @return void
    */
  public function write($outputFile)
  {
    if(preg_match('/png$/', $this->type))
      imagepng($this->image, $outputFile, ceil($this->quality / 10));
    elseif(preg_match('/gif$/', $this->type))
      imagegif($this->image, $outputFile, $this->quality);
    else
      imagejpeg($this->image, $outputFile, $this->quality);
  }
}
