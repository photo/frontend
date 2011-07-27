<?php
/**
 * Amazon AWS SimpleDb implementation for DatabaseInterface
 *
 * This class defines the functionality defined by DatabaseInterface for AWS SimpleDb.
 * @author Jaisen Mathai <jaisen@jmathai.com>
 */
class DatabaseSimpleDb implements DatabaseInterface
{
  /**
    * Member variables holding the names to the SimpleDb domains needed and the database object itself.
    * @access private
    * @var array
    */
  private $db, $domainAction, $domainPhoto, $domainTag, $domainUser;

  /**
    * Constructor
    *
    * @param array $opts Credentials for AWS
    * @return void 
    */
  public function __construct($opts)
  {
    $this->db = new AmazonSDB($opts->awsKey, $opts->awsSecret);
    $this->domainPhoto = getConfig()->get('aws')->simpleDbDomain;
    $this->domainAction = getConfig()->get('aws')->simpleDbDomain.'Action';
    $this->domainUser = getConfig()->get('aws')->simpleDbDomain.'User';
    $this->domainTag = getConfig()->get('aws')->simpleDbDomain.'Tag';
  }

  /**
    * TODO remove this crap and use postPhoto instead
    */
  public function addAttribute($id, $keyValuePairs, $replace = true)
  {
    $res = $this->db->put_attributes($this->domainPhoto, $id, $keyValuePairs, $replace);
    return $res->isOK();
  }

  /**
    * Delete an action from the database
    *
    * @param string $id ID of the action to delete
    * @return boolean 
    */
  public function deleteAction($id)
  {
    $res = $this->db->delete_attributes($this->domainAction, $id);
    return $res->isOK();
  }

  /**
    * Delete a photo from the database
    *
    * @param string $id ID of the photo to delete
    * @return boolean 
    */
  public function deletePhoto($id)
  {
    $res = $this->db->delete_attributes($this->domainPhoto, $id);
    return $res->isOK();
  }

  /**
    * Retrieve a photo from the database
    *
    * @param string $id ID of the photo to retrieve
    * @return mixed Array on success, FALSE on failure 
    */
  public function getPhoto($id)
  {
    $res = $this->db->select("select * from `{$this->domainPhoto}` where itemName()='{$id}'", array('ConsistentRead' => 'true'));
    if(isset($res->body->SelectResult->Item))
      return self::normalizePhoto($res->body->SelectResult->Item);
    else
      return false;
  }

  /**
    * Retrieve a photo from the database and include the actions on the photo.
    * Actions are stored in a separate domain so the calls need to be made in parallel
    *
    * @param string $id ID of the photo to retrieve
    * @return mixed Array on success, FALSE on failure 
    */
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

  /**
    * Get tags filtered by $filter
    * Consistent read set to false
    *
    * @param array $filters Filters to be applied to the list
    * @return mixed Array on success, FALSE on failure 
    */
  public function getTags($filters = array())
  {
    $res = $this->db->select("select * from `{$this->domainTag}`", array('ConsistentRead' => 'false'));
    $tags = array();
    if(isset($res->body->SelectResult))
    {
      if(isset($res->body->SelectResult->Item))
      {
        foreach($res->body->SelectResult->Item as $val)
          $tags[] = self::normalizeTag($val);

        return $tags;
      }

      return null;
    }
    return false;
  }

