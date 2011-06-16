<?php
interface DatabaseInterface
{
  public function deletePhoto($id);
  public function getPhoto($id);
  public function getPhotos();
  //private function normalizePhoto($raw);
}
