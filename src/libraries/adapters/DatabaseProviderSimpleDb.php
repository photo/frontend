<?php
class DatabaseProviderSimpleDb implements DatabaseInterface
{
  public function __construct($opts)
  {
    $this->db = new AmazonSDB($opts->awsKey, $opts->awsSecret);
  }

  public function getPhotos()
  {
    $res = $this->db->select('select * from photos');

    $photos = array();
    foreach($res->body->SelectResult->Item as $photo)
    {
      $photos[] = $this->normalizePhoto($photo);
    }
    return $photos;
  }

  private function normalizePhoto($raw)
  {
    $photo = new Photo(strval($raw->Name));
    foreach($raw->Attribute as $item)
    {
      $name = (string)$item->Name;
      $value = (string)$item->Value;
      $photo->$name = $value;
    }
    return $photo;
  }
}
