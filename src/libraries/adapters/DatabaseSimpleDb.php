<?php
class DatabaseSimpleDb implements DatabaseInterface
{
  private $domain;
  public function __construct($opts)
  {
    $this->db = new AmazonSDB($opts->awsKey, $opts->awsSecret);
    $this->domain = getConfig()->get('aws')->domain;
  }

  public function deletePhoto($id)
  {
    $res = $this->db->delete_attributes($this->domain, $id);
    return $res->isOK();
  }

  public function getPhoto($id)
  {
    $res = $this->db->select("select * from {$this->domain} where itemName()='{$id}'", array('ConsistentRead' => 'true'));
    return self::normalizePhoto($res->body->SelectResult->Item);
  }

  public function getPhotos()
  {
    $res = $this->db->select("select * from {$this->domain}", array('ConsistentRead' => 'true'));

    $photos = array();
    foreach($res->body->SelectResult->Item as $photo)
    {
      $photos[] = $this->normalizePhoto($photo);
    }
    return $photos;
  }

  public function putPhoto($id, $params)
  {
    $res = $this->db->put_attributes($this->domain, $id, $params);
    return $res->isOK();
  }

  public function addAttribute($id, $keyValuePairs, $replace = true)
  {
    $res = $this->db->put_attributes($this->domain, $id, $keyValuePairs, $replace);
    return $res->isOK();
  }

  private function normalizePhoto($raw)
  {
    $id = strval($raw->Name);
    $photo = array();
    foreach($raw->Attribute as $item)
    {
      $name = (string)$item->Name;
      $value = (string)$item->Value;
      $photo[$name] = $value;
    }
    return Photo::normalize($id, $photo);
  }
}
