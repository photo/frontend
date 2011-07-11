<?php
class DatabaseSimpleDb implements DatabaseInterface
{
  private $domain;
  public function __construct($opts)
  {
    $this->db = new AmazonSDB($opts->awsKey, $opts->awsSecret);
    $this->domain = getConfig()->get('aws')->simpleDbDomain;
  }

  public function addAttribute($id, $keyValuePairs, $replace = true)
  {
    $res = $this->db->put_attributes($this->domain, $id, $keyValuePairs, $replace);
    return $res->isOK();
  }

  public function deletePhoto($id)
  {
    $res = $this->db->delete_attributes($this->domain, $id);
    return $res->isOK();
  }

  public function getPhoto($id)
  {
    $res = $this->db->select("select * from `{$this->domain}` where itemName()='{$id}'", array('ConsistentRead' => 'true'));
    if(isset($res->body->SelectResult->Item))
      return self::normalizePhoto($res->body->SelectResult->Item);
    else
      return false;
  }

  public function getPhotos($filter = array(), $limit = 25, $offset = null)
  {
    // TODO: support logic for multiple conditions
    $where = '';
    if(!empty($filter))
    {
      if(isset($filter['tags']) && !empty($filter['tags']))
      {
        if(!is_array($filter['tags']))
          $filter['tags'] = (array)explode(',', $filter['tags']);
        $where = "where tags in('" . implode("','", $filter['tags']) . "')";
      }
    }
    $res = $this->db->select("select * from `{$this->domain}` {$where} limit {$limit}", array('ConsistentRead' => 'true'));

    $photos = array();
    foreach($res->body->SelectResult->Item as $photo)
    {
      $photos[] = $this->normalizePhoto($photo);
    }
    return $photos;
  }

  public function postPhoto($id, $params)
  {
    $params = self::preparePhoto($id, $params);
    $res = $this->db->put_attributes($this->domain, $id, $params, true);
    return $res->isOK();
  }

  public function putPhoto($id, $params)
  {
    $res = $this->db->put_attributes($this->domain, $id, $params);
    return $res->isOK();
  }

  public function initialize()
  {
    $domains = $this->db->get_domain_list("/^{$this->domain}$/");
    if(count($domains) == 1)
      return true;

    $res = $this->db->create_domain($this->domain);
    return $res->isOK();
  }

  private function normalizePhoto($raw)
  {
    $appId = getConfig()->get('application')->appId;
    $id = strval($raw->Name);
    $photo = array('tags' => '');
    foreach($raw->Attribute as $item)
    {
      $name = (string)$item->Name;
      $value = (string)$item->Value;
      if($name == 'tags')
        $photo[$name][] = $value;
      else
        $photo[$name] = $value;
    }
    return Photo::normalize($id, $appId, $photo);
  }

  private function preparePhoto($id, $params)
  {
    $params['Name'] = $id;
    if(isset($params['tags']) || !is_array($params['tags']))
      $params['tags'] = (array)explode(',', $params['tags']);
    return $params;
  }
}
