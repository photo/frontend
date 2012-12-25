<?php
/**
 * MySQL implementation
 *
 * This class defines the functionality defined by DatabaseInterface for a MySQL database.
 * It use EpiDatabase.
 * @author Hub Figuiere <hub@figuiere.net>
 */
class DatabaseMySql implements DatabaseInterface
{
  /**
    * Member variables holding the names to the SimpleDb domains needed and the database object itself.
    * @access private
    */
  private $config, $errors = array(), $owner, $mySqlDb, $mySqlHost, $mySqlUser, $mySqlPassword, $mySqlTablePrefix;

  /**
    * Constructor
    *
    * @return void
    */
  public function __construct($config = null, $params = null)
  {
    $this->config = !is_null($config) ? $config : getConfig()->get();
    $mysql = $this->config->mysql;

    if(!is_null($params) && isset($params['db']))
    {
      $this->db = $params['db'];
    }
    else
    {
      $utilityObj = new Utility;
      EpiDatabase::employ('mysql', $mysql->mySqlDb,
                          $mysql->mySqlHost, $mysql->mySqlUser, $utilityObj->decrypt($mysql->mySqlPassword));
      $this->db = getDatabase();
    }

    foreach($mysql as $key => $value)
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
    $res = $this->db->execute("DELETE FROM `{$this->mySqlTablePrefix}action` WHERE `id`=:id AND `owner`=:owner", array(':id' => $id, ':owner' => $this->owner));
    return ($res !== false);
  }

  /**
    * Delete an album from the database
    *
    * @param string $id ID of the action to delete
    * @return boolean
    */
  public function deleteAlbum($id)
  {
    // if one fails then don't continue by using the second condition
    $res1 = $this->db->execute("DELETE FROM `{$this->mySqlTablePrefix}album` WHERE `id`=:id AND `owner`=:owner", array(':id' => $id, ':owner' => $this->owner));
    $res2 = $this->db->execute("DELETE FROM `{$this->mySqlTablePrefix}elementAlbum` WHERE `owner`=:owner AND `album`=:album", array(':owner' => $this->owner, ':album' => $id));

    if($res1 === false || $res2 === false)
      return false;

    return true;
  }

