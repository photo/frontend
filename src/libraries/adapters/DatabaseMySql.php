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
  private $mysqlDb, $mySqlHost, $mySqlUser, $mySqlPassword;

  /**
    * Constructor
    *
    * @param array $opts information for MySql
    * @return void 
    */
  public function __construct($opts)
  {
    $mysql = getConfig()->get('mysql');
    EpiDatabase::employ('mysql', $mysql->mySqlDb, 
                        $mysql->mySqlHost, $mysql->mySqlUser, $mysql->mySqlPassword);
  }

  /**
    * Delete an action from the database
    *
    * @param string $id ID of the action to delete
    * @return boolean 
    */
  public function deleteAction($id)
  {
    $res = getDatabase()->execute("DELETE FROM action WHERE id=:id", array(':id' => $id));
    return ($res == 1);
  }

  /**
    * Delete a photo from the database
    *
    * @param string $id ID of the photo to delete
    * @return boolean 
    */
  public function deletePhoto($id)
  {
    $res = getDatabase()->execute("DELETE FROM photo WHERE id=:id", array(':id' => $id));
    return ($res == 1);
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

    $photo_prev = getDatabase()->one("SELECT * FROM photo WHERE dateTaken> :dateTaken AND dateTaken IS NOT NULL ORDER BY dateTaken ASC LIMIT 1", array(':dateTaken' => $photo['dateTaken']));
    $photo_next = getDatabase()->one("SELECT * FROM photo WHERE dateTaken< :dateTaken AND dateTaken IS NOT NULL ORDER BY dateTaken DESC LIMIT 1", array(':dateTaken' => $photo['dateTaken']));

    $ret = array();
    if($photo_prev)
      $ret['previous'] = self::normalizePhoto($photo_prev);
    if($photo_next)
    $ret['next'] = self::normalizePhoto($photo_next);

    return $ret;
  }

  /**
    * Get a photo specified by $id
    *
    * @param string $id ID of the photo to retrieve
    * @return mixed Array on success, FALSE on failure 
    */
  public function getPhoto($id)
  {
    $photo = getDatabase()->one("SELECT * FROM photo WHERE id=:id", array(':id' => $id));
    if(empty($photo))
      return false;
    return self::normalizePhoto($photo);
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
      $actions = getDatabase()->all("SELECT * FROM action WHERE targetType='photo' AND targetId=:id",
      	       array(':id' => $id));
      if(!empty($actions))
      {
        foreach($actions as $action)
           $photo['actions'][] = $action;          
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
  public function getPhotos($filters = array(), $limit, $offset = null)
  {
    // TODO: support logic for multiple conditions
    $where = '';
    $sortBy = 'ORDER BY dateTaken DESC';
    if(!empty($filters) && is_array($filters))
    {
      foreach($filters as $name => $value)
      {
        switch($name)
        {
          case 'tags':
            if(!is_array($value))
              $value = (array)explode(',', $value);
            $where = $this->buildWhere($where, "tags IN('" . implode("','", $value) . "')");
            break;
          case 'page':
            if($value > 1)
            {
              $value = min($value, 40); // 40 pages at max of 2,500 recursion limit means 100k photos
              $offset = ($limit * $value) - $limit;
            }
            break;
          case 'sortBy':
            $sortBy = 'ORDER BY ' . str_replace(',', ' ', $value);
            $field = substr($value, 0, strpos($value, ','));
            $where = $this->buildWhere($where, "{$field} is not null");
            break;
        }
      }
    }

    $offset_sql = '';
    if($offset)
      $offset_sql = "OFFSET {$offset}";
    $photos = getDatabase()->all("SELECT * FROM photo {$where} {$sortBy} LIMIT {$limit} {$offset_sql}");
    if(empty($photos))
      return false;
    for($i = 0; $i < count($photos); $i++)
    {
      $photos[$i] = self::normalizePhoto($photos[$i]);
    }
    $result = getDatabase()->one("SELECT COUNT(*) FROM photo {$where}");
    if(!empty($result))
    {
      $photos[0]['totalRows'] = $result['COUNT(*)'];
    }

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
    $tag = getDatabase()->one('SELECT * FROM tag WHERE id=:id', array(':id' => $tag));
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
    $tags = getDatabase()->all("SELECT * FROM tag WHERE `count` IS NOT NULL AND `count` > '0' AND id IS NOT NULL ORDER BY id");
    foreach($tags as $key => $tag)
    {
      // TODO this should be in the normalize method
      if($tag['params'])
        $tags[$key] = array_merge($tag, json_decode($tag['params'], 1));
      unset($tags[$key]['params']);
    }
    return $tags;
  }

  /**
    * Get the user record entry.
    *
    * @return mixed Array on success, NULL if user record is empty, FALSE on error 
    */
  public function getUser()
  {
    $res = getDatabase()->one("SELECT * FROM user WHERE id='1'");
    if($res)
    {
      return self::normalizeUser($res);
    }
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
    unset($params['id']);

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
      // TODO, this doesn't use named parameters via PDO, should be fixed -- jmathai
      $stmt = self::sqlUpdateExplode($params);
      $res = getDatabase()->execute("UPDATE photo SET {$stmt} WHERE id=:id", array(':id' => $id));
    }

    if(!empty($versions))
      $resVersions = $this->postVersions($id, $versions);

    return (isset($res) && $res == 1) || (isset($resVersions) && $resVersions);
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
      $params['id'] = $id;

    $stmtIns = self::sqlInsertExplode($params);
    $stmtUpd = self::sqlUpdateExplode($params);

    $result = getDatabase()->execute("INSERT INTO tag ({$stmtIns['cols']}) VALUES ({$stmtIns['vals']}) ON DUPLICATE KEY UPDATE {$stmtUpd}");
    return true;
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
    foreach($params as $tagObj)
    {
      $res = $this->postTag($tagObj['id'], $tagObj);
    }
    return $res;
  }

 /**
    * Update counts for multiple tags by incrementing or decrementing.
    * The $params should include the tag in the `id` field.
    * [{id: tag1, count:10, longitude:12.34, latitude:56.78},...]
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
    $res = getDatabase()->all("SELECT * FROM tag  WHERE id IN ('" . implode("','", $justTags) . "')");
    if(!empty($res))
    {
      foreach($res as $val)
        $tagsFromDb[] = self::normalizeTag($val);
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
    $stmt = self::sqlUpdateExplode($params);
    $res = getDatabase()->execute("UPDATE user SET {$stmt} WHERE id=:id", array(':id' => $id));
    return $res = 1;
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
    $stmt = self::sqlInsertExplode($params);
    $result = getDatabase()->execute("INSERT INTO action (id,{$stmt['cols']}) VALUES (:id,{$stmt['vals']})", array(':id' => $id));
    return true;
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
    $stmt = self::sqlInsertExplode($params);
    $result = getDatabase()->execute("INSERT INTO photo ({$stmt['cols']}) VALUES ({$stmt['vals']})");
    return true;
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
      $count = 0;
    else
      $count = max(0, intval($params['count']));
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
    $stmt = self::sqlInsertExplode($params);
    $result = getDatabase()->execute("INSERT INTO user (id,{$stmt['cols']}) VALUES (:id,{$stmt['vals']})", array(':id' => $id));
    return true;
  }

  /**
    * Initialize the database by creating the database and tables needed.
    * This is called from the Setup controller.
    *
    * @return boolean
    */
  public function initialize()
  {
    // TODO create the database and tables
    return true;
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
    * Get all the versions of a given photo
    * TODO this can be eliminated once versions are in the json field
    *
    * @param string $id Id of the photo of which to get versions of
    * @return array Array of versions
    */
  private function getPhotoVersions($id)
  {
    $versions = getDatabase()->all("SELECT `key`,path FROM photoVersion WHERE id=:id",
                 array(':id' => $id));
    if(empty($versions))
      return false;
    return $versions;
  }

  /**
   * Explode params associative array into SQL update statement lists
   * Return a string
   * TODO, have this work with PDO named parameters
   */
  private function sqlUpdateExplode($params)
  {
    $stmt = '';
    foreach($params as $key => $value)
    {
      if(!empty($stmt))
        $stmt .= ",";
      $stmt .= "{$key}='{$value}'";
    }
    return $stmt;
  }

  /**
   * Explode params associative array into SQL insert statement lists
   * Return an array with 'cols' and 'vals'
   */
  private function sqlInsertExplode($params)
  {
    $stmt = array('cols' => '', 'vals' => '');
    foreach($params as $key => $value)
    {
      if(!empty($stmt['cols']))
        $stmt['cols'] .= ",";
      if(!empty($stmt['vals']))
        $stmt['vals'] .= ",";
      $stmt['cols'] .= $key;
      $stmt['vals'] .= "'{$value}'";
    }
    return $stmt;
  }

  /**
    * Normalizes data from MySql into schema definition
    *
    * @param SimpleXMLObject $raw An action from SimpleDb in SimpleXML.
    * @return array
    */
  private function normalizeAction($raw)
  {
    // TODO shouldn't we require and use this method?
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
    $photo['appId'] = getConfig()->get('application')->appId;

    $versions = $this->getPhotoVersions($photo['id']);
    if($versions)
    {
      foreach($versions as $version)
      {
        $photo[$version['key']] =  $version['path'];
        $photo[$version['key']] = sprintf('http://%s%s', $photo['host'], $version['path']);
      }
    }
    $photo['tags'] = explode(",", $photo['tags']);
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

  /**
    * Normalizes data from simpleDb into schema definition
    * TODO this should eventually translate the json field
    *
    * @param SimpleXMLObject $raw A tag from SimpleDb in SimpleXML.
    * @return array
    */
  private function normalizeUser($raw)
  {
    return $raw;
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
    $params['id'] = $id;
    if(isset($params['tags']) && is_array($params['tags']))
      $params['tags'] = implode(',', $params['tags']);

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
      getDatabase()->execute("INSERT INTO photoVersion (id, `key`, path) VALUES('{$id}', '{$key}', '{$value}')");
    }
    // TODO, what type of return value should we have here -- jmathai
    return true;
  }
}