  public function getPhotos($filters = array(), $limit, $offset = null)
  {
    // TODO: support logic for multiple conditions
    $where = '';
    if(!empty($filters) && is_array($filters))
    {
      foreach($filters as $name => $value)
      {
        switch($name)
        {
          case 'tags':
            if(!is_array($value))
              $value = (array)explode(',', $value);
            $where = $this->buildWhere($where, "tags in('" . implode("','", $value) . "')");
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
            $where = $this->buildWhere($where, "{$field} is not null");
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

  /**
    * Get the user record entry.
    *
    * @return mixed Array on success, NULL if user record is empty, FALSE on error 
    */
  public function getUser()
  {
    $res = $this->db->select("select * from `{$this->domainUser}` where itemName()='1'", array('ConsistentRead' => 'true'));
    if(isset($res->body->SelectResult->Item))
      return self::normalizeUser($res->body->SelectResult->Item);
    elseif(isset($res->body->SelectResult))
      return null;
    else
      return false;
  }

  /**
    * Update the information for an existing photo.
    * This method overwrites existing values present in $params.
    *
    * @param string $id ID of the photo to update.
    * @param array $params Attributes to update.
    * @return boolean
    */
  public function postPhoto($id, $params)
  {
    $params = self::preparePhoto($id, $params);
    $res = $this->db->put_attributes($this->domainPhoto, $id, $params, true);
    return $res->isOK();
  }

  /**
    * Update multiple tags.
    * The $params should include the tag in the `id` field.
    * [{id: tag1, count:10, longitude:12.34, latitude:56.78},...]
    *
    * @param array $params Tags and related attributes to update.
    * @return boolean
    */
  public function postTag($params)
  {
    if(!isset($params['id']) || empty($params['id']))
      return false;
    $tag = $params['id'];
    unset($params['id']);
    $res = $this->db->put_attributes($this->domainTag, $tag, $params, true);
    return $res->isOK();
  }

  /**
    * Increment the `count` field on the tags specified.
    *
    * @param array $params An array of tag ids (i.e. a tag name)
    * @return boolean
    */
  public function postTagIncrement($tags) {}

  /**
    * Update the information for the user record.
    * This method overwrites existing values present in $params.
    *
    * @param string $id ID of the user to update which is always 1.
    * @param array $params Attributes to update.
    * @return boolean
    */
  public function postUser($id, $params)
  {
    // make sure we don't overwrite an existing user record
    $res = $this->db->put_attributes($this->domainUser, $id, $params, true);
    return $res->isOK();
  }

  /**
    * Add a new action to the database
    * This method does not overwrite existing values present in $params - hence "new action".
    *
    * @param string $id ID of the action to update which is always 1.
    * @param array $params Attributes to update.
    * @return boolean
    */
  public function putAction($id, $params)
  {
    $res = $this->db->put_attributes($this->domainAction, $id, $params);
    return $res->isOK();
  }

  /**
    * Add a new photo to the database
    * This method does not overwrite existing values present in $params - hence "new photo".
    *
    * @param string $id ID of the photo to update which is always 1.
    * @param array $params Attributes to update.
    * @return boolean
    */
  public function putPhoto($id, $params)
  {
    $res = $this->db->put_attributes($this->domainPhoto, $id, $params);
    return $res->isOK();
  }

  /**
    * Alias of postTags
    */
  public function putTag($params)
  {
    return $this->postTag($params);
  }

  /**
    * Add a new user to the database
    * This method does not overwrite existing values present in $params - hence "new user".
    *
    * @param string $id ID of the user to update which is always 1.
    * @param array $params Attributes to update.
    * @return boolean
    */
  public function putUser($id, $params)
  {
    // make sure we don't overwrite an existing user record
    $res = $this->db->put_attributes($this->domainUser, $id, $params);
    return $res->isOK();
  }

  /**
    * Initialize the database by creating the domains needed.
    * This is called from the Setup controller.
    *
    * @return boolean
    */
  public function initialize()
  {
    $domains = $this->db->get_domain_list("/^{$this->domainPhoto}(Action|Tag|User)?$/");
    var_dump($domains);
    if(count($domains) == 4)
      return true;

    $queue = new CFBatchRequest();
    $this->db->batch($queue)->create_domain($this->domainAction);
    $this->db->batch($queue)->create_domain($this->domainPhoto);
    $this->db->batch($queue)->create_domain($this->domainTag);
    $this->db->batch($queue)->create_domain($this->domainUser);
    $responses = $this->db->batch($queue)->send();
    return $responses->areOK();
  }

  /**
    * Utility function to help build the WHERE clause for SELECT statements.
    *
    * @param string $existing Existing where clause.
    * @param string $add Clause to add.
    * @return string
    */
  private function buildWhere($existing, $add)
  {
    if(empty($existing))
      return "where {$add} ";
    else
      return "{$existing} and {$add} ";
  }

  /**
    * Normalizes data from simpleDb into schema definition
    *
    * @param SimpleXMLObject $raw An action from SimpleDb in SimpleXML.
    * @return array
    */
  private function normalizeAction($raw)
  {
    $action = array();
    foreach($raw->Attribute as $item)
    {
      $name = (string)$item->Name;
      $value = (string)$item->Value;
      $action[$name] = $value;
    }
    $action['id'] = strval($raw->Name);
    $action['appId'] = getConfig()->get('application')->appId;
    return $action;
  }

  /**
    * Normalizes data from simpleDb into schema definition
    *
    * @param SimpleXMLObject $raw A photo from SimpleDb in SimpleXML.
    * @return array
    */
  private function normalizePhoto($raw)
  {
    $photo = array();
    foreach($raw->Attribute as $item)
    {
      $name = (string)$item->Name;
      $value = (string)$item->Value;
      if($name == 'tags')
        $photo[$name][] = $value;
      else
        $photo[$name] = $value;
    }
    $photo['id'] = strval($raw->Name);
    $photo['appId'] = getConfig()->get('application')->appId;
    return $photo;
  }

  /**
    * Normalizes data from simpleDb into schema definition
    *
    * @param SimpleXMLObject $raw A tag from SimpleDb in SimpleXML.
    * @return array
    */
  private function normalizeTag($raw)
  {
    $tag = array();
    foreach($raw->Attribute as $item)
    {
      $name = (string)$item->Name;
      $value = (string)$item->Value;
      $tag[$name] = $value;
    }
    $tag['id'] = strval($raw->Name);
    return $tag;
  }

  /**
    * Normalizes data from simpleDb into schema definition
    *
    * @param SimpleXMLObject $raw A user from SimpleDb in SimpleXML.
    * @return array
    */
  private function normalizeUser($raw)
  {
    $user = array();
    foreach($raw->Attribute as $item)
    {
      $name = (string)$item->Name;
      $value = (string)$item->Value;
      $user[$name] = $value;
    }
    return $user;
  }

  /**
    * Formats a photo to be updated or added to the database.
    * Primarily to properly format tags as an array.
    *
    * @param string $id ID of the photo.
    * @param array $params Parameters for the photo to be normalized.
    * @return array
    */
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
