<?php
/** 
 * MySQL implementation
 */
class DatabaseMySql implements DatabaseInterface
{
  private $db;

  private function sanitize($s)
  {
    return mysql_real_escape_string($s);
  }

  public function __construct($opts)
  {
    $mysql = getConfig()->get('mysql');
    $this->db = mysql_connect($mysql->mySqlHost, $mysql->mySqlUser, $mysql->mySqlPassword);
    mysql_select_db(getConfig()->get('mysql')->mySqlDb, $this->db);
  }

  // delete methods can delete or toggle status
  public function deletePhoto($id)
  {
    $query = "DELETE FROM photos WHERE id=`" . self::sanitize($id) . "`;";
    return mysql_query($query, $this->db);
  }

  public function deleteAction($id)
  {
    $query = "DELETE FROM actions WHERE id=`" . self::sanitize($id) . "`;";
    return mysql_query($query, $this->db);
  }

  // get methods read
  public function getPhotoNextPrevious($id)
  {
    $photo = $this->getPhoto($id);
    if(!$photo)
      return false;

    $query = "SELECT * FROM photos WHERE dateTaken> '" 
             . self::sanitize($photo['dateTaken']) 
             . "' AND dateTaken IS NOT NULL ORDER BY dateTaken ASC LIMIT 1";
    $result = mysql_query($query, $this->db);
    if($result) {
      $photo_prev = mysql_fetch_object($result);
    }
    $query = "SELECT * FROM photos WHERE dateTaken< '" 
             . self::sanitize($photo['dateTaken']) 
             . "' AND dateTaken IS NOT NULL ORDER BY dateTaken DESC LIMIT 1";
    $result = mysql_query($query, $this->db);
    if($result) {
      $photo_next = mysql_fetch_object($result);
    }
    
    
    $ret = array();
    $ret['previous'] = $photo_prev;
    $ret['next'] = $photo_next;

    return $ret;
  }

  public function getPhoto($id)
  {
    $query = "SELECT * FROM photos WHERE id=`" . self::sanitize($id) . "`'";
    $result = mysql_query($query, $this->db);
    if($result) {
      $photo = mysql_fetch_object($result);
      return $photo;
    }
    return false;
  }

  public function getPhotoWithActions($id)
  {
    $photo = $this->getPhoto($id);
    if($photo) {
      $query = "SELECT * FROM actions WHERE targetType='photo' AND targetId=`"
      	       . self::sanitize($id) . "`;";
      $result = mysql_query($query, $this->db);
      if($result) {
        $photo['actions'] = array();
        while($action = mysql_fetch_object($result)) {
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
        $res = mysql_query("SELECT * FROM `photos` {$where} {$sortBy} LIMIT {$iterator}",
	                   $this->db);
	// todo deal with pages
        //if(!$res->body->SelectResult->NextToken)
        //  break;

        //$nextToken = $res->body->SelectResult->NextToken;
        //$params['NextToken'] = $nextToken;
        $currentPage++;
      }while($currentPage <= $value);
    }

    $photos = array();

    $query = "SELECT * FROM photos {$where} {$sortBy} LIMIT {$limit}";
    $response = mysql_query($query, $this->db);
    if($response)
    {
      while($photo = mysql_fetch_object($result))
      {
        $photos[] = $photo;
      }
    }

    $query = "SELECT COUNT(*) FROM photos {$where}";
    $response = mysql_query($query, $this->db);
    if($response)
    {
      $photos[0]['totalRows'] = intval(mysql_fetch_field($result));
    }

    return $photos;
  }

  public function getTags($filter = array())
  {
    $query = "SELECT * FROM tags WHERE `count` IS NOT NULL AND `count` > '0' "
    	     . "AND id IS NOT NULL ORDER BY id";
    $tags = array();
    $result = mysql_query($query, $this->db);
    if($result) {
      while($tag = mysql_fetch_object($result)) {
        $tags[] = $tag;
      }
    }
    else {
      return null;
    }
    return false;
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
    $query = "INSERT INTO tags (id, count) VALUES ($id, $count);";
    return mysql_query($query, $this->db);
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
  }
  public function putTag($id, $params)
  {
  }
  public function initialize()
  {
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