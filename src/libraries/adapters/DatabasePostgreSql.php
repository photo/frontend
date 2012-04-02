<?php
/**
 * PostgreSQL implementation
 *
 * This class defines the functionality defined by DatabaseInterface for a MySQL database.
 * It use EpiDatabase.
 * @author Hub Figuiere <hub@figuiere.net>
 */
class DatabasePostgreSql implements DatabaseInterface
{
  /**
    * Member variables holding the names to the SimpleDb domains needed and the database object itself.
    * @access private
    */
  private $config, $errors = array(), $owner, $postgreSqlDb, $postgreSqlHost, $postgreSqlUser, $postgreSqlPassword, $postgreSqlTablePrefix;

  /**
    * Constructor
    *
    * @return void
    */
  public function __construct($config = null, $params = null)
  {
    $this->config = !is_null($config) ? $config : getConfig()->get();
    $postgresql = $this->config->postgresql;

    if(!is_null($params) && isset($params['db']))
    {
      getLogger()->info("Here");
      $this->db = $params['db'];
    }
    else
    {
      $utilityObj = new Utility;
      EpiDatabase::employ('pgsql', $postgresql->postgreSqlDb,
                          $postgresql->postgreSqlHost, $postgresql->postgreSqlUser, $utilityObj->decrypt($postgresql->postgreSqlPassword));
      $this->db = getDatabase();
      #$this->db->execute("SET NAMES 'utf8'");
      getLogger()->info("There");
    }

    foreach($postgresql as $key => $value)
    {
      $this->{$key} = $value;
    }

    if(isset($this->config->user))
      $this->owner = $this->config->user->email;
  }

  /**
    * Delete an action from the database
    *
    * @param string $id ID of the action to delete
    * @return boolean
    */
  public function deleteAction($id)
  {
    $res = $this->db->execute("DELETE FROM {$this->postgreSqlTablePrefix}action WHERE idx=:id AND owner=:owner", array(':id' => $id, ':owner' => $this->owner));
    return ($res !== false);
  }

  /**
    * Delete credential
    *
    * @return boolean
    */
  public function deleteCredential($id)
  {
    $res = $this->db->execute("DELETE FROM {$this->postgreSqlTablePrefix}credential WHERE idx=:id AND owner=:owner", array(':id' => $id, ':owner' => $this->owner));
    return ($res !== false);
  }

  /**
    * Delete a group from the database
    *
    * @param string $id ID of the group to delete
    * @return boolean
    */
  public function deleteGroup($id)
  {
    $res = $this->db->execute("DELETE FROM {$this->postgreSqlTablePrefix}group WHERE idx=:id AND owner=:owner", array(':id' => $id, ':owner' => $this->owner));
    return ($res !== false);
  }

  /**
    * Delete a photo from the database
    *
    * @param string $id ID of the photo to delete
    * @return boolean
    */
  public function deletePhoto($photo)
  {
    if(!isset($photo['id']))
      return false;

    $res = $this->db->execute("DELETE FROM {$this->postgreSqlTablePrefix}photo WHERE idx=:id AND owner=:owner", array(':id' => $photo['id'], ':owner' => $this->owner));
    // TODO delete all versions
    return ($res !== false);
  }

  /**
    * Delete a tag from the database
    *
    * @param string $id ID of the tag to delete
    * @return boolean
    */
  public function deleteTag($id)
  {
    $resDel = $this->db->execute("DELETE FROM {$this->postgreSqlTablePrefix}tag WHERE idx=:id AND owner=:owner", array(':id' => $id, ':owner' => $this->owner));
    $resClean = $this->db->execute("DELETE FROM {$this->postgreSqlTablePrefix}elementtag WHERE owner=:owner AND tag=:tag", array(':owner' => $this->owner, ':tag' => $id));
    return ($resDel !== false);
  }

  /**
    * Delete a webhook from the database
    *
    * @param string $id ID of the webhook to delete
    * @return boolean
    */
  public function deleteWebhook($id)
  {
    $res = $this->db->execute("DELETE FROM {$this->postgreSqlTablePrefix}webhook WHERE id=:id AND owner=:owner", array(':id' => $id, ':owner' => $this->owner));
    return ($res !== false);
  }

  /**
    * Gets diagnostic information for debugging.
    *
    * @return array
    */
  public function diagnostics()
  {
    $utilityObj = new Utility;
    $diagnostics = array();
    $res = $this->db->execute("SELECT * FROM {$this->postgreSqlTablePrefix}photo WHERE owner=:owner LIMIT 1", array(':owner' => $this->owner));
    if($res == 1)
      $diagnostics[] = $utilityObj->diagnosticLine(true, 'Database connectivity is okay.');
    else
      $diagnostics[] = $utilityObj->diagnosticLine(false, 'Could not properly connect to the database.');

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
    if($database != 'postgresql')
      return;

    return include $file;
  }

  /**
    * Retrieve an action with $id
    *
    * @param string $id ID of the action to get
    * @return mixed Array on success, FALSE on failure
    */
  public function getAction($id)
  {
    $action = $this->db->one("SELECT * FROM {$this->postgreSqlTablePrefix}action WHERE idx=:id AND owner=:owner",
                               array(':id' => $id, ':owner' => $this->owner));
    if(empty($action))
      return false;
    
    return $this->normalizeAction($action);
  }

  /**
    * Retrieves activities
    *
    * @return mixed Array on success, FALSE on failure
    */
  public function getActivities()
  {
    $activities = $this->db->all("SELECT * FROM {$this->postgreSqlTablePrefix}activity WHERE owner=:owner",
                               array(':owner' => $this->owner));
    if($activities === false)
      return false;

    foreach($activities as $key => $activity)
      $activities[$key] = $this->normalizeActivity($activity);

    return $activities;
  }

