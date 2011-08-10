<?php
/**
 * Interface for the Database models.
 *
 * This defines the interface for any model that wants to connect to a remote database.
 * @author Jaisen Mathai <jaisen@jmathai.com>
 */
interface DatabaseInterface
{
  public function __construct($opts);
  // delete methods can delete or toggle status
  public function deletePhoto($id);
  public function deleteAction($id);
  // get methods read
  public function getPhotoNextPrevious($id);
  public function getPhoto($id);
  public function getPhotoWithActions($id);
  public function getPhotos($filter = array(), $limit, $offset = null);
  public function getUser();
  public function getTags($filter = array());
  // post methods update
  public function postPhoto($id, $params);
  public function postUser($id, $params);
  public function postTag($id, $params);
  public function postTags($params);
  public function postTagsCounter($params);
  // put methods create but do not update
  public function putAction($id, $params);
  public function putPhoto($id, $params);
  public function putUser($id, $params);
  public function putTag($id, $params);
  public function initialize();
}

/**
  * The public interface for instantiating a database obect.
  * This returns the appropriate type of object by reading the config.
  * Accepts a set of params that must include a type and targetType
  *
  * @param string $type Optional type parameter which defines the type of database.
  * @param array $opts Options which can be used by the database adapter.
  * @return object A database object that implements DatabaseInterface
  */
function getDb(/*$type, $opts*/)
{
  static $database, $type, $opts;
  if(func_num_args() == 2)
  {
    $type = func_get_arg(0);
    $opts = func_get_arg(1);
  }
  // load configs only once
  if(!$type)
    $type = getConfig()->get('systems')->database;
  if(!$opts)
    $opts = getConfig()->get('credentials');

  if($database)
    return $database;

  switch($type)
  {
    case 'SimpleDb':
      $database = new DatabaseSimpleDb($opts);
      break;
    case 'MySql':
      $database = new DatabaseMySql($opts);
      break;
  }
  
  if($database)
    return $database;

  throw new Exception(404, "DataProvider {$type} does not exist");
}
