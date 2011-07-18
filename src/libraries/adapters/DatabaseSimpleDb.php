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
        $res = $this->db->select("select * from `{$this->domain}` {$where} {$sortBy} limit {$iterator}", $params);
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
    $this->db->batch($queue)->select($sql = "select * from `{$this->domain}` {$where} {$sortBy} limit {$limit}", $params);
    if(isset($params['NextToken']))
      unset($params['NextToken']);
    $this->db->batch($queue)->select("select count(*) from `{$this->domain}` {$where}", $params);
    $responses = $this->db->batch($queue)->send();

    if(!$responses->areOK())
      return false;

    $photos = array();
    foreach($responses[0]->body->SelectResult->Item as $photo)
      $photos[] = $this->normalizePhoto($photo);

    $photos[0]['totalRows'] = intval($responses[1]->body->SelectResult->Item->Attribute->Value);
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
    if(!isset($params['tags']))
      $params['tags'] = array();
    elseif(!is_array($params['tags']))
      $params['tags'] = (array)explode(',', $params['tags']);
    return $params;
  }
}
