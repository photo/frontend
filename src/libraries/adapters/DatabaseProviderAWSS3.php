<?php
class DatabaseProviderAWSS3 implements DatabaseInterface
{
  public function __construct($opts)
  {
    $this->db = new EpiSimpleDb($opts->awsKey, $opts->awsSecret, $opts->domain);
  }

  public function getPhotos($start = 0, $count = 25)
  {
    
  }

  private function normalizePhoto($raw)
  {
    $photo = new Photo();
    $photo->name = $raw->name;
    $photo->path = $raw->path;
    return $photo;
  }
}
