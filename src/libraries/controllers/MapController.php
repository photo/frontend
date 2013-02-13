<?php
class MapController extends BaseController
{
  public function __construct()
  {
    parent::__construct();
  }

  public function render($latitude, $longitude, $zoom, $size, $type)
  {
    $mapUrl = getMap()->staticMap($latitude, $longitude, $zoom, $size, $type, false);

    header('Content-type: image/png');
    header(sprintf('Last-Modified: %s GMT', gmdate('D, d M Y H:i:s', strtotime('-1 year')))); 
    header(sprintf('Etag: %s', md5($mapUrl)));

    $ch = curl_init($mapUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    // don't complain about SSL
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 1);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
    echo curl_exec($ch);
    curl_close($ch);
  }
}