  /**
    * Delete credential
    *
    * @return boolean
    */
  public function deleteCredential($id)
  {
    $res = $this->db->execute("DELETE FROM `{$this->mySqlTablePrefix}credential` WHERE `id`=:id AND owner=:owner", array(':id' => $id, ':owner' => $this->owner));
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
    $res = $this->db->execute("DELETE FROM `{$this->mySqlTablePrefix}group` WHERE `id`=:id AND owner=:owner", array(':id' => $id, ':owner' => $this->owner));
    return ($res !== false);
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

    $resPhoto = $this->db->execute("DELETE FROM `{$this->mySqlTablePrefix}photo` WHERE `id`=:id AND owner=:owner", array(':id' => $photo['id'], ':owner' => $this->owner));
    $resVersions = $this->deletePhotoVersions($photo);

    return ($resPhoto !== false && $resVersions !== false);
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

    $res = $this->db->execute("DELETE FROM `{$this->mySqlTablePrefix}photoVersion` WHERE `owner`=:owner AND `id`=:id", array(':id' => $photo['id'], ':owner' => $this->owner));
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
    $resDel = $this->db->execute("DELETE FROM `{$this->mySqlTablePrefix}tag` WHERE `id`=:id AND owner=:owner", array(':id' => $id, ':owner' => $this->owner));
    $resClean = $this->db->execute("DELETE FROM `{$this->mySqlTablePrefix}elementTag` WHERE `owner`=:owner AND `tag`=:tag", array(':owner' => $this->owner, ':tag' => $id));
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
    $res = $this->db->execute("DELETE FROM `{$this->mySqlTablePrefix}webhook` WHERE `id`=:id AND owner=:owner", array(':id' => $id, ':owner' => $this->owner));
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
    $res = $this->db->execute("SELECT * FROM `{$this->mySqlTablePrefix}photo` WHERE owner=:owner LIMIT 1", array(':owner' => $this->owner));
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
    if($database != 'mysql')
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
    $action = $this->db->one("SELECT * FROM `{$this->mySqlTablePrefix}action` WHERE `id`=:id AND owner=:owner",
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
  public function getActivities($filters = array(), $limit = 10)
  {
    $filters['sortBy'] = 'dateCreated,desc';
    $buildQuery = $this->buildQuery($filters, $limit, null, 'activity');
    $activities = $this->db->all($sql = "SELECT * FROM `{$this->mySqlTablePrefix}activity` {$buildQuery['where']} {$buildQuery['sortBy']} {$buildQuery['limit']}",
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
    $activity = $this->db->all("SELECT * FROM `{$this->mySqlTablePrefix}activity` WHERE `id`=:id AND `owner`=:owner",
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
    $album = $this->db->one("SELECT * FROM `{$this->mySqlTablePrefix}album` WHERE `owner`=:owner AND `id`=:id ",
      array(':id' => $id, ':owner' => $this->owner));

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
    $photos = $this->db->all("SELECT `pht`.* 
      FROM `{$this->mySqlTablePrefix}photo` AS `pht` INNER JOIN `{$this->mySqlTablePrefix}elementAlbum` AS `alb` ON `pht`.`id`=`alb`.`element`
      WHERE `pht`.`owner`=:owner AND `alb`.`owner`=:owner
      AND `alb`.`album`=:album",
      array(':owner' => $this->owner, ':album' => $id));

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
  public function getAlbums($email, $limit = null, $offset = null)
  {
    // TODO jmathai, confirm MySql is optimized for a high LIMIT
    if($limit === null)
      $limit = 10;
    elseif($limit === 0)
      $limit = PHP_INT_MAX;

    $limit = (int)$limit;
    $offset = (int)$offset;

    if($this->owner === $email)
      $albums = $this->db->all("SELECT * FROM `{$this->mySqlTablePrefix}album` WHERE `owner`=:owner ORDER BY `name` LIMIT {$offset}, {$limit}", array(':owner' => $this->owner));
    else
      $albums = $this->db->all("SELECT * FROM `{$this->mySqlTablePrefix}album` WHERE `owner`=:owner AND `visible`=1 ORDER BY `name` LIMIT {$offset}, {$limit}", array(':owner' => $this->owner));


    if($albums === false)
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
    $cred = $this->db->one("SELECT * FROM `{$this->mySqlTablePrefix}credential` WHERE `id`=:id AND owner=:owner",
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
    $cred = $this->db->one("SELECT * FROM `{$this->mySqlTablePrefix}credential` WHERE userToken=:userToken AND owner=:owner",
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
    $res = $this->db->all("SELECT * FROM `{$this->mySqlTablePrefix}credential` WHERE owner=:owner AND status=1
						   ORDER BY `dateCreated` DESC", 
						   array(':owner' => $this->owner));
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
    $res = $this->db->all("SELECT grp.*, memb.email FROM `{$this->mySqlTablePrefix}group` AS grp LEFT JOIN `{$this->mySqlTablePrefix}groupMember` AS memb ON `grp`.`owner`=`memb`.`owner` WHERE `grp`.`id`=:id AND `grp`.`owner`=:owner", array(':id' => $id ,':owner' => $this->owner));
    if($res === false || empty($res))
      return false;

    $group = array('id' => $res[0]['id'], 'owner' => $res[0]['owner'], 'name' => $res[0]['name'], 'permission' => $res[0]['permission'], 'members' => array());
    foreach($res as $r)
    {
      if(!empty($r['email']))
        $group['members'][] = $r['email'];
    }

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
      $res = $this->db->all("SELECT `grp`.*, `memb`.`email` 
        FROM `{$this->mySqlTablePrefix}group` AS `grp` 
        LEFT JOIN `{$this->mySqlTablePrefix}groupMember` AS `memb` ON `grp`.`owner`=`memb`.`owner` AND `grp`.`id`=`memb`.`group` 
        WHERE `grp`.`id` IS NOT NULL AND `grp`.`owner`=:owner 
        ORDER BY `grp`.`name`", array(':owner' => $this->owner));
    else
      $res = $this->db->all("SELECT `grp`.*, `memb`.`email` 
        FROM `{$this->mySqlTablePrefix}group` AS `grp` 
        LEFT JOIN `{$this->mySqlTablePrefix}groupMember` AS `memb` ON `grp`.`owner`=`memb`.`owner` AND `grp`.`id`=`memb`.`group` 
        WHERE `memb`.`email`=:email AND `grp`.`id` IS NOT NULL AND `grp`.`owner`=:owner 
        ORDER BY `grp`.`name`", array(':email' => $email, ':owner' => $this->owner));

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

          if(!empty($group['email']))
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
    $photo = $this->db->one("SELECT * FROM `{$this->mySqlTablePrefix}photo` WHERE `id`=:id AND owner=:owner", array(':id' => $id, ':owner' => $this->owner));
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
    $buildQuery = $this->buildQuery($filterOpts, null, null, 'photo');
    $photo = $this->getPhoto($id);
    if(!$photo || !isset($photo['dateSortByDay']) || empty($photo['dateSortByDay']))
      return false;

    // owner is in buildQuery
    // TODO: paginating with random sorting is a pain - default for now
    // determine where to start
    // this should return the immediately adjacent photo prior to $photo
    // if there are none we set it to the current photo and only get a next
    $startResp = $this->db->all("SELECT `id`, `dateSortByDay` FROM `{$this->mySqlTablePrefix}photo` {$buildQuery['where']} AND `dateSortByDay` < :dateSortByDay {$buildQuery['groupBy']} ORDER BY `dateSortByDay` DESC, `id` DESC LIMIT 2", 
      array(':dateSortByDay' => $photo['dateSortByDay']));
    $ind = count($startResp)-1;
    if($ind >= 0)
      $startValue = $startResp[$ind]['dateSortByDay'];
    else
      $startValue = $photo['dateSortByDay'];

    // remembering that the photos are sorted in descending order on dateSortByDay
    // we reverse the sort so it's the oldest first then select everything after the 
    // photo immediately older than this one
    $photosNextPrev = $this->db->all(
      $sql = " SELECT `{$this->mySqlTablePrefix}photo`.*
        {$buildQuery['from']}
        {$buildQuery['where']} AND `dateSortByDay` >= :startValue
        {$buildQuery['groupBy']}
        ORDER BY `dateSortByDay` ASC, `id` ASC
        LIMIT 5", 
      array(':startValue' => $startValue)
    );

    $ret = array();
    if(!empty($photosNextPrev))
    {
      if($photosNextPrev[0]['dateSortByDay'] <= $photo['dateSortByDay'] && $photosNextPrev[0]['id'] !== $photo['id'])
      {
        $ret['next'] = array();
        if($photosNextPrev[1]['dateSortByDay'] <= $photo['dateSortByDay'] && $photosNextPrev[1]['id'] !== $photo['id'])
          $ret['next'][] = $this->normalizePhoto($photosNextPrev[1]);

        $ret['next'][] = $this->normalizePhoto($photosNextPrev[0]);
      }

      $last = array_pop($photosNextPrev);
      if($last && $last['dateSortByDay'] > $photo['dateSortByDay'] && $last['id'] !== $photo['id'])
      {
        $otherLast = array_pop($photosNextPrev);
        if($otherLast && $last['dateSortByDay'] > $photo['dateSortByDay'] && $otherLast['id'] !== $photo['id'])
          $ret['previous'][] = $this->normalizePhoto($otherLast);

        $ret['previous'][] = $this->normalizePhoto($last);
      }
    }
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
      $actions = $this->db->all("SELECT * FROM `{$this->mySqlTablePrefix}action` WHERE owner=:owner AND targetType='photo' AND targetId=:id",
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
    $query = $this->buildQuery($filters, $limit, $offset, 'photo');
    // buildQuery includes owner
    $photos = $this->db->all($sql = "SELECT {$this->mySqlTablePrefix}photo.* {$query['from']} {$query['where']} {$query['groupBy']} {$query['sortBy']} {$query['limit']} {$query['offset']}");
    if($photos === false)
      return false;

    for($i = 0; $i < count($photos); $i++)
      $photos[$i] = $this->normalizePhoto($photos[$i]);

    $photos[0]['currentRows'] = count($photos);
    // TODO evaluate SQL_CALC_FOUND_ROWS (indexes with the query builder might be hard to optimize)
    // http://www.mysqlperformanceblog.com/2007/08/28/to-sql_calc_found_rows-or-not-to-sql_calc_found_rows/
    $result = $this->db->one("SELECT COUNT(*) {$query['from']} {$query['where']} {$query['groupBy']}");
    if(!empty($result))
      $photos[0]['totalRows'] = intval($result['COUNT(*)']);

    return $photos;
  }

  /**
    * Get a resource map
    *
    * @param string $id tag to be retrieved
    * @return mixed Array on success, FALSE on failure
    */
  public function getResourceMap($id)
  {
    $resourceMap = $this->db->one("SELECT * FROM `{$this->mySqlTablePrefix}resourceMap` WHERE `owner`=:owner AND `id`=:id", array(':owner' => $this->owner, ':id' => $id));
    if(!$resourceMap)
      return false;

    $resourceMap = $this->normalizeResourceMap($resourceMap);
    return $resourceMap;
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
    $tag = $this->db->one("SELECT * FROM `{$this->mySqlTablePrefix}tag` WHERE `owner`=:owner AND `id`=:id", array(':owner' => $this->owner, ':id' => $tag));

    if(!$tag )
      return false;

    // TODO this should be in the normalize method #943
    if($tag['extra'])
      $tag = array_merge($tag, json_decode($tag['extra'], 1));
    unset($tag['extra']);
    return $tag;
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
    $sortBy = '`id`';
    $params = array(':owner' => $this->owner);
    if(isset($filters['sortBy']))
    {
      $sortParts = (array)explode(',', $filters['sortBy']);
      $sortParts[0] = $this->_($sortParts[0]);
      $sortBy = "`{$sortParts[0]}` ";
      if(isset($sortParts[1]))
      {
        $sortParts[1] = $this->_($sortParts[1]);
        $sortBy .= $sortParts[1];
      }
    }

    if(isset($filters['permission']) && $filters['permission'] == 0)
      $countField = 'countPrivate';

    if(isset($filters['search']) && $filters['search'] != '')
    {
      $filters['search'] = $this->_($filters['search']);
      $query = "SELECT * FROM `{$this->mySqlTablePrefix}tag` WHERE `owner`=:owner AND `id` IS NOT NULL AND `{$countField}` IS NOT NULL AND `{$countField}` > '0' AND `id` LIKE :search ORDER BY {$sortBy}";
      $params[':search'] = "{$filters['search']}%";
    }
    else
    {
      $query = "SELECT * FROM `{$this->mySqlTablePrefix}tag` WHERE `owner`=:owner AND `id` IS NOT NULL AND `{$countField}` IS NOT NULL AND `{$countField}` > '0' ORDER BY {$sortBy}";
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
    $res = $this->db->one($sql = "SELECT * FROM `{$this->mySqlTablePrefix}user` WHERE `id`=:owner", array(':owner' => $owner));
    if($res)
      return $this->normalizeUser($res);
    return null;
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

    $res = $this->db->one($sql = "SELECT * FROM `{$this->mySqlTablePrefix}user` WHERE `id`=:email AND `password`=:password", array(':email' => $email, ':password' => $password));
    if($res)
      return $this->normalizeUser($res);
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
    $webhook = $this->db->one("SELECT * FROM `{$this->mySqlTablePrefix}webhook` WHERE `id`=:id AND owner=:owner", array(':id' => $id, ':owner' => $this->owner));
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
      $res = $this->db->all("SELECT * FROM `{$this->mySqlTablePrefix}webhook` WHERE owner=:owner AND `topic`='{$topic}'", array(':owner' => $this->owner));
    else
      $res = $this->db->all("SELECT * FROM `{$this->mySqlTablePrefix}webhook` WHERE owner=:owner", array(':owner' => $this->owner));

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
    return array('mysql');
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

      getLogger()->crit(sprintf('Could not initialize user for MySql due to email conflict (%s).', $this->owner));
      return false;
    }
    elseif($version === '0.0.0')
    {
      try
      {
        return $this->executeScript(sprintf('%s/upgrade/db/mysql/mysql-base.php', getConfig()->get('paths')->configs), 'mysql');
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
    * Delete all activity for a user
    *
    * @return boolean
    */
  public function postActivitiesPurge()
  {
    $result = $this->db->execute("DELETE FROM `{$this->mySqlTablePrefix}activity` WHERE owner=:owner", array(':owner' => $this->owner));
    return ($result !== false);
  }

  /**
    * Update an existing album in the database
    * This method does not overwrite existing values present in $params - hence "new action".
    *
    * @param string $id ID of the action to update which is always 1.
    * @param array $params Attributes to update.
    * @return boolean
    */
  public function postAlbum($id, $params)
  {
    $params = $this->prepareAlbum($params);
    $params['owner'] = $this->owner;
    $bindings = array();
    if(isset($params['::bindings']))
      $bindings = $params['::bindings'];
    $stmt = $this->sqlUpdateExplode($params, $bindings);
    $bindings[':id'] = $id;
    $bindings[':owner'] = $this->owner;

    $result = $this->db->execute("UPDATE `{$this->mySqlTablePrefix}album` SET {$stmt} WHERE `id`=:id AND owner=:owner", $bindings);
    return ($result !== false);
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
      $tmpRes = $this->db->execute("REPLACE INTO `{$this->mySqlTablePrefix}elementAlbum`(`owner`,`type`,`element`,`album`) VALUES(:owner,:type,:elementId,:albumId)",
        array(':owner' => $this->owner, ':type' => $type, ':elementId' => $elementId, ':albumId' => $albumId));
      $res = $res && $tmpRes !== 0;
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
      $res = $res && $this->db->execute("DELETE FROM `{$this->mySqlTablePrefix}elementAlbum` WHERE `owner`=:owner AND `element`=:elementId AND `album`=:albumId AND `type`=:type",
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
      $bindings = $params['::bindings'];
    $stmt = $this->sqlUpdateExplode($params, $bindings);
    $bindings[':id'] = $id;
    $bindings[':owner'] = $this->owner;

    $result = $this->db->execute("UPDATE `{$this->mySqlTablePrefix}credential` SET {$stmt} WHERE `id`=:id AND owner=:owner", $bindings);

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

    $result = $this->db->execute("UPDATE `{$this->mySqlTablePrefix}group` SET {$stmt} WHERE `id`=:id AND owner=:owner", $bindings);
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
        $this->updateGroupToPhotoMapping($id, $params['groups']);
        // TODO: Generalize this and use for tags too -- @jmathai
        $params['groups'] = preg_replace(array('/^,|,$/','/,{2,}/'), array('', ','), $params['groups']);
      }
      if(isset($params['albums']))
      {
        $this->updateAlbumToPhotoMapping($id, $params['albums']);
        // TODO: Generalize this and use for tags too -- @jmathai
        $params['albums'] = preg_replace(array('/^,|,$/','/,{2,}/'), array('', ','), $params['albums']);
      }
      $params = $this->preparePhoto($id, $params);
      unset($params['id']);
      $bindings = $params['::bindings'];
      $stmt = $this->sqlUpdateExplode($params, $bindings);
      $res = $this->db->execute("UPDATE `{$this->mySqlTablePrefix}photo` SET {$stmt} WHERE `id`=:id AND owner=:owner", 
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
    * The $params should include the tag in the `id` field.
    * [{id: tag1, count:10, longitude:12.34, latitude:56.78},...]
    *
    * @param array $params Tags and related attributes to update.
    * @return boolean
    */
  public function postTag($id, $params)
  {
    if(!isset($params['id']))
    {
      $params['id'] = $id;
    }
    $params['owner'] = $this->owner;
    $params = $this->prepareTag($params);
    if(isset($params['::bindings']))
      $bindings = $params['::bindings'];
    else
      $bindings = array();

    $stmtIns = $this->sqlInsertExplode($params, $bindings);
    $stmtUpd = $this->sqlUpdateExplode($params, $bindings);

    $result = $this->db->execute("INSERT INTO `{$this->mySqlTablePrefix}tag` ({$stmtIns['cols']}) VALUES ({$stmtIns['vals']}) ON DUPLICATE KEY UPDATE {$stmtUpd}", $bindings);
    return ($result !== false);
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
    if(isset($params['password']) && !empty($params['password']))
    {
      $sql = "UPDATE `{$this->mySqlTablePrefix}user` SET `password`=:password,`extra`=:extra WHERE `id`=:id";
      $params = array(':id' => $this->owner, ':password' => $params['password'], ':extra' => $params['extra']);
    }
    else
    {
      $sql = "UPDATE `{$this->mySqlTablePrefix}user` SET `extra`=:extra WHERE `id`=:id";
      $params = array(':id' => $this->owner, ':extra' => $params['extra']);
    }

    $res = $this->db->execute($sql, $params); 
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
    $res = $this->db->execute("UPDATE `{$this->mySqlTablePrefix}webhook` SET {$stmt} WHERE `id`=:id AND owner=:owner", array(':id' => $id, ':owner' => $this->owner));
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
    $result = $this->db->execute("REPLACE INTO `{$this->mySqlTablePrefix}activity` (id,{$stmt['cols']}) VALUES (:id,{$stmt['vals']})", array(':id' => $id));
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
    $result = $this->db->execute("INSERT INTO `{$this->mySqlTablePrefix}action` (id,{$stmt['cols']}) VALUES (:id,{$stmt['vals']})", array(':id' => $id));
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
    $result = $this->db->execute($sql = "INSERT INTO `{$this->mySqlTablePrefix}album` (id,{$stmt['cols']}) VALUES (:id,{$stmt['vals']})", array(':id' => $id));
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
    $result = $this->db->execute("INSERT INTO `{$this->mySqlTablePrefix}credential` ({$stmt['cols']}) VALUES ({$stmt['vals']})");

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
    $result = $this->db->execute("INSERT INTO `{$this->mySqlTablePrefix}group` ({$stmt['cols']}) VALUES ({$stmt['vals']})");
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
    $params['id'] = $id;
    $params['owner'] = $this->owner;
    $tags = null;
    if(isset($params['tags']) && !empty($params['tags']))
      $tags = (array)explode(',', $params['tags']);
    if(isset($params['groups']))
    {
      $this->updateGroupToPhotoMapping($id, $params['groups']);
      // TODO: Generalize this and use for tags too -- @jmathai
      $params['groups'] = preg_replace(array('/^,|,$/','/,{2,}/'), array('', ','), $params['groups']);
    }
    if(isset($params['albums']))
    {
      $this->updateAlbumToPhotoMapping($id, $params['albums']);
      // TODO: Generalize this and use for tags too -- @jmathai
      $params['albums'] = preg_replace(array('/^,|,$/','/,{2,}/'), array('', ','), $params['albums']);
    }
    $params = $this->preparePhoto($id, $params);
    $bindings = $params['::bindings'];
    $stmt = $this->sqlInsertExplode($params, $bindings);
    $result = $this->db->execute("INSERT INTO `{$this->mySqlTablePrefix}photo` ({$stmt['cols']}) VALUES ({$stmt['vals']})", $bindings);
    if(!empty($tags))
    {
      foreach($tags as $tag)
        $this->addTagToElement($id, $tag, 'photo');
    }
    return ($result !== false);
  }

  /**
    * Create a resource map
    *
    * @param string $id resource map to be retrieved
    * @param array $params Attributes to create
    * @return mixed Array on success, FALSE on failure
    */
  public function putResourceMap($id, $params)
  {
    if(!isset($params['id']))
      $params['id'] = $id;
    $params['owner'] = $this->owner;
    $params = $this->prepareResourceMap($params);
    $stmt = $this->sqlInsertExplode($params);
    $result = $this->db->execute("INSERT INTO `{$this->mySqlTablePrefix}resourceMap` ({$stmt['cols']}) VALUES ({$stmt['vals']})");
    return  ($result !== false);
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
    if(isset($params['password']))
    {
      $sql = "INSERT INTO `{$this->mySqlTablePrefix}user` (`id`,`password`,`extra`) VALUES (:id,:password,:extra)";
      $params = array(':id' => $this->owner, ':password' => $params['password'], ':extra' => $params['extra']);
    }
    else
    {
      $sql = "INSERT INTO `{$this->mySqlTablePrefix}user` (`id`,`extra`) VALUES (:id,:extra)";
      $params = array(':id' => $this->owner, ':extra' => $params['extra']);
    }

    $result = $this->db->execute($sql, $params);
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
    $result = $this->db->execute("INSERT INTO `{$this->mySqlTablePrefix}webhook` (id,owner,{$stmt['cols']}) VALUES (:id,:owner,{$stmt['vals']})", array(':id' => $id, ':owner' => $this->owner));
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
      $result = $this->db->one("SELECT * from `{$this->mySqlTablePrefix}admin` WHERE `key`=:key", array(':key' => 'version'));
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
    * Insert albums into the mapping table
    *
    * @param string $id Element id (id of the photo or video)
    * @param string $albums Album IDs to be added
    * @param string $type Element type (photo or video)
    * @return boolean
    */
  private function addAlbumsToElement($id, $albums, $type)
  {
    if(empty($id) || empty($albums) || empty($type))
      return false;

    $hasAlbum = false;
    $sql = "REPLACE INTO `{$this->mySqlTablePrefix}elementAlbum`(`owner`, `type`, `element`, `album`) VALUES";
    foreach($albums as $album)
    {
      if(strlen($album) > 0)
      {
        $sql .= sprintf("('%s', '%s', '%s', '%s'),", $this->_($this->owner), $this->_($type), $this->_($id), $this->_($album));
        $hasAlbum = true;
      }
    }

    if(!$hasAlbum)
      return false;

    $sql = substr($sql, 0, -1);
    $res = $this->db->execute($sql);

    return $res !== false;
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

    $sql = "REPLACE INTO `{$this->mySqlTablePrefix}groupMember`(`owner`, `group`, `email`) VALUES ";
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
    * @param string $groups Groups to be added
    * @param string $type Element type (photo or video)
    * @return boolean
    */
  private function addGroupsToElement($id, $groups, $type)
  {
    if(empty($id) || empty($groups) || empty($type))
      return false;

    $hasGroup = false;
    $sql = "REPLACE INTO `{$this->mySqlTablePrefix}elementGroup`(`owner`, `type`, `element`, `group`) VALUES";
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
    $res = $this->db->execute("REPLACE INTO `{$this->mySqlTablePrefix}elementTag`(`owner`, `type`, `element`, `tag`) VALUES(:owner, :type, :element, :tag)", array(':owner' => $this->owner, ':type' => $type, ':element' => $id, ':tag' => $tag));
    return $res !== false;
  }

  /**
    * Build parts of the photos select query
    *
    * @param array $filters filters used to perform searches
    * @param int $limit number of records to have returned
    * @param offset $offset starting point for records
    * @return array
    */
  private function buildQuery($filters, $limit, $offset, $table)
  {
    // TODO: support logic for multiple conditions
    $from = "FROM `{$this->mySqlTablePrefix}{$table}` ";
    $where = "WHERE `{$this->mySqlTablePrefix}{$table}`.`owner`='{$this->owner}'";
    $groupBy = '';
    $sortBy = 'ORDER BY dateSortByDay DESC';
    if(!empty($filters) && is_array($filters))
    {
      foreach($filters as $name => $value)
      {
        switch($name)
        {
          case 'album':
            $subquery = sprintf("`id` IN (SELECT element FROM `{$this->mySqlTablePrefix}elementAlbum` WHERE `{$this->mySqlTablePrefix}elementAlbum`.`owner`='%s' AND `type`='%s' AND `album`='%s')",
              $this->_($this->owner), 'photo', $value);
            $where = $this->buildWhere($where, $subquery);
            break;
          case 'hash':
            $hash = $this->_($value);
            $where = $this->buildWhere($where, "hash='{$hash}'");
            break;
          case 'ids':
            $ids = (array)explode(',', $value);
            foreach($ids as $k => $v)
              $ids[$k] = $this->_($v);
            $where = $this->buildWhere($where, sprintf("`id` IN ('%s')", implode("','", $ids)));
            break;
          case 'groups':
            if(!is_array($value))
              $value = (array)explode(',', $value);
            foreach($value as $k => $v)
              $value[$k] = $this->_($v);
            $subquery = sprintf("(`id` IN (SELECT element FROM `{$this->mySqlTablePrefix}elementGroup` WHERE `{$this->mySqlTablePrefix}elementGroup`.`owner`='%s' AND `type`='%s' AND `group` IN('%s')) OR permission='1')",
              $this->_($this->owner), 'photo', implode("','", $value));
            $where = $this->buildWhere($where, $subquery);
            break;
          case 'page':
            if($value > 1)
              $offset = intval(($limit * $value) - $limit);
            break;
          case 'permission':
            $where = $this->buildWhere($where, "`permission`='1'");
            break;
          case 'sortBy':
            if($value === 'dateTaken,desc')
              $sortBy = 'ORDER BY dateSortByDay DESC';
            elseif($value === 'dateTaken,asc')
              $sortBy = 'ORDER BY dateSortByDay ASC';
            elseif($value === 'dateUploaded,desc')
              $sortBy = 'ORDER BY dateSortByDay DESC, dateUploaded ASC';
            elseif($value === 'dateUploaded,asc')
              $sortBy = 'ORDER BY dateSortByDay ASC, dateUploaded ASC';
            else
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
              $thisRes = $this->db->all(sprintf("SELECT `element`, `tag` FROM `%selementTag` WHERE `owner`='%s' AND `type`='photo' AND `tag`='%s'", $this->mySqlTablePrefix, $this->owner, $v));
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

            $where = $this->buildWhere($where, sprintf("`%sphoto`.`id` IN('%s')", $this->mySqlTablePrefix, implode("','", array_keys($ids))));
            break;
          case 'type': // type for activity
            $value = $this->_($value);
            $where = $this->buildWhere($where, "`type`='{$value}'");
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
    * Delete albums for an element from the mapping table
    *
    * @param string $id Element id
    * @return boolean
    */
  private function deleteAlbumsFromElement($id)
  {
    $res = $this->db->execute("DELETE FROM `{$this->mySqlTablePrefix}elementAlbum` WHERE `owner`=:owner AND `type`=:type AND `element`=:album", array(':owner' => $this->owner, ':type' => 'photo', ':album' => $id));
    return $res !== false;
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
    $res = $this->db->execute("DELETE FROM `{$this->mySqlTablePrefix}elementGroup` WHERE `owner`=:owner AND `type`=:type AND `element`=:element", array(':owner' => $this->owner, ':type' => $type, ':element' => $id));
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
    $res = $this->db->execute("DELETE FROM `{$this->mySqlTablePrefix}groupMember` WHERE `owner`=:owner AND `group`=:group", array(':owner' => $this->owner, ':group' => $id));
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
    $res = $this->db->execute("DELETE FROM `{$this->mySqlTablePrefix}elementTag` WHERE `owner`=:owner AND `type`=:type AND `element`=:element", array(':owner' => $this->owner, ':type' => $type, ':element' => $id));
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
    $versions = $this->db->all("SELECT `key`,path FROM `{$this->mySqlTablePrefix}photoVersion` WHERE `owner`=:owner AND `id`=:id ",
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

    $raw['cover'] = null;
    if(!empty($raw['extra']))
    {
      $extra = json_decode($raw['extra'], 1);
      if(isset($extra['cover']))
        $raw['cover'] = $extra['cover'];
    }
    if(empty($raw['groups']))
      $raw['groups'] = array();
    else
      $raw['groups'] = (array)explode(',', $raw['groups']);
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

    $versions = $this->getPhotoVersions($photo['id']);
    if($versions && !empty($versions))
    {
      foreach($versions as $version)
      {
        $photo[$version['key']] = $version['path'];
      }
    }

    if(isset($photo['albums']) && strlen($photo['albums']) > 0)
      $photo['albums'] = (array)explode(',', $photo['albums']);
    else
      $photo['albums'] = array();

    if(isset($photo['groups']) && strlen($photo['groups']) > 0)
      $photo['groups'] = (array)explode(',', $photo['groups']);
    else
      $photo['groups'] = array();

    if(isset($photo['tags']) && strlen($photo['tags']) > 0)
      $photo['tags'] = (array)explode(',', $photo['tags']);
    else
      $photo['tags'] = array();

    $exif = (array)json_decode($photo['exif'], 1);
    $extra = (array)json_decode($photo['extra'], 1);
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
  private function normalizeResourceMap($raw)
  {
    if(!empty($raw['resource']))
    {
      $resource = json_decode($raw['resource'], 1);
      foreach($resource as $key => $value)
      {
        if(!isset($raw[$key]))
          $raw[$key] = $value;
      }
    }
    unset($raw['resource']);
    return $raw;
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
      {
        // perform a check to make sure we don't clobber top level elements in $raw. See #853
        if(!isset($raw[$key]))
          $raw[$key] = $value;
      }
    }
    unset($raw['extra'], $raw['password']);
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

  /** Prepare album to store in the database
   */
  private function prepareAlbum($params)
  {
    if(isset($params['extra']))
      $params['extra'] = json_encode($params['extra']);

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
        case 'extraDropboxSource':
        case 'extraFileSystem':
        case 'extraDatabase':
          $extra[$key] = $value;
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
      $bindings[':exif'] = json_encode($exif);
      $paramsOut['exif'] = ':exif';
    }
    if(!empty($extra))
    {
      $bindings[':extra'] = json_encode($extra);
      $paramsOut['extra'] = ':extra';
    }

    if(isset($params['dateTaken']))
    {
      // here we seed a field for the default sort order of day descending and time ascending
      $invHours = str_pad(intval(23-date('H', $params['dateTaken'])), 2, '0', STR_PAD_LEFT);
      $invMins = str_pad(intval(59-date('i', $params['dateTaken'])), 2, '0', STR_PAD_LEFT);
      $invSecs = str_pad(intval(59-date('s', $params['dateTaken'])), 2, '0', STR_PAD_LEFT);
      $bindings[':dateSortByDay'] = sprintf('%s%s%s%s', date('Ymd', $params['dateTaken']), $invHours, $invMins, $invSecs);
      $paramsOut['dateSortByDay'] = ':dateSortByDay';
    }
    $paramsOut['::bindings'] = $bindings;
    return $paramsOut;
  }

  /** Prepare resource map to store in the database
   */
  private function prepareResourceMap($params)
  {
    $resource = array();
    if(isset($params['uri']))
    {
      $resource['uri'] = $params['uri'];
      unset($params['uri']);
    }

    if(isset($params['method']))
    {
      $resource['method'] = $params['method'];
      unset($params['method']);
    }

    if(empty($resource))
      $params['resource'] = '';
    else
      $params['resource'] = json_encode($resource);

    return $params;
  }

  /** Prepare tags to store in the database
   */
  private function prepareTag($params)
  {
    $bindings = array();
    if(!empty($params['id']))
    {
      $bindings[':id'] = $params['id'];
      $params['id'] = ':id';
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
    $ret = array('extra' => '', 'password' => '');
    $extra = array();
    if(isset($params) && is_array($params) && !empty($params))
    {
      foreach($params as $key => $val)
      {
        $prefix = substr($key, 0, 4);
        if($val !== null && ($prefix === 'last' ||  $prefix === 'attr'))
          $extra[$key] = $val;
      }
      $ret['extra'] = json_encode($extra);
      if(isset($params['password']))
        $ret['password'] = $params['password'];
    }
    return $ret;
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
      $result = $this->db->execute("REPLACE INTO {$this->mySqlTablePrefix}photoVersion (`id`, `owner`, `key`, `path`) VALUES(:id, :owner, :key, :value)",
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
        $stmt .= "`{$key}`={$value}";
      else
        $stmt .= sprintf("`%s`='%s'", $key, $this->_($value));
    }
    return $stmt;
  }

  /**
   * Update the mapping table for albums<->photos
   *
   * @param string $id ID of the photo
   * @param array $albums Albums
   * @return string
   */
  private function updateAlbumToPhotoMapping($id, $albums)
  {
    if(!is_array($albums))
      $albums = (array)explode(',', $albums);
    $this->deleteAlbumsFromElement($id, 'photo');
    if(!empty($albums))
      $this->addAlbumsToElement($id, $albums, 'photo');
  }


  /**
   * Update the mapping table for groups<->photos
   *
   * @param string $id ID of the photo
   * @param array $groups Groups
   * @return string
   */
  private function updateGroupToPhotoMapping($id, $groups)
  {
    if(!is_array($groups))
      $groups = (array)explode(',', $groups);
    $this->deleteGroupsFromElement($id, 'photo');
    if(!empty($groups))
      $this->addGroupsToElement($id, $groups, 'photo');

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
