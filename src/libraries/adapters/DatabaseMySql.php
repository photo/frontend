<?php
/** 
 * MySQL implementation
 *
 * This class defines the functionality defined by DatabaseInterface for a MySQL database.
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
    $ret['previous'] = $photo_prev;
    $ret['next'] = $photo_next;

    return $ret;
  }

  public function getPhoto($id)
  {
    $photo = getDabase()->one("SELECT * FROM photo WHERE id=:id", array(':id' => $id));
    if(!isset($photo))
      return false;
    return $photo;
  }

  public function getPhotoWithActions($id)
  {
    $photo = $this->getPhoto($id);
    if($photo) 
    {
      $actions = getDatabase()->all("SELECT * FROM action WHERE targetType='photo' AND targetId=:id",
      	       array(':id' => $id));
      if($actions)
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
      do
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
      }while($currentPage <= $value);
    }

    $photos = getDatabase()->all("SELECT * FROM photo {$where} {$sortBy} LIMIT {$limit}");
    if(!$photo)
      return false;
    $result = getDatabase()->one("SELECT COUNT(*) FROM photo {$where}");
    if($result)
    {
      $photos[0]['totalRows'] = intval($result);
    }

    return $photos;
  }

  public function getTags($filter = array())
  {
    $tags = getDatabase()->all("SELECT * FROM tag WHERE `count` IS NOT NULL AND `count` > '0' AND id IS NOT NULL ORDER BY id");
    return $tags;
  }

  // post methods update
  public function postPhoto($id, $params)
  {
  }
  public function postUser($id, $params)
  {
  }
  public function postTag($id, $params)
  {
    if(!isset($params['count']))
      $count = 0;
    else
      $count = max(0, intval($params['count']));
    return getDatabase()->execute("INSERT INTO tag (id, count) VALUES (:id, :count)",
            array(':id' => $id, ':count' => $count)) == 1;
  }

  public function postTags($params)
  {
    foreach($params as $tagObj)
    {
      $tag = $tagObj['id'];
      unset($tagObj['id']);
      $res = $this->postTag($tag, $params);
    }
    return $res;
  }

  public function postTagsCounter($params)
  {
  }
  // put methods create but do not update
  public function putAction($id, $params)
  {
  }
  public function putPhoto($id, $params)
  {
  }
  public function putUser($id, $params)
  {
    foreach($params as $key => $value)
    {
      if(isset($stmt)) {
        $stmt .= ",";
      }
      $stmt .= "{$key}={$value}";
    }
    $result = getDatabase()->execute("UPDATE user SET {$stmt} WHERE id=:id", array(':id' => $id));
    return ($result == 1);
  }

  public function putTag($id, $params)
  {
    return $this->postTag($id, $params);
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

}

?>