  /**
    * Retrieves activity
    *
    * @param string $id ID of the activity to get
    * @return mixed Array on success, FALSE on failure
    */
  public function getActivity($id)
  {
    $activity = $this->db->all("SELECT * FROM {$this->postgreSqlTablePrefix}activity WHERE idx=:id AND owner=:owner",
                               array(':id' => $id, ':owner' => $this->owner));
    if($activity === false)
      return false;

    $activity = $this->normalizeActivity($activity);

    return $activity;
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
    if($this->owner === $email)
    {
      $album = $this->db->one("SELECT * FROM {$this->postgreSqlTablePrefix}album WHERE idx=:id AND owner=:owner",
        array(':id' => $id, ':owner' => $this->owner));
    }
    else
    {
      $groups = $this->getGroups($email);
      if($groups === false)
        return false;

      $groupIds = array();
      foreach($groups as $grp)
        $groupIds[] = $this->_($grp['id']);

      $groupIds = implode("','", $groupIds);
      $album = $this->db->one("SELECT alb.* FROM {$this->postgreSqlTablePrefix}album AS alb INNER JOIN {$this->postgreSqlTablePrefix}elementGroup AS grp
        ON alb.id=grp.element AND grp.type='album' WHERE alb.idx=:id AND alb.owner=:owner AND (alb.permission='1' OR alb.id IN ('{$groupIds}'))",
                                 array(':id' => $id, ':owner' => $this->owner));
    }

    if($album === false)
      return false;

    $album = $this->normalizeAlbum($album);
    return $album;
  }

  /**
    * Retrieve elements for an album
    *
    * @param string $id ID of the album to get elements of
    * @return mixed Array on success, FALSE on failure
    */
  public function getAlbumElements($id)
  {
    $photos = $this->db->all("SELECT pht.* 
      FROM {$this->postgreSqlTablePrefix}photo AS pht INNER JOIN {$this->postgreSqlTablePrefix}elementalbum AS alb ON pht.id=alb.element
      WHERE pht.owner=:owner AND alb.owner=:owner AND alb.type=:type",
      array(':owner' => $this->owner, ':type' => 'photo'));

    if($photos === false)
      return false;

    foreach($photos as $key => $photo)
      $photos[$key] = $this->normalizePhoto($photo);
    return $photos;
  }

  /**
    * Retrieve albums
    *
    * @param string $email email of viewer to determine which albums they have access to
    * @return mixed Array on success, FALSE on failure
    */
  public function getAlbums($email)
  {
    if($this->owner === $email)
    {
      $albums = $this->db->all("SELECT * FROM {$this->postgreSqlTablePrefix}album WHERE owner=:owner", array(':owner' => $this->owner));
    }
    else
    {
      $groups = $this->getGroups($email);
      if($groups === false)
        return false;

      $groupIds = array();
      foreach($groups as $grp)
        $groupIds[] = $this->_($grp['id']);

      $groupIds = implode("','", $groupIds);
      $albums = $this->db->all("SELECT * FROM {$this->postgreSqlTablePrefix}album AS alb INNER JOIN {$this->postgreSqlTablePrefix}elementGroup AS grp
        ON alb.id=grp.element AND grp.type='album' WHERE alb.owner=:owner AND (alb.permission='1' OR alb.id IN ('{$groupIds}'))",
                                 array(':owner' => $this->owner));
    }

    if(empty($albums))
      return false;

    foreach($albums as $key => $album)
      $albums[$key] = $this->normalizeAlbum($album);
    
    return $albums;
  }

  /**
    * Retrieve a credential with $id
    *
    * @param string $id ID of the credential to get
    * @return mixed Array on success, FALSE on failure
    */
  public function getCredential($id)
  {
    $cred = $this->db->one("SELECT * FROM {$this->postgreSqlTablePrefix}credential WHERE idx=:id AND owner=:owner",
                               array(':id' => $id, ':owner' => $this->owner));
    if(empty($cred))
    {
      return false;
    }
    return $this->normalizeCredential($cred);
  }

  /**
    * Retrieve a credential by userToken
    *
    * @param string $userToken userToken of the credential to get
    * @return mixed Array on success, FALSE on failure
    */
  public function getCredentialByUserToken($userToken)
  {
    $cred = $this->db->one("SELECT * FROM {$this->postgreSqlTablePrefix}credential WHERE userToken=:userToken AND owner=:owner",
                               array(':userToken' => $userToken, ':owner' => $this->owner));
    if(empty($cred))
    {
      return false;
    }
    return $this->normalizeCredential($cred);
  }

  /**
    * Retrieve credentials
    *
    * @return mixed Array on success, FALSE on failure
    */
  public function getCredentials()
  {
    $res = $this->db->all("SELECT * FROM {$this->postgreSqlTablePrefix}credential WHERE owner=:owner AND status=1", array(':owner' => $this->owner));
    if($res === false)
    {
      return false;
    }
    $credentials = array();
    if(!empty($res))
    {
      foreach($res as $cred)
      {
        $credentials[] = $this->normalizeCredential($cred);
      }
    }
    return $credentials;
  }

  /**
    * Retrieve group from the database specified by $id
    *
    * @param string $id id of the group to return
    * @return mixed Array on success, FALSE on failure
    */
  public function getGroup($id = null)
  {
    $res = $this->db->all("SELECT grp.*, memb.* FROM {$this->postgreSqlTablePrefix}group AS grp INNER JOIN {$this->postgreSqlTablePrefix}groupmember AS memb ON grp.owner=memb.owner WHERE grp.idx=:id AND grp.owner=:owner", array(':id' => $id ,':owner' => $this->owner));
    if($res === false || empty($res))
      return false;

    $group = array('id' => $res[0]['id'], 'owner' => $res[0]['owner'], 'name' => $res[0]['name'], 'permission' => $res[0]['permission'], 'members' => array());
    foreach($res as $r)
      $group['members'][] = $r['email'];

    return $this->normalizeGroup($group);
  }

