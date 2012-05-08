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
  public function diagnostics();
  // delete methods can delete or toggle status
  public function deleteAction($id);
  public function deleteCredential($id);
  public function deleteGroup($id);
  public function deletePhoto($photo);
  public function deletePhotoVersions($photo);
  public function deleteTag($id);
  public function deleteWebhook($id);
  // get methods read
  public function getAction($id);
  public function getActivities($filters = array(), $limit = null);
  public function getActivity($id);
  public function getAlbum($id, $email);
  public function getAlbumElements($id);
  public function getAlbums($email);
  public function getCredential($id);
  public function getCredentialByUserToken($userToken);
  public function getCredentials();
  public function getPhotoNextPrevious($id);
  public function getGroup($id = null);
  public function getGroups($email = null);
  public function getPhoto($id);
  public function getPhotoWithActions($id);
  public function getPhotos($filters = array(), $limit = null, $offset = null);
  public function getUser($owner = null);
  public function getUserByEmailAndPassword($email = null, $password = null);
  public function getTag($tag);
  public function getTags($filters = array());
  public function getWebhook($id);
  public function getWebhooks($topic = null);
  // upgrade
  public function identity();
  public function executeScript($file, $database);
  // post methods update
  public function postAlbumAdd($albumId, $type, $elementIds);
  public function postAlbumRemove($albumId, $type, $elementIds);
  public function postCredential($id, $params);
  public function postGroup($id, $params);
  public function postPhoto($id, $params);
  public function postUser($params);
  public function postTag($id, $params);
  public function postTags($params);
  public function postWebhook($id, $params);
  // put methods create but do not update
  public function putGroup($id, $params);
  public function putAction($id, $params);
  public function putActivity($id, $params);
  public function putAlbum($id, $params);
  public function putCredential($id, $params);
  public function putPhoto($id, $params);
  public function putUser($params);
  public function putTag($id, $params);
  public function putWebhook($id, $params);
  // general methods
  public function initialize($isEditMode);
}

if(!function_exists('getDb'))
{
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

    $systems = getConfig()->get('systems');
    // load configs only once
    if($systems !== null)
      $type = $systems->database;

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
}
