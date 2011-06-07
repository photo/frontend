<?php
interface DatabaseInterface
{
  public function getPhotos();
  private function normalizePhotos($raw);
}
