<?php
/**
  * Setup controller for HTML endpoints
  * This controls the setup flow when the software is first installed.
  * The main purpose of this flow is to generate settings.ini.
  *
  * @author Jaisen Mathai <jaisen@jmathai.com>
  */
class SetupController
{
  /**
    * Returns the main setup screen markup.
    *
    * @return string HTML
    */
  public static function setup()
  {
    $step = 0;
    if(isset($_GET['step']))
      $step = intval($_GET['step']);

    $imageLibs = array();
    if(class_exists('Imagick'))
      $imageLibs['ImageMagick'] = 'ImageMagick';
    if(class_exists('Gmagick'))
      $imageLibs['GraphicsMagick'] = 'GraphicsMagick';
    if(extension_loaded('gd') && function_exists('gd_info'))
      $imageLibs['GD'] = 'GD';

    $permissionCheck = self::verifyRequirements($imageLibs);
    if($permissionCheck !== true)
    {
      getTemplate()->display('blank.php', array('body' => getTemplate()->get('setupRequirements.php', array('errors' => $permissionCheck))));
      return;
    }

    $params = array('imageLibs' => $imageLibs, 'appId' => $_SERVER['HTTP_HOST'], 'step' => $step);
    $body = getTheme()->get('setup.php', $params);
    getTheme()->display('template.php', array('body' => $body, 'page' => 'setup'/* do not use inline js, 'js' => getTheme()->get('js/setup.js.php'*/));
  }

  /**
    * Posts the setup values from the form to store and generate settings.ini.
    *
    * @return void HTTP redirect
    */
  public static function setupPost()
  {
    $fs_type = $_POST['fileSystem'];
    $db_type = $_POST['database'];

    $needs_aws = ($fs_type === 'S3') || ($db_type === 'SimpleDb');

    $opts = new stdClass;
    if($needs_aws)
    {
      $opts->awsKey = $_POST['awsKey'];
      $opts->awsSecret = $_POST['awsSecret'];
    }
    getConfig()->set('credentials', $opts);
    if($needs_aws)
    {
      $aws = new stdClass;
      $aws->s3BucketName = $_POST['s3Bucket'];
      $aws->simpleDbDomain = $_POST['simpleDbDomain'];
      $aws->s3Host = $_POST['s3Bucket'].'.s3.amazonaws.com';
      getConfig()->set('aws', $aws);
    }
    if($db_type === 'MySql')
    {
      $mysql = new stdClass;
      $mysql->mySqlHost = $_POST['mySqlHost'];
      $mysql->mySqlUser = $_POST['mySqlUser'];
      $mysql->mySqlPassword = $_POST['mySqlPassword'];
      $mysql->mySqlDb = $_POST['mySqlPassword'];
      getConfig()->set('mysql', $mysql);
    }
    if($fs_type === 'localfs')
    {
      $fsConfis = new stdClass;
      $fsConfig->fsRoot = $_POST['fsRoot'];
      $fsConfig->fsHost = $_POST['fsHost'];
      getConfig()->set('localfs', $fsConfig);
    }
    $systems = new stdClass;
    $systems->database = $_POST['database'];
    $systems->fileSystem = $_POST['fileSystem'];
    getConfig()->set('systems', $systems);

    $fs = getFs($fs_type);
    $db = getDb($db_type);

    /* halt on error */
    if($needs_aws && (empty($_POST['awsKey']) || empty($_POST['awsSecret'])))
      getRoute()->redirect('/setup?e=emptyCredentials');
    if(!$fs->initialize())
      getRoute()->redirect('/setup?e=fileSystemInitializationError');
    if(!$db->initialize())
      getRoute()->redirect('/setup?e=databaseInitializationError');

    // continue
    $baseDir = dirname(dirname(dirname(__FILE__)));
    $htmlDir = "{$baseDir}/html";
    $libDir = "{$baseDir}/libraries";
    $configDir = "{$baseDir}/configs";
    $replacements = array(
      '{adapters}' => "{$libDir}/adapters",
      '{configs}' => $configDir,
      '{controllers}' => "{$libDir}/controllers",
      '{external}' => "{$libDir}/external",
      '{libraries}' => "{$libDir}",
      '{models}' => "{$libDir}/models",
      '{photos}' => "{$htmlDir}/photos",
      '{exiftran}' => exec('which exiftran'),
      '{localSecret}' => sha1(uniqid(true)),
      '{s3Host}' => "{$_POST['s3Bucket']}.s3.amazonaws.com",
      '{email}' => "{$_POST['email']}"
    );
    $pReplace = array();
    foreach($_POST as $key => $val)
      $pReplace["{{$key}}"] = $val;
    $replacements = array_merge($pReplace, $replacements);
    $generatedIni = str_replace(
      array_keys($replacements),
      array_values($replacements),
      file_get_contents("{$configDir}/template.ini")
    );
    $wasPut = file_put_contents("{$configDir}/generated/settings.ini", $generatedIni);
    if(!$wasPut)
      getRoute()->redirect('/setup?e=iniFileWriteError');
    else
      getRoute()->redirect('/?m=welcome');
  }

  /**
    * Verify the server requirements are available on this host.
    *
    * @return mixed  TRUE on success, array on error
    */
  private static function verifyRequirements($imageLibs)
  {
    $errors = array();
    $configDir = dirname(dirname(dirname(__FILE__))) . '/configs';
    $generatedDir = "{$configDir}/generated";

    if(file_exists($generatedDir)
        && is_writable($generatedDir)
        && !empty($imageLibs))
      return true;

    $user = exec("whoami");
    if(empty($user))
      $user = 'Apache user';

    if(!is_writable($configDir))
    {
      $errors[] = "Please make sure {$user} can write to {$configDir}";
    }

    if(!file_exists($generatedDir))
    {
      $createDir = mkdir($generatedDir, 0700);
      if(!$createDir)
      {
        $errors[] = "An error occurred while trying to create {$generatedDir}";
      }
    }
    elseif(!is_writable($generatedDir))
    {
      $errors[] = "{$generatedDir} exists but is not writable by {$user}";
    }

    if((!class_exists('Imagick') && !class_exists('Gmagick')))
    {
      $errors[] = "Imagick does not exist (nor does Gmagick)";
    }

    return $errors;
  }
}