  /**
    * Retrieve groups from the database optionally filter by member (email)
    *
    * @param string $email email address to filter by
    * @return mixed Array on success, FALSE on failure
    */
  public function getGroups($email = null)
  {

    if(empty($email))
      $res = $this->db->all("SELECT grp.*, memb.email 
        FROM {$this->postgreSqlTablePrefix}groupname AS grp 
        INNER JOIN {$this->postgreSqlTablePrefix}groupmember AS memb ON grp.owner=memb.owner AND grp.idx=memb.group 
        WHERE grp.idx IS NOT NULL AND grp.owner=:owner 
        ORDER BY grp.name", array(':owner' => $this->owner));
    else
      $res = $this->db->all("SELECT grp.*, memb.email 
        FROM {$this->postgreSqlTablePrefix}groupname AS grp 
        INNER JOIN {$this->postgreSqlTablePrefix}groupmember AS memb ON grp.owner=memb.owner AND grp.idx=memb.group 
        WHERE memb.email=:email AND grp.idx IS NOT NULL AND grp.owner=:owner 
        ORDER BY grp.name", array(':email' => $email, ':owner' => $this->owner));

    if($res !== false)
    {
      $groups = array();
      if(!empty($res))
      {
        $tempGroups = array();
        foreach($res as $group)
        {
          if(!isset($tempGroups[$group['id']]))
            $tempGroups[$group['id']] = array('id' => $group['id'], 'name' => $group['name'], 'owner' => $group['owner'], 'permission' => $group['permission'], 'members' => array());
          $tempGroups[$group['id']]['members'][] = $group['email'];
        }
        foreach($tempGroups as $g)
          $groups[] = $this->normalizeGroup($g);
      }
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
    $photo = $this->db->one("SELECT * FROM {$this->postgreSqlTablePrefix}photo WHERE idx=:id AND owner=:owner", array(':id' => $id, ':owner' => $this->owner));
    if(empty($photo))
      return false;
    return $this->normalizePhoto($photo);
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

    $sortKey = 'datetaken';
    if(isset($filterOpts['sortBy'])) {
        $sortOptions = (array)explode(',', $filterOpts['sortBy']);
        if(!empty($sortOptions)) {
            $sortKey = $sortOptions[0];
        }
    }
    // owner is in buildQuery
    $photo_prev = $this->db->one("SELECT {$this->postgreSqlTablePrefix}photo {$buildQuery['from']} {$buildQuery['where']} AND {$sortKey}> :{$sortKey} AND {$sortKey} IS NOT NULL {$buildQuery['groupBy']} ORDER BY {$sortKey} ASC LIMIT 1", array(":{$sortKey}" => $photo[$sortKey]));
    $photo_next = $this->db->one("SELECT {$this->postgreSqlTablePrefix}photo {$buildQuery['from']} {$buildQuery['where']} AND {$sortKey}< :{$sortKey} AND {$sortKey} IS NOT NULL {$buildQuery['groupBy']} ORDER BY {$sortKey} DESC LIMIT 1", array(":{$sortKey}" => $photo[$sortKey]));

    $ret = array();
    if($photo_prev)
      $ret['previous'] = $this->normalizePhoto($photo_prev);
    if($photo_next)
      $ret['next'] = $this->normalizePhoto($photo_next);

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
    $photo = $this->getPhoto($id);
    $photo['actions'] = array();
    if($photo)
    {
      $actions = $this->db->all("SELECT * FROM {$this->postgreSqlTablePrefix}action WHERE owner=:owner AND targetType='photo' AND targetId=:id",
      	       array(':id' => $id, ':owner' => $this->owner));
      if(!empty($actions))
      {
        foreach($actions as $action)
           $photo['actions'][] = $this->normalizeAction($action);
      }
    }
    return $photo;
  }

  /**
    * Get a list of a user's photos filtered by $filter, $limit and $offset
    *
    * @param array $filters Filters to be applied before obtaining the result
    * @return mixed Array on success, FALSE on failure
    */
  public function getPhotos($filters = array(), $limit = 20, $offset = null)
  {
    $query = $this->buildQuery($filters, $limit, $offset);

    // buildQuery includes owner
    $photos = $this->db->all($sql = "SELECT {$this->postgreSqlTablePrefix}photo.* {$query['from']} {$query['where']} {$query['groupBy']} {$query['sortBy']} {$query['limit']} {$query['offset']}");
    if($photos === false)
      return false;

    for($i = 0; $i < count($photos); $i++)
      $photos[$i] = $this->normalizePhoto($photos[$i]);

    // TODO evaluate SQL_CALC_FOUND_ROWS (indexes with the query builder might be hard to optimize)
    // http://www.mysqlperformanceblog.com/2007/08/28/to-sql_calc_found_rows-or-not-to-sql_calc_found_rows/
    $result = $this->db->one("SELECT COUNT(*) {$query['from']} {$query['where']} {$query['groupBy']}");
    if(!empty($result))
      $photos[0]['totalRows'] = intval($result['count']);

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
    $tag = $this->db->one('SELECT * FROM {$this->postgreSqlTablePrefix}tag WHERE idx=:id AND owner=:owner', array(':id' => $tag));
    // TODO this should be in the normalize method
    if($tag['params'])
      $tag = array_merge($tag, json_decode($tag['params'], 1));
    unset($tag['params']);
    return $tag;
  }

  /**
    * Get tags filtered by $filter
    * Consistent read set to false
    *
    * @param array $filters Filters to be applied to the list
    * @return mixed Array on success, FALSE on failure
    */
  public function getTags($filter = array())
  {
    $countField = 'countpublic';
    $params = array(':owner' => $this->owner);

    if(isset($filter['permission']) && $filter['permission'] == 0)
      $countField = 'countprivate';

    if(isset($filter['search']) && $filter['search'] != '')
    {
      $query = "SELECT * FROM {$this->postgreSqlTablePrefix}tag WHERE idx IS NOT NULL AND owner=:owner AND {$countField} IS NOT NULL AND {$countField} > '0' AND idx LIKE :search ORDER BY idx";
      $params[':search'] = "{$filter['search']}%";
    }
    else
    {
      $query = "SELECT * FROM {$this->postgreSqlTablePrefix}tag WHERE idx IS NOT NULL AND owner=:owner AND {$countField} IS NOT NULL AND {$countField} > '0' ORDER BY idx";
    }

    $tags = $this->db->all($query, $params);

    if($tags === false)
      return false;

    foreach($tags as $key => $tag)
    {
      // TODO this should be in the normalize method
      if($tag['extra'])
        $tags[$key] = array_merge($tag, json_decode($tag['extra'], 1));
      unset($tags[$key]['params']);

      $tags[$key]['id'] = $tags[$key]['idx'];
      $tags[$key]['countPublic'] = $tags[$key]['countpublic'];
      $tags[$key]['countPrivate'] = $tag[$key]['countprivate'];

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
    $res = $this->db->one($sql = "SELECT * FROM {$this->postgreSqlTablePrefix}username WHERE idx=:owner", array(':owner' => $owner));
    if($res)
    {
      return $this->normalizeUser($res);
    }
    return null;
  }

  /**
    * Get a webhook specified by $id
    *
    * @param string $id ID of the webhook to retrieve
    * @return mixed Array on success, FALSE on failure
    */
  public function getWebhook($id)
  {
    $webhook = $this->db->one("SELECT * FROM {$this->postgreSqlTablePrefix}webhook WHERE idx=:id AND owner=:owner", array(':id' => $id, ':owner' => $this->owner));
    if(empty($webhook))
      return false;
    return $this->normalizeWebhook($webhook);
  }

  /**
    * Get all webhooks for a user
    *
    * @return mixed Array on success, FALSE on failure
    */
  public function getWebhooks($topic = null)
  {
    if($topic)
      $res = $this->db->all("SELECT * FROM {$this->postgreSqlTablePrefix}webhook WHERE owner=:owner AND topic='{$topic}'", array(':owner' => $this->owner));
    else
      $res = $this->db->all("SELECT * FROM {$this->postgreSqlTablePrefix}webhook WHERE owner=:owner", array(':owner' => $this->owner));

    if($res === false)
      return false;
    if(empty($res))
      return null;

    $webhooks = array();
    foreach($res as $webhook)
    {
      $webhooks[] = $this->normalizeWebhook($webhook);
    }
    return $webhooks;
  }

  /**
    * Identification method to return array of strings.
    *
    * @return array
    */
  public function identity()
  {
    return array('postgresql');
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
    * Initialize the database by creating the database and tables needed.
    * This is called from the Setup controller.
    *
    * @return boolean
    */
  public function initialize($isEditMode)
  {
    $version = $this->version();
    // we're not running setup for the first time and we're not in edit mode
    if($version !== '0.0.0' && $isEditMode === false)
    {
      // email address has to be unique
      // getting a null back from getUser() means we can proceed
      $user = true;
      if($this->owner != '')
        $user = $this->getUser($this->owner);

      // getUser returns null if the user does not exist
      if($user === null)
        return true;

      getLogger()->crit(sprintf('Could not initialize user for PostgreSql due to email conflict (%s).', $this->owner));
      return false;
    }
    elseif($version === '0.0.0')
    {
      try
      {
        return $this->executeScript(sprintf('%s/upgrade/db/postgresql/postgresql-base.php', getConfig()->get('paths')->configs), 'postgresql');
      }
      catch(EpiDatabaseException $e)
      {
        getLogger()->crit($e->getMessage());
        return false;
      }
    }
    return true;
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
    $res = true;
    foreach($elementIds as $elementId)
    {
      $res = $res && $this->db->execute("REPLACE INTO {$this->postgreSqlTablePrefix}elementAlbum(owner,type,element,album) VALUES(:owner,:type,:elementId,:albumId)",
        array(':owner' => $this->owner, ':type' => $type, ':elementId' => $elementId, ':albumId' => $albumId));
    }
    return $res !== false;
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
    $res = true;
    foreach($elementIds as $elementId)
    {
      $res = $res && $this->db->execute("DELETE FROM {$this->postgreSqlTablePrefix}elementalbum WHERE owner=:owner AND element=:elementid AND album=:albumid AND tagtype=:type",
        array(':owner' => $this->owner, ':elementId' => $elementId, ':albumId' => $albumId, ':type' => $type));
    }
    return $res !== false;
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
    $params = $this->prepareCredential($params);
    $bindings = array();
    if(isset($params['::bindings']))
    {
      $bindings = $params['::bindings'];
    }
    $stmt = $this->sqlUpdateExplode($params, $bindings);
    $bindings[':id'] = $id;
    $bindings[':owner'] = $this->owner;

    $result = $this->db->execute("UPDATE {$this->postgreSqlTablePrefix}credential SET {$stmt} WHERE idx=:id AND owner=:owner", $bindings);

    return ($result !== false);
  }

  /**
    * Update the information for an existing credential.
    * This method overwrites existing values present in $params.
    *
    * @param string $id ID of the credential to update.
    * @param array $params Attributes to update.
    * @return boolean
    */
  public function postGroup($id, $params)
  {
    $members = false;
    if(isset($params['members']))
    {
      $members = !empty($params['members']) ? (array)explode(',', $params['members']) : null;
      unset($params['members']);
    }
    $params = $this->prepareGroup($id, $params);
    $bindings = array();
    if(isset($params['::bindings']))
    {
      $bindings = $params['::bindings'];
    }
    $stmt = $this->sqlUpdateExplode($params, $bindings);
    $bindings[':id'] = $id;
    $bindings[':owner'] = $this->owner;

    $result = $this->db->execute("UPDATE {$this->postgreSqlTablePrefix}groupname SET {$stmt} WHERE idx=:id AND owner=:owner", $bindings);
    if($members !== false)
    {
      $this->deleteGroupMembers($id);
      $this->addGroupMembers($id, $members);
    }

    return $result !== false;
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
    getLogger()->info("Starting postPhoto"); 
    if(empty($id))
      return false;
    elseif(empty($params))
      return true;

    $tags = false;
    if(isset($params['tags']))
      $tags = !empty($params['tags']) ? (array)explode(',', $params['tags']) : null;

    // path\d+x\d+ keys go into the photoVersion table
    foreach($params as $key => $val)
    {
      if(preg_match('/^path\d+x\d+/', $key))
      {
        $versions[$key] = $val;
        unset($params[$key]);
      }
    }

    if(!empty($params))
    {
      if(isset($params['groups']))
      {
        $this->deleteGroupsFromElement($id, 'photo');
        $this->addGroupsToElement($id, $params['groups'], 'photo');
        // TODO: Generalize this and use for tags too -- @jmathai
        $params['groups'] = preg_replace(array('/^,|,$/','/,{2,}/'), array('', ','), implode(',', $params['groups']));
      }
      $params = $this->preparePhoto($id, $params);
      unset($params['id']);
      $bindings = $params['::bindings'];
      $stmt = $this->sqlUpdateExplode($params, $bindings);
      $res = $this->db->execute("UPDATE {$this->postgreSqlTablePrefix}photo SET {$stmt} WHERE idx=:id AND owner=:owner", 
        array_merge($bindings, array(':id' => $id, ':owner' => $this->owner)));
    }
    if(!empty($versions))
      $resVersions = $this->postVersions($id, $versions);

    if($tags !== false)
    {
      $this->deleteTagsFromElement($id, 'photo');
      if(!empty($tags))
      {
        // TODO combine this into a multi row insert in addTagsToElement
        foreach($tags as $tag)
          $this->addTagToElement($id, $tag, 'photo');
      }
    }

    return (isset($res) && $res !== false) || (isset($resVersions) && $resVersions);
  }

  /**
    * Update a single tag.
    * The $params should include the tag in the id field.
    * [{id: tag1, count:10, longitude:12.34, latitude:56.78},...]
    *
    * @param array $params Tags and related attributes to update.
    * @return boolean
    */
  public function postTag($id, $params)
  {
    getLogger()->info("Starting postTag");

    /* Can't use this in PostgreSQL, using idx instead */
    unset($params['id']);

    if(!isset($params['idx']))
    {
      $params['idx'] = $id;
    }
    $params['owner'] = $this->owner;
    $params = $this->prepareTag($params);
    if(isset($params['::bindings']))
      $bindings = $params['::bindings'];
    else
      $bindings = array();

    $results = $this->db->one("SELECT * FROM {$this->postgreSqlTablePrefix}tag WHERE owner=:owner AND idx=:idx",
                               array(':owner' => $this->owner, ':idx' => $id));

    $stmtIns = $this->sqlInsertExplode($params, $bindings);
    $stmtUpd = $this->sqlUpdateExplode($params, $bindings);

    if ($results)
    { 
	getLogger()->info(print_r($bindings,1));
	getLogger()->info(print_r($stmtUpd,1));
	$result = $this->db->execute("UPDATE {$this->postgreSqlTablePrefix}tag SET {$stmtUpd} WHERE owner='{$this->owner}' AND idx='{$id}'", $bindings);
    }
    else
    {
	$result = $this->db->execute("INSERT INTO {$this->postgreSqlTablePrefix}tag ({$stmtIns['cols']}) VALUES ({$stmtIns['vals']})", $bindings);
	getLogger()->info(print_r($stmtIns,1));
    }

    return ($result !== false);
  }

  /**
    * Update multiple tags.
    * The $params should include the tag in the id field.
    * [{id: tag1, count:10, longitude:12.34, latitude:56.78},...]
    *
    * @param array $params Tags and related attributes to update.
    * @return boolean
    */
  public function postTags($params)
  {
    if(empty($params))
      return true;
    foreach($params as $tagObj)
    {
      $res = $this->postTag($tagObj['id'], $tagObj);
    }
    return $res;
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
    $params = $this->prepareUser($params);
    $res = $this->db->execute("UPDATE {$this->postgreSqlTablePrefix}username SET extra=:extra WHERE idx=:id", array(':id' => $this->owner, ':extra' => $params));
    return ($res == 1);
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
    $stmt = $this->sqlUpdateExplode($params);
    $res = $this->db->execute("UPDATE {$this->postgreSqlTablePrefix}webhook SET {$stmt} WHERE idx=:id AND owner=:owner", array(':id' => $id, ':owner' => $this->owner));
    return ($res == 1);
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
    $stmt = $this->sqlInsertExplode($this->prepareActivity($params));
    $result = $this->db->execute("INSERT INTO {$this->postgreSqlTablePrefix}activity (idx,{$stmt['cols']}) VALUES (:id,{$stmt['vals']})", array(':id' => $id));
    return ($result !== false);
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
    $stmt = $this->sqlInsertExplode($params);
    $result = $this->db->execute("INSERT INTO {$this->postgreSqlTablePrefix}action (idx,{$stmt['cols']}) VALUES (:id,{$stmt['vals']})", array(':id' => $id));
    return ($result !== false);
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
    $params['owner'] = $this->owner;
    $stmt = $this->sqlInsertExplode($params);
    $result = $this->db->execute($sql = "INSERT INTO {$this->postgreSqlTablePrefix}album (idx,{$stmt['cols']}) VALUES (:id,{$stmt['vals']})", array(':id' => $id));
    return ($result !== false);
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
    $params['owner'] = $this->owner;
    if(!isset($params['id']))
      $params['id'] = $id;
    $params = $this->prepareCredential($params);
    $stmt = $this->sqlInsertExplode($params);
    $result = $this->db->execute("INSERT INTO {$this->postgreSqlTablePrefix}credential ({$stmt['cols']}) VALUES ({$stmt['vals']})");

    return ($result !== false);
  }

  /**
    * Alias of postGroup
    */
  public function putGroup($id, $params)
  {
    $params['owner'] = $this->owner;
    if(!isset($params['id']))
      $params['id'] = $id;
    $members = false;
    if(isset($params['members']))
    {
      $members = !empty($params['members']) ? (array)explode(',', $params['members']) : null;
      unset($params['members']);
    }
    $params = $this->prepareGroup($id, $params);
    $stmt = $this->sqlInsertExplode($params);
    $result = $this->db->execute("INSERT INTO {$this->postgreSqlTablePrefix}groupname ({$stmt['cols']}) VALUES ({$stmt['vals']})");
    if($members !== false)
      $this->addGroupMembers($id, $members);
    return $result !== false;
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
    getLogger()->info("Starting putPhoto");
    $params['idx'] = $id;
    $params['owner'] = $this->owner;
    $tags = null;
    if(isset($params['tags']) && !empty($params['tags']))
      $tags = (array)explode(',', $params['tags']);
    $params = $this->preparePhoto($id, $params);
    $bindings = $params['::bindings'];
    $stmt = $this->sqlInsertExplode($params, $bindings);
    $sql = "INSERT INTO {$this->postgreSqlTablePrefix}photo ({$stmt['cols']}) VALUES ({$stmt['vals']})";
    getLogger()->info(print_r($bindings,1));
    getLogger()->info($sql);
    $result = $this->db->execute("INSERT INTO {$this->postgreSqlTablePrefix}photo ({$stmt['cols']}) VALUES ({$stmt['vals']})", $bindings);
    if(!empty($tags))
    {
      foreach($tags as $tag)
        $this->addTagToElement($id, $tag, 'photo');
    }
    if($result !== false)
	getLogger()->info("result is going to be false");
    getLogger()->info("Ending putPhoto");
    return $result !== false;
  }

  /**
    * Add a new tag to the database
    * This method does not overwrite existing values present in $params - hence "new tag".
    *
    * @param string $id ID of the user to update which is always 1.
    * @param array $params Attributes to update.
    * @return boolean
    */
  public function putTag($id, $params)
  {
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
    $params = $this->prepareUser($params);
    $result = $this->db->execute("INSERT INTO {$this->postgreSqlTablePrefix}username (idx,extra) VALUES (:id,:extra)", array(':id' => $this->owner, ':extra' => $params['extra']));
    return $result !== false;
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
    $stmt = $this->sqlInsertExplode($params);
    $result = $this->db->execute("INSERT INTO {$this->postgreSqlTablePrefix}webhook (idx,owner,{$stmt['cols']}) VALUES (:id,:owner,{$stmt['vals']})", array(':id' => $id, ':owner' => $this->owner));
    return $result !== false;
  }

  /**
    * Get the current database version
    *
    * @return string Version number
    */
  public function version()
  {
    try
    {
      $result = $this->db->one("SELECT * from {$this->postgreSqlTablePrefix}admin WHERE key=:key", array(':key' => 'version'));
      if($result)
        return $result['value'];
    }
    catch(EpiDatabaseException $e)
    {
      return '0.0.0';
    }

    return '0.0.0';
  }

  /**
    * Add members to a group
    *
    * @param string $id Group id
    * @param array $members Members to be added
    * @return boolean
    */
  private function addGroupMembers($id, $members)
  {
    if(empty($id) || empty($members))
      return false;

    $sql = "REPLACE INTO {$this->postgreSqlTablePrefix}groupmember(owner, group, email) VALUES ";
    foreach($members as $member)
      $sql .= sprintf("('%s', '%s', '%s'),", $this->_($this->owner), $this->_($id), $this->_($member));

    $sql = substr($sql, 0, -1);
    $res = $this->db->execute($sql);
    return $res > 0;
  }

  /**
    * Insert groups into the mapping table
    *
    * @param string $id Element id (id of the photo or video)
    * @param string $tag Tag to be added
    * @param string $type Element type (photo or video)
    * @return boolean
    */
  private function addGroupsToElement($id, $groups, $type)
  {
    if(empty($id) || empty($groups) || empty($type))
      return false;

    $hasGroup = false;
    $sql = "REPLACE INTO {$this->postgreSqlTablePrefix}elementgroup(owner, type, element, group) VALUES";
    foreach($groups as $group)
    {
      if(strlen($group) > 0)
      {
        $sql .= sprintf("('%s', '%s', '%s', '%s'),", $this->_($this->owner), $this->_($type), $this->_($id), $this->_($group));
        $hasGroup = true;
      }
    }

    if(!$hasGroup)
      return false;

    $sql = substr($sql, 0, -1);
    $res = $this->db->execute($sql);
    return $res !== false;
  }

  /**
    * Insert tags into the mapping table
    *
    * @param string $id Element id (id of the photo or video)
    * @param string $tag Tag to be added
    * @param string $type Element type (photo or video)
    * @return boolean
    */
  private function addTagToElement($id, $tag, $type)
  {
    getLogger()->info("Starting addTagToElement");

    /* Look for existing tag first */
    $results = $this->db->one("SELECT * FROM {$this->postgreSqlTablePrefix}elementtag WHERE owner=:owner AND tagtype=:tagtype AND element=:element AND tag=:tag",
                               array(':owner' => $this->owner, ':tagtype' => $type, ':element' => $id, ':tag' => $tag));

    /* if it exists do an insert, otherwise do an update */
    if(empty($results))
    	$sql = "INSERT INTO {$this->postgreSqlTablePrefix}elementtag (owner, tagtype, element, tag) VALUES ('$this->owner', '$type', '$id', '$tag')";
    else
    	$sql = "UPDATE {$this->postgreSqlTablePrefix}elementtag SET (owner, tagtype, element, tag) = ('$this->owner', '$type', '$id', '$tag') WHERE element = '$id'";

    $res = $this->db->execute($sql);
    
    return $res !== false;
  }

  /**
    * Build parts of the photos select query
    *
    * @param array $filters filers used to perform searches
    * @param int $limit number of records to have returned
    * @param offset $offset starting point for records
    * @return array
    */
  private function buildQuery($filters, $limit, $offset)
  {
    // TODO: support logic for multiple conditions
    $from = "FROM {$this->postgreSqlTablePrefix}photo ";
    $where = "WHERE {$this->postgreSqlTablePrefix}photo.owner='{$this->owner}'";
    $groupBy = '';
    $sortBy = 'ORDER BY datetaken DESC';
    if(!empty($filters) && is_array($filters))
    {
      foreach($filters as $name => $value)
      {
        switch($name)
        {
          case 'hash':
            $hash = $this->_($value);
            $where = $this->buildWhere($where, "hash='{$hash}'");
            break;
          case 'groups':
            if(!is_array($value))
              $value = (array)explode(',', $value);
            foreach($value as $k => $v)
              $value[$k] = $this->_($v);
            $subquery = sprintf("(id IN (SELECT element FROM {$this->postgreSqlTablePrefix}elementgroup WHERE {$this->postgreSqlTablePrefix}elementgroup.owner='%s' AND type='%s' AND group IN('%s')) OR permission='1')",
              $this->_($this->owner), 'photo', implode("','", $value));
            $where = $this->buildWhere($where, $subquery);
            break;
          case 'page':
            if($value > 1)
            {
              $value = min($value, 40); // 40 pages at max of 2,500 recursion limit means 100k photos
              $offset = intval(($limit * $value) - $limit);
            }
            break;
          case 'permission':
            $where = $this->buildWhere($where, "permission='1'");
            break;
          case 'sortBy':
            $sortBy = 'ORDER BY ' . $this->_(str_replace(',', ' ', $value));
            $field = $this->_(substr($value, 0, strpos($value, ',')));
            $where = $this->buildWhere($where, "{$field} is not null");
            break;
          case 'tags':
            if(!is_array($value))
              $value = (array)explode(',', $value);
            $tagCount = count($value);
            if($tagCount == 0)
              break;

            $ids = array();
            foreach($value as $k => $v)
            {
              $v = $value[$k] = $this->_($v);
              $thisRes = $this->db->all(sprintf("SELECT element, tag FROM %selementtag WHERE owner='%s' AND tagtype='photo' AND tag='%s'", $this->postgreSqlTablePrefix, $this->owner, $v));
              foreach($thisRes as $t)
              {
                if(isset($ids[$t['element']]))
                  $ids[$t['element']]++;
                else
                  $ids[$t['element']] = 1;
              }
            }
            
            foreach($ids as $k => $cnt)
            {
              if($cnt < $tagCount)
                unset($ids[$k]);
            }

            $where = $this->buildWhere($where, sprintf("%sphoto.idx IN('%s')", $this->postgreSqlTablePrefix, implode("','", array_keys($ids))));
            break;
        }
      }
    }

    $limit_sql = '';
    if($limit)
      $limit_sql = "LIMIT {$limit}";

    $offset_sql = '';
    if($offset)
      $offset_sql = "OFFSET {$offset}";

    $ret = array('from' => $from, 'where' => $where, 'groupBy' => $groupBy, 'sortBy' => $sortBy, 'limit' => $limit_sql, 'offset' => $offset_sql);
    return $ret;
  }
  /**
    * Utility function to help build the WHERE clause for SELECT statements.
    * (Taken from DatabaseSimpleDb)
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
    * Delete groups for an element from the mapping table
    *
    * @param string $id Element id (id of the photo or video)
    * @param string $tag Tag to be added
    * @param string $type Element type (photo or video)
    * @return boolean
    */
  private function deleteGroupsFromElement($id, $type)
  {
    $res = $this->db->execute("DELETE FROM {$this->postgreSqlTablePrefix}elementgroup WHERE owner=:owner AND type=:type AND element=:element", array(':owner' => $this->owner, ':type' => $type, ':element' => $id));
    return $res !== false;
  }

  /**
    * Delete members from a group
    *
    * @param string $id Element id (id of the photo or video)
    * @return boolean
    */
  private function deleteGroupMembers($id)
  {
    $res = $this->db->execute("DELETE FROM {$this->postgreSqlTablePrefix}groupmember WHERE owner=:owner AND group=:group", array(':owner' => $this->owner, ':group' => $id));
    return $res !== false;
  }

  /**
    * Delete tags for an element from the mapping table
    *
    * @param string $id Element id (id of the photo or video)
    * @param string $tag Tag to be added
    * @param string $type Element type (photo or video)
    * @return boolean
    */
  private function deleteTagsFromElement($id, $type)
  {
    $res = $this->db->execute("DELETE FROM {$this->postgreSqlTablePrefix}elementtag WHERE owner=:owner AND tagtype=:type AND element=:element", array(':owner' => $this->owner, ':type' => $type, ':element' => $id));
    return $res !== false;
  }

  /**
    * Get all the versions of a given photo
    * TODO this can be eliminated once versions are in the json field
    *
    * @param string $id Id of the photo of which to get versions of
    * @return array Array of versions
    */
  private function getPhotoVersions($id)
  {
    $versions = $this->db->all("SELECT key,path FROM {$this->postgreSqlTablePrefix}photoversion WHERE idx=:id AND owner=:owner",
                 array(':id' => $id, ':owner' => $this->owner));
    if(empty($versions))
      return false;
    return $versions;
  }

  /**
    * Normalizes data from MySql into schema definition
    *
    * @param SimpleXMLObject $raw An action from SimpleDb in SimpleXML.
    * @return array
    */
  private function normalizeActivity($raw)
  {
    $raw['data'] = json_decode($raw['data'], 1);
    return $raw;
  }

  /**
    * Normalizes data from MySql into schema definition
    *
    * @param SimpleXMLObject $raw An action from SimpleDb in SimpleXML.
    * @return array
    */
  private function normalizeAction($raw)
  {
    return $raw;
  }

  /**
    * Normalizes data from MySql into schema definition
    *
    * @param SimpleXMLObject $raw An action from SimpleDb in SimpleXML.
    * @return array
    */
  private function normalizeAlbum($raw)
  {
    if(empty($raw))
      return $raw;

    $raw['coverId'] = $raw['coverPhoto'] = null;
    if(!empty($raw['extra']))
    {
      $extra = json_decode($raw['extra'], 1);
      if(isset($extra['coverId']))
        $raw['coverId'] = $extra['coverId'];
      if(isset($extra['coverPhoto']))
        $raw['coverPhoto'] = $extra['coverPhoto'];
    }
    unset($raw['extra']);
    return $raw;
  }

  /**
    * Normalizes data from simpleDb into schema definition
    * TODO this should eventually translate the json field
    *
    * @param SimpleXMLObject $raw A photo from SimpleDb in SimpleXML.
    * @return array
    */
  private function normalizePhoto($photo)
  {
    if(empty($photo))
      return $photo;

    $photo['appId'] = $this->config->application->appId;

    $versions = $this->getPhotoVersions($photo['idx']);
    if($versions && !empty($versions))
    {
      foreach($versions as $version)
      {
        $photo[$version['key']] = $version['path'];
      }
    }

    $photo['id'] = $photo['idx'];
    $photo['pathBase'] = $photo['pathbase'];
    $photo['pathOriginal'] = $photo['pathoriginal'];
    $photo['dateTaken'] = $photo['datetaken'];

    $photo['tags'] = strlen($photo['tags']) ? (array)explode(",", $photo['tags']) : array();
    $photo['groups'] = strlen($photo['groups']) ? (array)explode(",", $photo['groups']) : array();

    $exif = (array)json_decode($photo['exif']);
    $extra = (array)json_decode($photo['extra']);
    $photo = array_merge($photo, $exif, $extra);
    unset($photo['exif'], $photo['extra']);

    return $photo;
  }

  /**
    * Normalizes data from simpleDb into schema definition
    * TODO this should eventually translate the json field
    *
    * @param SimpleXMLObject $raw A tag from SimpleDb in SimpleXML.
    * @return array
    */
  private function normalizeTag($raw)
  {
    return $raw;
  }

  private function normalizeWebhook($raw)
  {
    return $raw;
  }

  /**
    * Normalizes data from simpleDb into schema definition
    * TODO this should eventually translate the json field
    *
    * @param SimpleXMLObject $raw A tag from SimpleDb in SimpleXML.
    * @return array
    */
  private function normalizeUser($raw)
  {
    $jsonParsed = json_decode($raw['extra'], 1);
    if(!empty($jsonParsed))
    {
      foreach($jsonParsed as $key => $value)
        $raw[$key] = $value;
    }
    unset($raw['extra']);
    return $raw;
  }

  private function normalizeCredential($raw)
  {
    if(isset($raw['permissions']) && !empty($raw['permissions']))
      $raw['permissions'] = (array)explode(',', $raw['permissions']);

    return $raw;
  }


  /**
    * Normalizes data from simpleDb into schema definition
    *
    * @param SimpleXMLObject $raw An action from SimpleDb in SimpleXML.
    * @return array
    */
  private function normalizeGroup($raw)
  {
    if(!isset($raw['members']) || empty($raw['members']))
      $raw['members'] = array();
    return $raw;
  }

  /** Prepare activity to store in the database
   */
  private function prepareActivity($params)
  {
    if(isset($params['data']))
      $params['data'] = json_encode($params['data']);

    return $params;
  }

  /** Prepare credential to store in the database
   */
  private function prepareCredential($params)
  {
    if(isset($params['permissions']))
      $params['permissions'] = implode(',', $params['permissions']);

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
    $paramsOut = $bindings = $exif = $extra = array();
    foreach($params as $key => $value)
    {
      switch($key)
      {
        case 'exif':
        case 'exifOrientation':
        case 'exifCameraMake':
        case 'exifCameraModel':
        case 'exifExposureTime':
        case 'exifFNumber':
        case 'exifMaxApertureValue':
        case 'exifMeteringMode':
        case 'exifFlash':
        case 'exifFocalLength':
        case 'exifISOSpeed':
        case 'gpsAltitude':
          $exif[$key] = $value;
          break;
        case 'extra':
          break;
        default:
          // when lat/long is empty set value to null else it is stored as 0. Gh-313
          if(($key == 'latitude' || $key == 'longitude') && empty($value))
            $value = null;
          $bindings[":{$key}"] = $value;
          $paramsOut[$key] = ":{$key}";
          break;
      }
    }
    if(!empty($exif))
    {
      $bindings[":exif"] = json_encode($exif);
      $paramsOut['exif'] = ':exif';
    }
    if(!empty($extra))
    {
      $bindings[":extra"] = json_encode($extra);
      $paramsOut['extra'] = ':extra';
    }
    $paramsOut['::bindings'] = $bindings;
    return array_change_key_case($paramsOut, CASE_LOWER);
  }

  /** Prepare tags to store in the database
   */
  private function prepareTag($params)
  {
    $bindings = array();
    if(!empty($params['idx']))
    {
      $bindings[':idx'] = $params['idx'];
      $params['idx'] = ':idx';
    }

    if(!empty($bindings))
    {
      $params['::bindings'] = $bindings;
    }
    return $params;
  }

  /** Prepare user to store in the database
   */
  private function prepareUser($params)
  {
    $ret = array();
    if(isset($params) && is_array($params) && !empty($params))
    {
      foreach($params as $key => $val)
        $ret[$key] = $val;
    }
    return json_encode($ret);
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
    return $params;
  }

  /**
    * Inserts a new version of photo with $id and $versions
    * TODO this should be in a json field in the photo table
    *
    * @param string $id ID of the photo.
    * @param array $versions Versions to the photo be inserted
    * @return array
    */
  private function postVersions($id, $versions)
  {
    foreach($versions as $key => $value)
    {
      // TODO this is gonna fail if we already have the version -- hfiguiere
      // Possibly use REPLACE INTO? -- jmathai
      $result = $this->db->execute("INSERT INTO {$this->postgreSqlTablePrefix}photoversion (idx, owner, key, path) VALUES(:id, :owner, :key, :value)",
        array(':id' => $id, ':owner' => $this->owner, ':key' => $key, ':value' => $value));
    }
    // TODO, what type of return value should we have here -- jmathai
    return ($result != 1);
  }

  /**
   * Explode params associative array into SQL insert statement lists
   * Return an array with 'cols' and 'vals'
   */
  private function sqlInsertExplode($params, $bindings = array())
  {
    $stmt = array('cols' => '', 'vals' => '');
    foreach($params as $key => $value)
    {
      if($key == '::bindings')
        continue;
      if(!empty($stmt['cols']))
        $stmt['cols'] .= ",";
      if(!empty($stmt['vals']))
        $stmt['vals'] .= ",";
      $stmt['cols'] .= $key;
      if(!empty($bindings) && array_key_exists($value, $bindings))
      {
        if(is_null($value))
          $stmt['vals'] .= 'NULL';
        else
          $stmt['vals'] .= $value;
      }
      else
      {
        if(is_null($value))
          $stmt['vals'] .= 'NULL';
        else
          $stmt['vals'] .= sprintf("'%s'", $this->_($value));
      }
    }
    return $stmt;
  }

  /**
   * Explode params associative array into SQL update statement lists
   * TODO, have this work with PDO named parameters
   *
   * Return a string
   */
  private function sqlUpdateExplode($params, $bindings = array())
  {
    $stmt = '';
    foreach($params as $key => $value)
    {
      if($key == '::bindings')
        continue;
      if(!empty($stmt)) {
        $stmt .= ",";
      }
      if(!empty($bindings) && array_key_exists($value, $bindings))
        $stmt .= "{$key}={$value}";
      else
        $stmt .= sprintf("%s='%s'", $key, $this->_($value));
    }
    return $stmt;
  }

  /**
   * Wrapper function for escaping strings for queries
   *
   * @param string $str String to be escaped
   * @return string
   */
  private function _($str)
  {
    return addslashes($str);
  }
}
