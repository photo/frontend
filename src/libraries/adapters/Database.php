<?php
interface DatabaseInterface
{
  public function getPhotos($start = 0, $count = 25) {}
}
