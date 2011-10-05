<?php
/**
 * Interface for the Database models.
 *
 * This defines the interface for any model that wants to connect to a remote database.
 * @author Jaisen Mathai <jaisen@jmathai.com>
 */
interface DatabaseInterface
{
  public function __construct();
  //
  public function errors();
  // delete methods can delete or toggle status
  public function deleteAction($id);
  public function deleteCredential($id);
  public function deleteGroup($id);
  public function deletePhoto($id);
  public function deleteWebhook($id);
  // get methods read
  public function getCredential($id);
  public function getCredentials();
  public function getPhotoNextPrevious($id);
  public function getGroup($id = null);
  public function getGroups($email = null);
  public function getPhoto($id);
  public function getPhotoWithActions($id);
  public function getPhotos($filter = array(), $limit, $offset = null);
  public function getUser();
  public function getTag($tag);
  public function getTags($filter = array());
  public function getWebhook($id);
  public function getWebhooks($topic = null);
  // post methods update
  public function postCredential($id, $params);
  public function postGroup($id, $params);
  public function postPhoto($id, $params);
  public function postUser($id, $params);
  public function postTag($id, $params);
  public function postTags($params);
  public function postTagsCounter($params);
  public function postWebhook($id, $params);
  // put methods create but do not update
  public function putGroup($id, $params);
  public function putAction($id, $params);
  public function putCredential($id, $params);
  public function putPhoto($id, $params);
  public function putUser($id, $params);
  public function putTag($id, $params);
  public function putWebhook($id, $params);
  // general methods
  public function initialize();
}

/**
  * The public interface for instantiating a database obect.
  * This returns the appropriate type of object by reading the config.
  * Accepts a set of params that must include a type and targetType
  *
  * @param string $type Optional type parameter which defines the type of database.
  * @return object A database object that implements DatabaseInterface
  */
function getDb(/*$type*/)
{
  static $database, $type;
  if($database)
    return $database;

  if(func_num_args() == 1)
    $type = func_get_arg(0);

  // load configs only once
  if(!$type)
    $type = getConfig()->get('systems')->database;

  switch($type)
  {
    case 'SimpleDb':
      $database = new DatabaseSimpleDb();
      break;
    case 'MySql':
      $database = new DatabaseMySql();
      break;
  }

  if($database)
    return $database;

  throw new Exception("DataProvider {$type} does not exist", 404);
}
