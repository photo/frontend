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
    */
  private $db, $domainAction, $domainCredential, $domainPhoto, $domainTag, $domainUser;

  /**
    * Constructor
    *
    * @return void
    */
  public function __construct()
  {
    $this->db = new AmazonSDB(getConfig()->get('credentials')->awsKey, getConfig()->get('credentials')->awsSecret);
    $this->domainPhoto = getConfig()->get('aws')->simpleDbDomain;
    $this->domainAction = getConfig()->get('aws')->simpleDbDomain.'Action';
    $this->domainCredential = getConfig()->get('aws')->simpleDbDomain.'Credential';
    $this->domainGroup = getConfig()->get('aws')->simpleDbDomain.'Group';
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
    * Retrieve a credential with $id
    *
    * @param string $id ID of the credential to get
    * @return mixed Array on success, FALSE on failure
    */
  public function getCredential($id)
  {
    $res = $this->db->select("SELECT * FROM `{$this->domainCredential}` WHERE itemName()='{$id}' AND status='1'", array('ConsistentRead' => 'true'));
    if(isset($res->body->SelectResult->Item))
      return self::normalizeCredential($res->body->SelectResult->Item);
    else
      return false;
  }

  /**
    * Retrieve groups from the database optionally filter by member (email)
    *
    * @param string $email email address to filter by
    * @return mixed Array on success, NULL on empty, FALSE on failure 
    */
  public function getGroups($email = null)
  {
    if(empty($email))
      $res = $this->db->select("SELECT * FROM `{$this->domainGroup}` ORDER BY name", array('ConsistentRead' => 'true'));
    else
      $res = $this->db->select("SELECT * FROM `{$this->domainGroup}` WHERE members in ('{$email}') ORDER BY name", array('ConsistentRead' => 'true'));

    if(isset($res->body->SelectResult->Item))
    {
      $groups = array();
      foreach($res->body->SelectResult->Item as $group)
        $groups[] = self::normalizeGroup($group);
      return $groups;
    }
    elseif(!isset($res->body->SelectResult))
    {
      return null;
    }
  }

  /**
    * Get a photo specified by $id
    *
    * @param string $id ID of the photo to retrieve
    * @return mixed Array on success, FALSE on failure
    */
  public function getPhoto($id)
  {
    $res = $this->db->select("SELECT * FROM `{$this->domainPhoto}` WHERE itemName()='{$id}'", array('ConsistentRead' => 'true'));
    if(isset($res->body->SelectResult->Item))
      return self::normalizePhoto($res->body->SelectResult->Item);
    else
      return false;
  }

  /**
    * Retrieve the next and previous photo surrounding photo with $id
    *
    * @param string $id ID of the photo to get next and previous for
    * @return mixed Array on success, FALSE on failure
    */
  public function getPhotoNextPrevious($id)
  {
    $photo = $this->getPhoto($id);
    if(!$photo)
      return false;

    $queue = new CFBatchRequest();
    $this->db->batch($queue)->select("SELECT * FROM `{$this->domainPhoto}` WHERE dateTaken>'{$photo['dateTaken']}' AND dateTaken IS NOT NULL ORDER BY dateTaken ASC LIMIT 1");
    $this->db->batch($queue)->select("SELECT * FROM `{$this->domainPhoto}` WHERE dateTaken<'{$photo['dateTaken']}' AND dateTaken IS NOT NULL ORDER BY dateTaken DESC LIMIT 1");
    $responses = $this->db->batch($queue)->send();
    if(!$responses->areOK())
      return false;

    $ret = array();
    if(isset($responses[0]->body->SelectResult->Item))
      $ret['previous'] = self::normalizePhoto($responses[0]->body->SelectResult->Item);
    if(isset($responses[1]->body->SelectResult->Item))
      $ret['next'] = self::normalizePhoto($responses[1]->body->SelectResult->Item);

    return $ret;
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
    $this->db->batch($queue)->select("SELECT * FROM `{$this->domainPhoto}` WHERE itemName()='{$id}'", array('ConsistentRead' => 'true'));
    $this->db->batch($queue)->select("SELECT * FROM `{$this->domainAction}` WHERE targetType='photo' AND targetId='{$id}'", array('ConsistentRead' => 'true'));
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
    * Get a list of a user's photos filtered by $filter, $limit and $offset
    *
    * @param array $filters Filters to be applied before obtaining the result
    * @return mixed Array on success, FALSE on failure
    */
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
          case 'groups':
            $where = $this->buildWhere($where, "groups IN('" . implode("','", $value) . "')");
            break;
          case 'page':
            if($value > 1)
            {
              $value = min($value, 40); // 40 pages at max of 2,500 recursion limit means 100k photos
              $offset = ($limit * $value) - $limit;
              $page = $value;
            }
            break;
          case 'permissionx':
            $where = $this->buildWhere($where, "permission='{$value}'");
            break;
          case 'sortBy':
            $sortBy = 'ORDER BY ' . str_replace(',', ' ', $value);
            $field = substr($value, 0, strpos($value, ','));
            $where = $this->buildWhere($where, "{$field} is not null");
            break;
          case 'tags':
            if(!is_array($value))
              $value = (array)explode(',', $value);
            $where = $this->buildWhere($where, "tags IN('" . implode("','", $value) . "')");
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
        $res = $this->db->select("SELECT * FROM `{$this->domainPhoto}` {$where} {$sortBy} LIMIT {$iterator}", $params);
        if(!$res->body->SelectResult->NextToken)
          break;

        $nextToken = $res->body->SelectResult->NextToken;
        $params['NextToken'] = $nextToken;
        $currentPage++;
      }while($currentPage < $page);
    }

    $params = array('ConsistentRead' => 'true');
    if(isset($nextToken) && !empty($nextToken))
      $params['NextToken'] = $nextToken;


    $queue = new CFBatchRequest();
    $this->db->batch($queue)->select("SELECT * FROM `{$this->domainPhoto}` {$where} {$sortBy} LIMIT {$limit}", $params);
    if(isset($params['NextToken']))
      unset($params['NextToken']);
    $this->db->batch($queue)->select("SELECT COUNT(*) FROM `{$this->domainPhoto}` {$where}", $params);
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
    * Get a tag
    * Consistent read set to false
    *
    * @param string $tag tag to be retrieved
    * @return mixed Array on success, FALSE on failure
    */
  public function getTag($tag)
  {
    $res = $this->db->select("SELECT * FROM `{$this->domainTag}` WHERE itemName()='{$tag}')", array('ConsistentRead' => 'false'));
    if(isset($res->body->SelectResult->Item))
      return self::normalizeTag($res->body->SelectResult->Item);

    return false;
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
    $res = $this->db->select("SELECT * FROM `{$this->domainTag}` WHERE `count` IS NOT NULL AND `count` > '0' AND itemName() IS NOT NULL ORDER BY itemName()", array('ConsistentRead' => 'false'));
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

  /**
    * Get the user record entry.
    *
    * @return mixed Array on success, NULL if user record is empty, FALSE on error
    */
  public function getUser()
  {
    $res = $this->db->select("SELECT * FROM `{$this->domainUser}` WHERE itemName()='1'", array('ConsistentRead' => 'true'));
    if(isset($res->body->SelectResult->Item))
      return self::normalizeUser($res->body->SelectResult->Item);
    elseif(isset($res->body->SelectResult))
      return null;
    else
      return false;
  }

  /**
    * Initialize the database by creating the domains needed.
    * This is called from the Setup controller.
    *
    * @return boolean
    */
  public function initialize()
  {
    $domains = $this->db->get_domain_list("/^{$this->domainPhoto}(Action|Credential|Group|Tag|User)?$/");
    if(count($domains) == 6)
      return true;

    $queue = new CFBatchRequest();
    $this->db->batch($queue)->create_domain($this->domainAction);
    $this->db->batch($queue)->create_domain($this->domainCredential);
    $this->db->batch($queue)->create_domain($this->domainGroup);
    $this->db->batch($queue)->create_domain($this->domainPhoto);
    $this->db->batch($queue)->create_domain($this->domainTag);
    $this->db->batch($queue)->create_domain($this->domainUser);
    $responses = $this->db->batch($queue)->send();
    return $responses->areOK();
  }

  /**
    * Allows injection of member variables.
    * Primarily used for unit testing with mock objects.
    *
    * @param string $name Name of the member variable
    * @param mixed $value Value of the member variable
    * @return void
    */
  public function inject($name, $value)
  {
    $this->$name = $value;
  }

  /**
    * Update the information for an existing credential.
    * This method overwrites existing values present in $params.
    *
    * @param string $id ID of the credential to update.
    * @param array $params Attributes to update.
    * @return boolean
    */
  public function postCredential($id, $params)
  {
    $res = $this->db->put_attributes($this->domainCredential, $id, $params, true);
    return $res->isOK();
  }

  /**
    * Update a group.
    *
    * @param string $id ID of the group to update.
    * @param array $params Attributes to update.
    * @return boolean
    */
  public function postGroup($id, $params)
  {
    $params = self::prepareGroup($id, $params);
    $res = $this->db->put_attributes($this->domainGroup, $id, $params, true);
    return $res->isOK();
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
    * Update a single tag.
    * The $params should include the tag in the `id` field.
    * [{id: tag1, count:10, longitude:12.34, latitude:56.78},...]
    *
    * @param array $params Tags and related attributes to update.
    * @return boolean
    */
  public function postTag($id, $params)
  {
    $res = $this->db->put_attributes($this->domainTag, $id, $params, true);
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
  public function postTags($params)
  {
    // TODO use batch_put_attributes instead of a queue
    $queue = new CFBatchRequest();
    foreach($params as $tagObj)
    {
      if(!isset($tagObj['id']) || empty($tagObj['id']))
        continue;
      $tag = $tagObj['id'];
      unset($tagObj['id']);
      // TODO determine if updating tags requires count to be passed. Doesn't feel like it should.
      if(!isset($tagObj['count']))
        $tagObj['count'] = 0;
      else
        $tagObj['count'] = max(0, intval($tagObj['count']));
      $this->db->batch($queue)->put_attributes($this->domainTag, $tag, $tagObj, true);
    }
    $responses = $this->db->batch($queue)->send();
    return $responses->areOK();
  }

 /**
    * Update counts for multiple tags by incrementing or decrementing.
    * The $params should include the tag and increment value as a key/value pair.
    * {tag1: 10, sunnyvale: 3}
    *
    * @param array $params Tags and related attributes to update.
    * @return boolean
    */
  public function postTagsCounter($params)
  {
    $tagsToUpdate = $tagsFromDb = array();
    foreach($params as $tag => $changeBy)
      $tagsToUpdate[$tag] = $changeBy;

    $justTags = array_keys($tagsToUpdate);

    // TODO call getTags instead
    $res = $this->db->select($sql = "SELECT * FROM `{$this->domainTag}` WHERE itemName() IN ('" . implode("','", $justTags) . "')");
    if(isset($res->body->SelectResult))
    {
      if(isset($res->body->SelectResult->Item))
      {
        foreach($res->body->SelectResult->Item as $val)
          $tagsFromDb[] = self::normalizeTag($val);
      }
    }

    // track the tags which need to be updated
    // start with ones which already exist in the database and increment them accordingly
    $updatedTags = array();
    foreach($tagsFromDb as $key => $tagFromDb)
    {
      $thisTag = $tagFromDb['id'];
      $changeBy = $tagsToUpdate[$thisTag];
      $updatedTags[] = array('id' => $thisTag, 'count' => $tagFromDb['count']+$changeBy);
      // unset so we can later loop over tags which didn't already exist
      unset($tagsToUpdate[$thisTag]);
    }
    // these are new tags
    foreach($tagsToUpdate as $tag => $count)
      $updatedTags[] = array('id' => $tag, 'count' => $count);
    return $this->postTags($updatedTags);
  }


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
    * Add a new credential to the database
    * This method does not overwrite existing values present in $params - hence "new credential".
    *
    * @param string $id ID of the credential to update which is always 1.
    * @param array $params Attributes to update.
    * @return boolean
    */
  public function putCredential($id, $params)
  {
    $res = $this->db->put_attributes($this->domainCredential, $id, $params);
    return $res->isOK();
  }

  /**
    * Alias of postGroup
    */
  public function putGroup($id, $params)
  {
    return $this->postGroup($id, $params);
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
    * Add a new tag to the database
    * This method does not overwrite existing values present in $params - hence "new user".
    *
    * @param string $id ID of the user to update which is always 1.
    * @param array $params Attributes to update.
    * @return boolean
    */
  public function putTag($id, $params)
  {
    if(!isset($params['count']))
      $params['count'] = 0;
    else
      $params['count'] = max(0, intval($params['count']));
    return $this->postTag($id, $params);
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
    * Utility function to help build the WHERE clause for SELECT statements.
    * TODO possibly put duplicate code in a utility class
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
    $action['id'] = strval($raw->Name);
    $action['appId'] = getConfig()->get('application')->appId;
    foreach($raw->Attribute as $item)
    {
      $name = (string)$item->Name;
      $value = (string)$item->Value;
      $action[$name] = $value;
    }
    return $action;
  }

  /**
    * Normalizes data from simpleDb into schema definition
    *
    * @param SimpleXMLObject $raw An action from SimpleDb in SimpleXML.
    * @return array
    */
  private function normalizeCredential($raw)
  {
    $credential = array();
    $credential['id'] = strval($raw->Name);
    foreach($raw->Attribute as $item)
    {
      $name = (string)$item->Name;
      $value = (string)$item->Value;
      if($name == 'permissions')
        $credential[$name] = (array)explode(',', $value);
      else
        $credential[$name] = $value;
    }
    return $credential;
  }

  /**
    * Normalizes data from simpleDb into schema definition
    *
    * @param SimpleXMLObject $raw An action from SimpleDb in SimpleXML.
    * @return array
    */
  private function normalizeGroup($raw)
  {
    $group = array();
    $group['id'] = strval($raw->Name);
    foreach($raw->Attribute as $item)
    {
      $name = (string)$item->Name;
      $value = (string)$item->Value;
      $group[$name] = $value;
    }
    return $group;
  }

  /**
    * Normalizes data from simpleDb into schema definition
    *
    * @param SimpleXMLObject $raw A photo from SimpleDb in SimpleXML.
    * @return array
    */
  private function normalizePhoto($raw)
  {
    $photo = array('tags' => array());
    $photo['id'] = strval($raw->Name);
    $photo['appId'] = getConfig()->get('application')->appId;
    foreach($raw->Attribute as $item)
    {
      $name = (string)$item->Name;
      $value = (string)$item->Value;
      if($name == 'tags')
      {
        if($value != '')
          $photo[$name][] = $value;
        continue;
      }

      $photo[$name] = $value;
    }

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
    $tag['id'] = strval($raw->Name);
    foreach($raw->Attribute as $item)
    {
      $name = (string)$item->Name;
      $value = (string)$item->Value;
      $tag[$name] = $value;
    }
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
    $user['id'] = strval($raw->Name);
    foreach($raw->Attribute as $item)
    {
      $name = (string)$item->Name;
      $value = (string)$item->Value;
      $user[$name] = $value;
    }
    return $user;
  }

  /**
    * Formats a group to be updated or added to the database.
    * Primarily to properly format members as an array.
    *
    * @param string $id ID of the group.
    * @param array $params Parameters for the group to be normalized.
    * @return array
    */
  private function prepareGroup($id, $params)
  {
    $params['Name'] = $id;
    if(!isset($params['members']))
      $params['members'] = array();
    elseif(!is_array($params['members']))
      $params['members'] = (array)explode(',', $params['members']);
    return $params;
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
