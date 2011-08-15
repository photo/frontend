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
  public function __construct($opts)
  {
    $mysql = getConfig()->get('mysql');
    EpiDatabase::employ('mysql', $mysql->mySqlDb, 
                        $mysql->mySqlHost, $mysql->mySqlUser, $mysql->mySqlPassword);
  }

  // delete methods can delete or toggle status
  public function deletePhoto($id)
  {
    $res = getDatabase()->execute("DELETE FROM photo WHERE id=:id", array(':id' => $id));
    return ($res == 1);
  }

  public function deleteAction($id)
  {
    $res = getDatabase()->execute("DELETE FROM action WHERE id=:id", array(':id' => $id));
    return ($res == 1);
  }

  // get methods read
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

  // get all version for a photo
  // this is for the fields "pathNxN"
  private function getPhotoVersions($id)
  {
    $version = getDatabase()->all("SELECT `key`,path FROM photoVersion WHERE id=:id",
                 array(':id' => $id));
    if(empty($version))
      return false;
    return $version;
  }

  public function getPhoto($id)
  {
    $photo = getDatabase()->one("SELECT * FROM photo WHERE id=:id", array(':id' => $id));
    if(empty($photo))
      return false;
    return self::normalizePhoto($photo);
  }

  public function getPhotoWithActions($id)
  {
    $photo = $this->getPhoto($id);
    if($photo) 
    {
      $actions = getDatabase()->all("SELECT * FROM action WHERE targetType='photo' AND targetId=:id",
      	       array(':id' => $id));
      if(!empty($actions))
      {
        foreach($actions as $action)
        {
           $photo['actions'] = array();
	   $action['appId'] = getConfig()->get('application')->appId;
	   $photo['actions'][] = $action;          
        }
      }
    }
    return $photo;
  }

  public function getPhotos($filter = array(), $limit, $offset = null)
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

    if(!empty($offset))
    {
      $iterator = max(1, intval($offset - 1));
      $nextToken = null;
      $currentPage = 1;
      $thisLimit = min($iterator, $offset);
      /*do
      {
      // FIXME FIXME FIXME
        //$res = mysql_query("SELECT * FROM `photo` {$where} {$sortBy} LIMIT {$iterator}",
	//                   $this->db);
	// todo deal with pages
        //if(!$res->body->SelectResult->NextToken)
        //  break;

        //$nextToken = $res->body->SelectResult->NextToken;
        //$params['NextToken'] = $nextToken;
        $currentPage++;
      }while($currentPage <= $value);*/
    }

    $photos = getDatabase()->all("SELECT * FROM photo {$where} {$sortBy} LIMIT {$limit}");
    if(empty($photos))
      return false;
    for($i = 0; $i < count($photos); $i++)
    {
      $photos[$i] = self::normalizePhoto($photos[$i]);
    }
    $result = getDatabase()->one("SELECT COUNT(*) FROM photo {$where}");
    if(!empty($result))
    {
      $photos[0]['totalRows'] = intval($result);
    }

    return $photos;
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

  private function postVersions($id, $versions)
  {
    foreach($versions as $key => $value)
    {
      // TODO this is gonna fail if we already have the version
      getDatabase()->execute("INSERT INTO photoVersion (id, `key`, path) VALUES('{$id}', '{$key}', '{$value}')");
    }
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
    return $tags;
  }

  // post methods update
  public function postPhoto($id, $params)
  {
    $params = self::preparePhoto($id, $params);

    foreach($params as $key => $val)
    {
      if(preg_match('/^path\d+x\d+/', $key))
      {
        $versions[$key] = $val;
	unset($params[$key]);
      }
    }

    $stmt = self::sqlUpdateExplode($params);
    $res = getDatabase()->execute("UPDATE photo SET {$stmt} WHERE id=:id", array(':id' => $id));
    if(!empty($versions))
    {
      $this->postVersions($id, $versions);
    }
    return $res == 1;
  }

  public function postUser($id, $params)
  {
    $stmt = self::sqlUpdateExplode($params);
    $res = getDatabase()->execute("UPDATE user SET {$stmt} WHERE id=:id", array(':id' => $id));
    return $res = 1;
  }

  public function postTag($id, $params)
  {
    if(!isset($params['count']))
      $count = 0;
    else
      $count = max(0, intval($params['count']));
    $res = getDatabase()->execute("UPDATE tag SET count=:count WHERE id=:id", array(':id' => $id, ':count' => $count));
    // there was no update. Insert
    if($res == 0)
      $this->putTag($id, $params);
    return true;
  }

  public function postTags($params)
  {
    foreach($params as $tagObj)
    {
      $res = $this->postTag($tagObj[id], $tagObj);
    }
    return $res;
  }

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

  // put methods create but do not update
  public function putAction($id, $params)
  {
    $stmt = self::sqlInsertExplode($params);
    $result = getDatabase()->execute("INSERT INTO action (id,{$stmt['cols']}) VALUES (:id,{$stmt['vals']})", array(':id' => $id));
    return true;
  }

  public function putPhoto($id, $params)
  {
    $params = self::preparePhoto($id, $params);
    $stmt = self::sqlInsertExplode($params);
    $result = getDatabase()->execute("INSERT INTO photo ({$stmt['cols']}) VALUES ({$stmt['vals']})");
    return true;
  }

  public function putUser($id, $params)
  {
    $stmt = self::sqlInsertExplode($params);
    $result = getDatabase()->execute("INSERT INTO user (id,{$stmt['cols']}) VALUES (:id,{$stmt['vals']})", array(':id' => $id));
    return true;
  }

  public function putTag($id, $params)
  {
    if(!isset($params[id])) 
    {
      $params[id] = $id;
    }
    $stmt = self::sqlInsertExplode($params);
    $result = getDatabase()->execute("INSERT INTO tag ({$stmt['cols']}) VALUES ({$stmt['vals']})");
    return true;
  }

  public function initialize()
  {
    // create the database
    return true;
  }

  /**
    * Utility function to help build the WHERE clause for SELECT statements.
    * (Taken from DatabaseSimpleDb)
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
   * Explode params associative array into SQL update statement lists
   * Return a string
   */
  private function sqlUpdateExplode($params)
  {
    $stmt = '';
    foreach($params as $key => $value)
    {
      if(!empty($stmt)) {
        $stmt .= ",";
      }
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
    *
    */
  private function normalizeTag($raw)
  {
    return $raw;
  }


  /**
    *
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
   * Finish loading a photo. 
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
}
