<?php
class DatabaseSimpleDb implements DatabaseInterface
{
  private $domainPhoto, $domainAction, $domainUser;
  public function __construct($opts)
  {
    $this->db = new AmazonSDB($opts->awsKey, $opts->awsSecret);
    $this->domainPhoto = getConfig()->get('aws')->simpleDbDomain;
    $this->domainAction = getConfig()->get('aws')->simpleDbDomain.'Action';
    $this->domainUser = getConfig()->get('aws')->simpleDbDomain.'User';
  }

  public function addAttribute($id, $keyValuePairs, $replace = true)
  {
    $res = $this->db->put_attributes($this->domainPhoto, $id, $keyValuePairs, $replace);
    return $res->isOK();
  }

  public function deleteAction($id)
  {
    $res = $this->db->delete_attributes($this->domainAction, $id);
    return $res->isOK();
  }

  public function deletePhoto($id)
  {
    $res = $this->db->delete_attributes($this->domainPhoto, $id);
    return $res->isOK();
  }

  public function getPhoto($id)
  {
    $res = $this->db->select("select * from `{$this->domainPhoto}` where itemName()='{$id}'", array('ConsistentRead' => 'true'));
    if(isset($res->body->SelectResult->Item))
      return self::normalizePhoto($res->body->SelectResult->Item);
    else
      return false;
  }

  public function getPhotoWithActions($id)
  {
    $queue = new CFBatchRequest();
    $this->db->batch($queue)->select("select * from `{$this->domainPhoto}` where itemName()='{$id}'", array('ConsistentRead' => 'true'));
    $this->db->batch($queue)->select("select * from `{$this->domainAction}` where targetType='photo' and targetId='{$id}'", array('ConsistentRead' => 'true'));
    $responses = $this->db->batch($queue)->send();
    if(!$responses->areOk())
      return false;


    if(isset($responses[0]->body->SelectResult->Item))
      $photo = self::normalizePhoto($responses[0]->body->SelectResult->Item);

    $photo['actions'] = array();
    foreach($responses[1]->body->SelectResult->Item as $action)
      $photo['actions'][] = $this->normalizeAction($action);
      
    return $photo;
  }

  public function getPhotos($filters = array(), $limit, $offset = null)
  {
    // TODO: support logic for multiple conditions
    $where = 'where';
    if(!empty($filters) && is_array($filters))
    {
      foreach($filters as $name => $value)
      {
        switch($name)
        {
          case 'tags':
            if(!is_array($value))
              $value = (array)explode(',', $value);
            $where .= " tags in('" . implode("','", $value) . "')";
            break;
          case 'page':
            if($value > 1)
            {
              $value = min($value, 40); // 40 pages at max of 2,500 recursion limit means 100k photos
              $offset = ($limit * $value) - $limit;
            }
            break;
          case 'sortBy':
            $sortBy = 'order by ' . str_replace(',', ' ', $value);
            $field = substr($value, 0, strpos($value, ','));
            $where .= " {$field} is not null";
            break;
        }
      }
    }

    if(!empty($offset))
    {
      $iterator = max(1, intval($offset - 1));
      $nextToken = null;
      $params = array('ConsistentRead' => 'true');
      $currentPage = 1;
      $thisLimit = min($iterator, $offset);
      do
      {
        $res = $this->db->select("select * from `{$this->domainPhoto}` {$where} {$sortBy} limit {$iterator}", $params);
        if(!$res->body->SelectResult->NextToken)
          break;

        $nextToken = $res->body->SelectResult->NextToken;
        $params['NextToken'] = $nextToken;
        $currentPage++;
      }while($currentPage <= $value);
    }

    $params = array('ConsistentRead' => 'true');
    if(isset($nextToken) && !empty($nextToken))
      $params['NextToken'] = $nextToken;


    $queue = new CFBatchRequest();
    $this->db->batch($queue)->select($sql = "select * from `{$this->domainPhoto}` {$where} {$sortBy} limit {$limit}", $params);
    if(isset($params['NextToken']))
      unset($params['NextToken']);
    $this->db->batch($queue)->select("select count(*) from `{$this->domainPhoto}` {$where}", $params);
    $responses = $this->db->batch($queue)->send();

    if(!$responses->areOK())
      return false;

    $photos = array();
    foreach($responses[0]->body->SelectResult->Item as $photo)
      $photos[] = $this->normalizePhoto($photo);

    $photos[0]['totalRows'] = intval($responses[1]->body->SelectResult->Item->Attribute->Value);
    return $photos;
  }

  public function postAction($id, $params)
  {
    $res = $this->db->put_attributes($this->domainAction, $id, $params);
    return $res->isOK();
  }

  public function postPhoto($id, $params)
  {
    $params = self::preparePhoto($id, $params);
    $res = $this->db->put_attributes($this->domainPhoto, $id, $params, true);
    return $res->isOK();
  }

  public function putPhoto($id, $params)
  {
    $res = $this->db->put_attributes($this->domainPhoto, $id, $params);
    return $res->isOK();
  }

  public function initialize()
  {
    $domains = $this->db->get_domain_list("/^{$this->domainPhoto}(User|Action)?$/");
    if(count($domains) == 3)
      return true;
    elseif(count($domains) != 0)
      return false;

    $queue = new CFBatchRequest();
    $this->db->batch($queue)->create_domain($this->domainPhoto);
    $this->db->batch($queue)->create_domain($this->domainAction);
    $this->db->batch($queue)->create_domain($this->domainUser);
    $responses = $this->db->batch($queue)->send();
    return $responses->areOK();
  }

  private function normalizeAction($raw)
  {
    $action = array('id' => strval($raw->Name));
    foreach($raw->Attribute as $item)
    {
      $name = (string)$item->Name;
      $value = (string)$item->Value;
      $action[$name] = $value;
    }
    return $action;
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
    if(!isset($params['tags']))
      $params['tags'] = array();
    elseif(!is_array($params['tags']))
      $params['tags'] = (array)explode(',', $params['tags']);
    return $params;
  }
}
