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
  private $config, $db, $domainAction, $domainActivity, $domainAlbum, $domainCredential, $domainPhoto, 
    $domainTag, $domainUser, $domainWebhook, $errors = array(), $owner;

  /**
    * Constructor
    *
    * @return void
    */
  public function __construct($config = null, $params = null)
  {
    $this->config = !is_null($config) ? $config : getConfig()->get();


    $utilityObj = new Utility;
    if(!is_null($params) && isset($params['db']))
      $this->db = $params['db'];
    else
      $this->db = new AmazonSDB($utilityObj->decrypt($this->config->credentials->awsKey), $utilityObj->decrypt($this->config->credentials->awsSecret));

    $this->domainPhoto = $this->config->aws->simpleDbDomain;
    $this->domainAction = $this->config->aws->simpleDbDomain.'Action';
    $this->domainActivity = $this->config->aws->simpleDbDomain.'Activity';
    $this->domainAlbum = $this->config->aws->simpleDbDomain.'Album';
    $this->domainCredential = $this->config->aws->simpleDbDomain.'Credential';
    $this->domainGroup = $this->config->aws->simpleDbDomain.'Group';
    $this->domainUser = $this->config->aws->simpleDbDomain.'User';
    $this->domainTag = $this->config->aws->simpleDbDomain.'Tag';
    $this->domainWebhook = $this->config->aws->simpleDbDomain.'Webhook';

    if(isset($this->config->user))
      $this->owner = $this->config->user->email;
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
    $this->logErrors($res);
    return $res->isOK();
  }

  /**
    * Delete an album from the database
    *
    * @param string $id ID of the action to delete
    * @return boolean
    */
  public function deleteAlbum($id)
  {
    return false;
  }

  /**
    * Delete credential
    *
    * @return boolean
    */
  public function deleteCredential($id)
  {
    $res = $this->db->delete_attributes($this->domainCredential, $id);
    $this->logErrors($res);
    return $res->isOK();
  }

  /**
    * Delete a group from the database
    *
    * @param string $id ID of the group to delete
    * @return boolean
    */
  public function deleteGroup($id)
  {
    $res = $this->db->delete_attributes($this->domainGroup, $id);
    $this->logErrors($res);
    return $res->isOK();
  }

  /**
    * Delete a photo in it's entirety from the database
    *
    * @param string $id ID of the photo to delete
    * @return boolean
    */
  public function deletePhoto($photo)
  {
    if(!isset($photo['id']))
      return false;

    $res = $this->db->delete_attributes($this->domainPhoto, $photo['id']);
    $this->logErrors($res);
    return $res->isOK();
  }

  /**
    * Delete a photos versions excluding base and original
    *
    * @param string $id ID of the photo to delete versions of
    * @return boolean
    */
  public function deletePhotoVersions($photo)
  {
    if(!isset($photo['id']))
      return false;

    $photo = $this->getPhoto($photo['id']);
    $attrs = array();
    foreach($photo as $key => $val)
    {
      if(preg_match('/^path/', $key) === 1 && !in_array($key, array('pathOriginal', 'pathBase')))
      {
        $attrs[] = array('Name' => $key);
      }
    }

    $res = $this->db->delete_attributes($this->domainPhoto, $photo['id'], $attrs);
    return $res !== false;
  }

  /**
    * Delete a tag from the database
    *
    * @param string $id ID of the tag to delete
    * @return boolean
    */
  public function deleteTag($id)
  {
    $res = $this->db->delete_attributes($this->domainTag, $id);
    $this->logErrors($res);
    return $res->isOK();
  }

  /**
    * Delete a webhook from the database
    *
    * @param string $id ID of the webhook to delete
    * @return boolean
    */
  public function deleteWebhook($id)
  {
    $res = $this->db->delete_attributes($this->domainWebhook, $id);
    $this->logErrors($res);
    return $res->isOK();
  }

  /**
    * Gets diagnostic information for debugging.
    *
    * @return array
    */
  public function diagnostics()
  {
    $diagnostics = array();
    $utilityObj = new Utility;
    $domains = array('', 'Action', 'Credential', 'Group', 'User', 'Tag', 'Webhook');
    $queue = $this->getBatchRequest();
    foreach($domains as $domain)
      $this->db->batch($queue)->domain_metadata("{$this->domainPhoto}{$domain}");
    $responses = $this->db->batch($queue)->send();
    if($responses->areOK())
    {
      $diagnostics[] = $utilityObj->diagnosticLine(true, 'All SimpleDb domains are accessible.');
    }
    else
    {
      foreach($responses as $key => $res)
      {
        if((int)$res->status !== 200)
          $diagnostics[] = $utilityObj->diagnosticLine(false, sprintf('The SimpleDb domains "%s" is NOT accessible.', $domains[$key]));
      }
    }
    return $diagnostics;
  }

  /**
    * Get a list of errors
    *
    * @return array
    */
  public function errors()
  {
    return $this->errors;
  }

  /**
    * Executes an upgrade script
    *
    * @return void
    */
  public function executeScript($file, $database)
  {
    if($database != 'simpledb')
      return;

    $status = include $file;
    return $status;
  }

  /**
    * Retrieve an action with $id
    *
    * @param string $id ID of the action to get
    * @return mixed Array on success, FALSE on failure
    */
  public function getAction($id)
  {
    $res = $this->db->select("SELECT * FROM `{$this->domainAction}` WHERE itemName()='{$id}' AND status='1'", array('ConsistentRead' => 'true'));
    $this->logErrors($res);
    if(isset($res->body->SelectResult->Item))
      return self::normalizeAction($res->body->SelectResult->Item);
    else
      return false;
  }

  /**
    * Retrieves activity
    *
    * @return mixed Array on success, FALSE on failure
    */
  public function getActivity($id)
  {
    $res = $this->db->select("SELECT * FROM `{$this->domainActivities}` WHERE itemName()='{$id}'", array('ConsistentRead' => 'true'));
    $this->logErrors($res);
    if(isset($res->body->SelectResult->Item))
      return self::normalizeActivity($res->body->SelectResult->Item);
    else
      return false;
  }

  /**
    * Retrieves activities
    *
    * @return mixed Array on success, FALSE on failure
    */
  public function getActivities($filter = array(), $limit = 10)
  {
    $res = $this->db->select("SELECT * FROM `{$this->domainActivities}`", array('ConsistentRead' => 'true'));
    $this->logErrors($res);
    if(isset($res->body->SelectResult->Item))
      return self::normalizeActivity($res->body->SelectResult->Item);
    else
      return false;
  }

  /**
    * Retrieves album
    *
    * @param string $id ID of the album to get
    * @param string $email email of viewer to determine which albums they have access to
    * @return mixed Array on success, FALSE on failure
    */
  public function getAlbum($id, $email)
  {
    return false;
  }

  /**
    * Retrieve elements for an album
    *
    * @param string $id ID of the album to get elements of
    * @return mixed Array on success, FALSE on failure
    */
  public function getAlbumElements($id)
  {
    return false;
  }

  /**
    * Retrieve albums
    *
    * @param string $email email of viewer to determine which albums they have access to
    * @return mixed Array on success, FALSE on failure
    */
  public function getAlbums($email, $limit = null, $offset = null)
  {
    return false;
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
    $this->logErrors($res);
    if(isset($res->body->SelectResult->Item))
      return self::normalizeCredential($res->body->SelectResult->Item);
    else
      return false;
  }

  /**
    * Retrieve a credential by userToken
    *
    * @param string $userToken userToken of the credential to get
    * @return mixed Array on success, FALSE on failure
    */
  public function getCredentialByUserToken($userToken)
  {
    $res = $this->db->select("SELECT * FROM `{$this->domainCredential}` WHERE userToken='{$userToken}' AND status='1'", array('ConsistentRead' => 'true'));
    $this->logErrors($res);
    if(isset($res->body->SelectResult->Item))
      return self::normalizeCredential($res->body->SelectResult->Item);
    else
      return false;
  }

  /**
    * Retrieve credentials
    *
    * @return mixed Array on success, FALSE on failure
    */
  public function getCredentials()
  {
    $res = $this->db->select("SELECT * FROM `{$this->domainCredential}` WHERE status='1'", array('ConsistentRead' => 'true'));
    $this->logErrors($res);
    if(isset($res->body->SelectResult->Item))
    {
      $credentials = array();
      foreach($res->body->SelectResult->Item as $credential)
        $credentials[] = self::normalizeCredential($credential);
      return $credentials;
    }
    else
    {
      return false;
    }
  }

  /**
    * Retrieve group from the database specified by $id
    *
    * @param string $id id of the group to return
    * @return mixed Array on success, FALSE on failure
    */
  public function getGroup($id = null)
  {
    $res = $this->db->select("SELECT * FROM `{$this->domainGroup}` WHERE itemName()='{$id}'", array('ConsistentRead' => 'true'));
    $this->logErrors($res);
    if(isset($res->body->SelectResult->Item))
      return self::normalizeGroup($res->body->SelectResult->Item);
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
      $res = $this->db->select("SELECT * FROM `{$this->domainGroup}` WHERE `name` IS NOT NULL ORDER BY `name`", array('ConsistentRead' => 'true'));
    else
      $res = $this->db->select("SELECT * FROM `{$this->domainGroup}` WHERE members in ('{$email}') AND `name` IS NOT NULL ORDER BY `name`", array('ConsistentRead' => 'true'));

    $this->logErrors($res);

    if(isset($res->body->SelectResult->Item))
    {
      $groups = array();
      foreach($res->body->SelectResult->Item as $group)
        $groups[] = self::normalizeGroup($group);
      return $groups;
    }

    return false;
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
    $this->logErrors($res);
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
  public function getPhotoNextPrevious($id, $filterOpts = null)
  {
    $buildQuery = $this->buildQuery($filterOpts, null, null);
    $photo = $this->getPhoto($id);
    if(!$photo)
      return false;

    $queue = $this->getBatchRequest();
    $this->db->batch($queue)->select("SELECT * FROM `{$this->domainPhoto}` {$buildQuery['where']} AND dateTaken>'{$photo['dateTaken']}' ORDER BY dateTaken ASC LIMIT 1");
    $this->db->batch($queue)->select("SELECT * FROM `{$this->domainPhoto}` {$buildQuery['where']} AND dateTaken<'{$photo['dateTaken']}' ORDER BY dateTaken DESC LIMIT 1");
    $responses = $this->db->batch($queue)->send();
    $this->logErrors($responses);
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
    $queue = $this->getBatchRequest();
    $this->db->batch($queue)->select("SELECT * FROM `{$this->domainPhoto}` WHERE itemName()='{$id}'", array('ConsistentRead' => 'true'));
    $this->db->batch($queue)->select("SELECT * FROM `{$this->domainAction}` WHERE targetType='photo' AND targetId='{$id}'", array('ConsistentRead' => 'true'));
    $responses = $this->db->batch($queue)->send();
    $this->logErrors($responses);
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
    * @param int $limit Total results to return
    * @param offset $offset Starting point of results to return
    * @return mixed Array on success, FALSE on failure
    */
  public function getPhotos($filters = array(), $limit = 20, $offset = null)
  {
    $buildQuery = $this->buildQuery($filters, $limit, $offset);
    $queue = $this->getBatchRequest();
    $this->db->batch($queue)->select("SELECT * FROM `{$this->domainPhoto}` {$buildQuery['where']} {$buildQuery['sortBy']} LIMIT {$buildQuery['limit']}", $buildQuery['params']);
    if(isset($buildQuery['params']['NextToken']))
      unset($buildQuery['params']['NextToken']);
    $this->db->batch($queue)->select("SELECT COUNT(*) FROM `{$this->domainPhoto}` {$buildQuery['where']}", $buildQuery['params']);
    $responses = $this->db->batch($queue)->send();

    $this->logErrors($responses);
    if(!$responses->areOK())
      return false;

    $photos = array();
    foreach($responses[0]->body->SelectResult->Item as $photo)
      $photos[] = $this->normalizePhoto($photo);

    if(!empty($photos))
      $photos[0]['totalRows'] = intval($responses[1]->body->SelectResult->Item->Attribute->Value);

    return $photos;
  }

  /**
    * Get a resource map
    * NOT IMPLEMENTED
    *
    * @param string $id tag to be retrieved
    * @return mixed Array on success, FALSE on failure
    */
  public function getResourceMap($id)
  {
    return false;
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
    $this->logErrors($res);
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
    $countField = 'countPublic';
    $sortBy = 'itemName()';
    if(isset($filters['sortBy']))
      $sortBy = str_replace(',', ' ', $sortBy);
    if(isset($filter['permission']) && $filter['permission'] == 0)
      $countField = 'countPrivate';


    if(isset($filter['search']) && $filter['search'] != '')
    {
      $query = "SELECT * FROM `{$this->domainTag}` WHERE `{$countField}` IS NOT NULL AND `{$countField}` > '0' AND itemName() IS NOT NULL AND itemName() LIKE '{$filter['search']}%' ORDER BY {$sortBy})";
      $params[':search'] = "{$filter['search']}%";
    }
    else
    {
      $query = "SELECT * FROM `{$this->domainTag}` WHERE `{$countField}` IS NOT NULL AND `{$countField}` > '0' AND itemName() IS NOT NULL ORDER BY {$sortBy}";
    }

    $res = $this->db->select($query, array('ConsistentRead' => 'false'));
    $this->logErrors($res);

    if(!$res->isOK())
      return false;

    $tags = array();
    if(isset($res->body->SelectResult))
    {
      if(isset($res->body->SelectResult->Item))
      {
        foreach($res->body->SelectResult->Item as $val)
          $tags[] = self::normalizeTag($val);
      }
    }
    return $tags;
  }

  /**
    * Get the user record entry.
    *
    * @return mixed Array on success, NULL if user record is empty, FALSE on error
    */
  public function getUser($owner = null)
  {
    if($owner === null)
      $owner = $this->owner;

    $res = $this->db->select("SELECT * FROM `{$this->domainUser}` WHERE itemName()='{$owner}'", array('ConsistentRead' => 'true'));
    $this->logErrors($res);
    if(isset($res->body->SelectResult->Item))
      return self::normalizeUser($res->body->SelectResult->Item);
    elseif(isset($res->body->SelectResult))
      return null;
    else
      return false;
  }

  /**
    * Get the user record entry by username and password.
    *
    * @return mixed Array on success, otherwise FALSE
    */
  public function getUserByEmailAndPassword($email = null, $password = null)
  {
    if($email == '' || $password == '')
      return false;;

    $res = $this->db->select("SELECT * FROM `{$this->domainUser}` WHERE itemName()='{$email}' AND `password`='{$password}", array('ConsistentRead' => 'true'));
    $this->logErrors($res);
    if(isset($res->body->SelectResult->Item))
      return self::normalizeUser($res->body->SelectResult->Item);
    else
      return false;
  }

  /**
    * Get a webhook specified by $id
    *
    * @param string $id ID of the webhook to retrieve
    * @return mixed Array on success, FALSE on failure
    */
  public function getWebhook($id)
  {
    $res = $this->db->select("SELECT * FROM `{$this->domainWebhook}` WHERE itemName()='{$id}'", array('ConsistentRead' => 'true'));
    $this->logErrors($res);
    if(isset($res->body->SelectResult->Item))
      return self::normalizeWebhook($res->body->SelectResult->Item);
    else
      return false;
  }

  /**
    * Get all webhooks for a user
    *
    * @return mixed Array on success, FALSE on failure
    */
  public function getWebhooks($topic = null)
  {
    if($topic)
      $res = $this->db->select("SELECT * FROM `{$this->domainWebhook}` WHERE topic='{$topic}'", array('ConsistentRead' => 'true'));
    else
      $res = $this->db->select("SELECT * FROM `{$this->domainWebhook}`", array('ConsistentRead' => 'true'));

    $this->logErrors($res);
    if(!$res->isOK())
      return false;


    if(isset($res->body->SelectResult))
    {
      if(isset($res->body->SelectResult->Item))
      {
        $webhooks = array();
        foreach($res->body->SelectResult->Item as $webhook)
          $webhooks[] = $this->normalizeWebhook($webhook);

        return $webhooks;
      }

      return null;
    }
    return false;
  }

  /**
    * Initialize the database by creating the domains needed.
    * This is called from the Setup controller.
    *
    * @return boolean
    */
  public function initialize($isEditMode)
  {
    if($this->version() !== '0.0.0' && $isEditMode === false)
      return true;

    // simpledb-base.php sets $status
    $status = $this->executeScript(sprintf('%s/upgrade/db/simpledb/simpledb-base.php', $this->config->paths->configs), 'simpledb');
    return $status;
  }

  /**
    * Identification method to return array of strings.
    *
    * @return array
    */
  public function identity()
  {
    return array('simpledb');
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
    * Add an album
    *
    * @param string $albumId ID of the album to update.
    * @param string $type Type of element
    * @param array $elementIds IDs of the elements to update.
    * @return boolean
    */
  public function postAlbum($id, $params)
  {
    return false;
  }

  /**
    * Add an element to an album
    *
    * @param string $albumId ID of the album to update.
    * @param string $type Type of element
    * @param array $elementIds IDs of the elements to update.
    * @return boolean
    */
  public function postAlbumAdd($albumId, $type, $elementIds)
  {
    return false;
  }

  /**
    * Remove an element from an album
    *
    * @param string $albumId ID of the album to update.
    * @param string $type Type of element
    * @param array $elementIds IDs of the elements to update.
    * @return boolean
    */
  public function postAlbumRemove($albumId, $type, $elementIds)
  {
    return false;
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
    $this->logErrors($res);
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
    $this->logErrors($res);
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
    if(empty($id))
      return false;
    elseif(empty($params))
      return true;

    $params = self::preparePhoto($id, $params);
    $res = $this->db->put_attributes($this->domainPhoto, $id, $params, true);
    $this->logErrors($res);
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
    $this->logErrors($res);
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
    $queue = $this->getBatchRequest();
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
    $this->logErrors($responses);
    return $responses->areOK();
  }

  /**
    * Update the information for the user record.
    * This method overwrites existing values present in $params.
    *
    * @param string $id ID of the user to update which is always 1.
    * @param array $params Attributes to update.
    * @return boolean
    */
  public function postUser($params)
  {
    // make sure we don't overwrite an existing user record
    if(isset($params['password']) && empty($params['password']))
      unset($params['password']);
    $res = $this->db->put_attributes($this->domainUser, $this->owner, $params, true);
    $this->logErrors($res);
    return $res->isOK();
  }

  /**
    * Update the information for the webhook record.
    *
    * @param string $id ID of the webhook to update which is always 1.
    * @param array $params Attributes to update.
    * @return boolean
    */
  public function postWebhook($id, $params)
  {
    // make sure we don't overwrite an existing user record
    $res = $this->db->put_attributes($this->domainWebhook, $id, $params, true);
    $this->logErrors($res);
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
    $this->logErrors($res);
    return $res->isOK();
  }

  /**
    * Add a new album to the database
    * This method does not overwrite existing values present in $params - hence "new action".
    *
    * @param string $id ID of the action to update which is always 1.
    * @param array $params Attributes to update.
    * @return boolean
    */
  public function putAlbum($id, $params)
  {
    return false;
  }

  /**
    * Add a new activity to the database
    * This method does not overwrite existing values present in $params - hence "new action".
    *
    * @param string $id ID of the action to update which is always 1.
    * @param array $params Attributes to update.
    * @return boolean
    */
  public function putActivity($id, $params)
  {
    $res = $this->db->put_attributes($this->domainActivity, $id, $params);
    $this->logErrors($res);
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
    $this->logErrors($res);
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
    $params = self::preparePhoto($id, $params);
    $res = $this->db->put_attributes($this->domainPhoto, $id, $params);
    $this->logErrors($res);
    return $res->isOK();
  }

  /**
    * Create a resource map
    * NOT IMPLEMENTED
    *
    * @param string $id resource map to be retrieved
    * @param array $params Attributes to create
    * @return mixed Array on success, FALSE on failure
    */
  public function putResourceMap($id, $params)
  {
    return false;
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
  public function putUser($params)
  {
    if(isset($params['password']) && empty($params['password']))
      unset($params['password']);
    $res = $this->db->put_attributes($this->domainUser, $this->owner, $params);
    $this->logErrors($res);
    return $res->isOK();
  }

  /**
    * Add a new webhook to the database
    *
    * @param string $id ID of the webhook to update which is always 1.
    * @param array $params Attributes to update.
    * @return boolean
    */
  public function putWebhook($id, $params)
  {
    return $this->postWebhook($id, $params);
  }

  /**
    * Get the current database version
    *
    * @return string Version number
    */
  public function version()
  {
    $user = $this->getUser();
    if(!$user || !isset($user['version']))
      return '0.0.0';

    return $user['version'];
  }

  /**
    * Query builder for searching photos.
    *
    * @param array $filters Filters to be applied before obtaining the result
    * @param int $limit Total results to return
    * @param offset $offset Starting point of results to return
    * @return array Components required to build the query
    */
  private function buildQuery($filters, $limit, $offset)
  {
    // TODO: support logic for multiple conditions
    $where = '';
    $sortBy = null;
    if(!empty($filters) && is_array($filters))
    {
      foreach($filters as $name => $value)
      {
        switch($name)
        {
          case 'hash':
            $where = $this->buildWhere($where, "hash='{$value}')");
            break;
          case 'groups':
            $where = $this->buildWhere($where, "(groups IN('" . implode("','", $value) . "') OR permission='1')");
            break;
          case 'page':
            if($value > 1)
            {
              $value = min($value, 40); // 40 pages at max of 2,500 recursion limit means 100k photos
              $offset = ($limit * $value) - $limit;
              $page = $value;
            }
            break;
          case 'permission':
            $where = $this->buildWhere($where, "permission='1'");
            break;
          case 'sortBy':
            $sortBy = 'ORDER BY ' . str_replace(',', ' ', $value);
            $field = substr($value, 0, strpos($value, ','));
            $where = $this->buildWhere($where, "{$field} IS NOT NULL");
            break;
          case 'tags':
            if(!is_array($value))
              $value = (array)explode(',', $value);
            $where = $this->buildWhere($where, "tags IN('" . implode("','", $value) . "')");
            break;
          case 'type': // type for activities
            $value = $this->_($value);
            $where = $this->buildWhere($where, "type='{$value}'");
            break;
        }
      }
    }

    if(!empty($offset))
    {
      $iterator = max(1, intval($offset - 1));
      $nextToken = null;
      $params = array('ConsistentRead' => 'true');
      if(!isset($page))
        $page = 1;
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

    return array('params' => $params, 'where' => $where, 'sortBy' => $sortBy, 'limit' => $limit);
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
      return "WHERE {$add} ";
    else
      return "{$existing} AND {$add} ";
  }

  private function logErrors($res)
  {
    if($res instanceof CFArray)
    {
      foreach($res as $r)
        $this->logErrors($r);
    }
    else
    {
      if(!$res->isOK())
      {
        foreach($res->body->Errors as $error)
        {
          $message = $this->errors[] = sprintf('Amazon Web Services error (code %s): %s', $error->Error->Code, $error->Error->Message);
          getLogger()->crit($message);
        }
      }
    }
  }

  /**
    * Gets a CFBatchRequest object for the AWS library
    *
    * @return object
   */
  public function getBatchRequest()
  {
    return new CFBatchRequest();
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
    $action['appId'] = $this->config->application->appId;
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
  private function normalizeActivity($raw)
  {
    $activity = array();
    $activity['id'] = strval($raw->Name);
    $activity['appId'] = $this->config->application->appId;
    foreach($raw->Attribute as $item)
    {
      $name = (string)$item->Name;
      $value = (string)$item->Value;
      $activity[$name] = $value;
    }
    return $activity;
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
      if($name == 'members')
      {
        if($value != '')
          $group[$name][] = $value;
        continue;
      }
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
    $photo['appId'] = $this->config->application->appId;
    foreach($raw->Attribute as $item)
    {
      $name = (string)$item->Name;
      $value = (string)$item->Value;
      if($name == 'tags' || $name == 'groups')
      {
        if($value != '')
          $photo[$name][] = $value;
        continue;
      }

      $photo[$name] = $value;
    }

    // we have to do this because natcasesort preserves array keys and turns it into an object literal (not an array literal)
    if(isset($photo['tags']))
    {
      $tags = $photo['tags'];
      natcasesort($tags);
      $photo['tags'] = array();
      foreach($tags as $tag)
        $photo['tags'][] = $tag;
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
    * Normalizes data from simpleDb into schema definition
    *
    * @param SimpleXMLObject $raw A webhook from SimpleDb in SimpleXML.
    * @return array
    */
  private function normalizeWebhook($raw)
  {
    $webhook = array();
    $webhook['id'] = strval($raw->Name);
    foreach($raw->Attribute as $item)
    {
      $name = (string)$item->Name;
      $value = (string)$item->Value;
      $webhook[$name] = $value;
    }
    return $webhook;
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
    foreach(array('groups','tags') as $val)
    {
      if(!isset($params[$val]))
        $params[$val] = array();
      elseif(!is_array($params[$val]))
        $params[$val] = (array)explode(',', $params[$val]);
    }
    return $params;
  }
}
