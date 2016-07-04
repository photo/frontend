<?php
// for unit tests
//  needed here and in dependencies since this file is included direclty for unit tests
if(!isset($pathsObj))
  $pathsObj = getConfig()->get('paths');

function openphoto_autoloader($name)
{
  if(!defined('IS_UNIT_TEST')) // application
    $pathsObj = getConfig()->get('paths');
  else // unit tests
    global $pathsObj;

  if(file_exists($file = sprintf('%s/%s.php', $pathsObj->controllers, $name)))
    include $file;
  elseif(file_exists($file = sprintf('%s/%s.php', $pathsObj->adapters, $name)))
    include $file;
  elseif(file_exists($file = sprintf('%s/%s.php', $pathsObj->models, $name)))
    include $file;
  elseif(file_exists($file = sprintf('%s/%s/%s.php', $pathsObj->external, $name, $name)))
    include $file;
}

/**
  * The asset pipeline object.
  * @return object An asset pipeline object.
  */
function getAssetPipeline($new = false)
{
  if($new)
    return new AssetPipeline;

  static $assetPipeline;
  if($assetPipeline)
    return $assetPipeline;

  $assetPipeline = new AssetPipeline;
  return $assetPipeline;
}

/**
  * The authentication object.
  * @return object An authentication object.
  */
function getAuthentication()
{
  static $authentication;
  if(!$authentication)
    $authentication = new Authentication;

  return $authentication;
}

/**
  * The credential object.
  * @return object An credential object.
  */
function getCredential()
{
  static $credential;
  if(!$credential)
    $credential = new Credential;

  return $credential;
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

  $systems = getConfig()->get('systems');
  // load configs only once
  if($systems !== null)
    $type = $systems->database;

  switch($type)
  {
    case 'MySql':
      $database = new DatabaseMySql();
      break;
  }

  if($database)
    return $database;

  throw new Exception("DataProvider {$type} does not exist", 404);
}

/**
  * The public interface for instantiating a file system obect.
  * This returns the appropriate type of object by reading the config.
  * Accepts a set of params that must include a type and targetType
  *
  * @param string $type Optional type parameter which defines the type of file system.
  * @param boolean $force Force the return of a new FS object without "caching"
  * @return object A file system object that implements FileSystemInterface
  */
function getFs(/*$type, $useCache*/)
{
  static $filesystems, $type;
  // load configs only once
  if(!$type)
    $type = getConfig()->get('systems')->fileSystem;

  if(func_num_args() >= 1)
    $type = func_get_arg(0);

  $useCache = true;
  if(func_num_args() >= 2)
    $useCache = func_get_arg(1);


  if($useCache === true && $filesystems[$type])
    return $filesystems[$type];

  switch($type)
  {
    case 'Local':
      $fs = new FileSystemLocal();
      break;
    case 'LocalDropbox':
      $fs = new FileSystemLocalDropbox();
      break;
    case 'LocalAppDotNet':
      $fs = new FileSystemLocalAppDotNet();
      break;
    case 'S3':
      $fs = new FileSystemS3();
      break;
    case 'S3AppDotNet':
      $fs = new FileSystemS3AppDotNet();
      break;
    case 'S3ArchiveOrg':
      $fs = new FileSystemS3ArchiveOrg();
      break;
    case 'S3Box':
      $fs = new FileSystemS3Box();
      break;
    case 'S3CX':
      $fs = new FileSystemS3CX();
      break;
    case 'S3Dropbox':
      $fs = new FileSystemS3Dropbox();
      break;
    case 'DreamObjects':
      $fs = new FileSystemDreamObjects();
      break;
    default:
      throw new Exception("FileSystem Provider {$type} does not exist", 404);
      break;
  }

  if($useCache)
    $filesystems[$type] = $fs;

  return $fs;
}

/**
  * The public interface for instantiating an image obect.
  * This returns the appropriate type of object by reading the config.
  * Accepts a set of params that must include a type and targetType
  *
  * @return object An image object that implements ImageInterface
  */
function getImage()
{
  static $type;
  $modules = getConfig()->get('modules');
  if(!$type && isset($modules->image))
    $type = $modules->image;

  try
  {
    switch($type)
    {
      case 'GraphicsMagick':
        return new ImageGraphicsMagick();
        break;
      case 'ImageMagick':
        return new ImageImageMagick();
        break;
      case 'GD':
        return new ImageGD();
        break;
    }
  }
  catch(OPInvalidImageException $e)
  {
    getLogger()->warn("Invalid image exception thrown for {$type}");
    return false;
  }
}

/**
  * The public interface for authenticating a user.
  *
  * @param string $type Optional type parameter which defines the type of authentication provider.
  * @return object A login object that implements LoginInterface
  */
function getLogin($provider)
{
  switch($provider)
  {
    case 'self':
      return new LoginSelf;
      break;
    case 'facebook':
      return new LoginFacebook;
      break;
    default:
      return new LoginSelf;
      break;
  }
}

/**
  * The public interface for instantiating an maps object.
  * This returns the appropriate type of object by reading the config.
  * Accepts a set of params that must include a type and targetType
  *
  * @return object A maps object that implements MapsInterface
  */
function getMap()
{
  static $type;
  $map = getConfig()->get('map');
  if(!$type && isset($map->service))
    $type = $map->service;

  try
  {
    switch($type)
    {
      case 'osm':
        return new MapOsm();
        break;
      case 'google':
        return new MapGoogle();
        break;
    }
  }
  catch(OPInvalidMapException $e)
  {
    getLogger()->warn("Invalid mapping exception thrown for {$type}");
    return false;
  }
}

/**
  * The public interface for instantiating a plugin obect.
  *
  * @return object A plugin object
  */
function getPlugin()
{
  static $plugin;
  if($plugin)
    return $plugin;

  $plugin = new Plugin;
  return $plugin;
}

/**
  * The public interface for instantiating a theme obect.
  *
  * @return object A theme object
  */
function getTheme($singleton = true)
{
  static $theme;
  if($singleton)
  {
    if (!$theme)
      $theme = new Theme();
    return $theme;
  }

  return new Theme();
}

/**
  * The public interface for instantiating an upgrade object.
  *
  * @return object An upgrade object
  */
function getUpgrade()
{
  static $upgrade;
  if(!$upgrade)
    $upgrade = new Upgrade;

  return $upgrade;
}

/**
  * The user config object
  *
  * @return object A user config object
  */
function getUserConfig()
{
  static $userConfig;
  if($userConfig)
    return $userConfig;

  $userConfig = new UserConfig;
  return $userConfig;
}

/**
  * The utlity object
  *
  * @return object A utility object
  */
function getUtility()
{
  static $utility;
  if($utility)
    return $utility;

  $utility = new Utility;
  return $utility;
}
