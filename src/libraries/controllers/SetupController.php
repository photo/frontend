<?php
class SetupController
{
  private static $directoryErrors = array();
  public static function setup()
  {
    $permissionCheck = self::verifyRequirements();
    if($permissionCheck !== true)
    {
      getTemplate()->display('template.php', array('body' => 'setupRequirements.php', array('errors' => $errors)));
      return;
    }

    /*$options = array(
      'systems' => array(
        'database' => array(
          'simpleDb' => 'Amazon SimpleDb'
        ),
        'fileSysetm' => array(
          's3' => 'Amazon S3', 'cloudFiles' => 'Rackspace Cloudfiles'
        )
      ),
      'credentials' => array(
        'providers' => array()
      )
    );*/
    $imageLibs = array();
    if(class_exists('Imagick'))
      $imageLibs['ImageMagick'] = 'ImageMagick';
    if(class_exists('Gmagick'))
      $imageLibs['GraphicsMagick'] = 'GraphicsMagick';

    $params = array('imageLibs' => $imageLibs, 'appId' => $_SERVER['HTTP_HOST']);
    getTemplate()->display('blank.php', array('body' => getTemplate()->get('setup.php', $params)));
  }

  public static function setupPost()
  {
    $opts = new stdClass;
    $opts->awsKey = $_POST['awsKey'];
    $opts->awsSecret = $_POST['awsSecret'];
    getConfig()->set('credentials', $opts);
    $aws = new stdClass;
    $aws->s3BucketName = $_POST['s3Bucket'];
    $aws->simpleDbDomain = $_POST['simpleDbDomain'];
    $aws->s3Host = $_POST['s3Bucket'].'.s3.amazonaws.com';
    getConfig()->set('aws', $aws);
    $systems = new stdClass;
    $systems->database = $_POST['database'];
    $systems->fileSystem = $_POST['fileSystem'];
    getConfig()->set('systems', $systems);

    $fs = getFs($_POST['fileSystem']);
    $db = getDb($_POST['database']);

    /* halt on error */
    if(empty($_POST['awsKey']) || empty($_POST['awsSecret']))
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
      '{controllers}' => "{$libDir}/controllers",
      '{external}' => "{$libDir}/external",
      '{libraries}' => "{$libDir}",
      '{models}' => "{$libDir}/models",
      '{photos}' => "{$htmlDir}/photos",
      '{exiftran}' => exec('which exiftran'),
      '{localSecret}' => sha1(uniqid(true)),
      '{s3Host}' => "{$_POST['s3Bucket']}.s3.amazonaws.com"
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

  private static function initialize()
  {
    
  }

  private static function verifyRequirements()
  {
    $errors = array();
    $configDir = dirname(dirname(dirname(__FILE__))) . '/configs';
    $generatedDir = "{$configDir}/generated";
    if(file_exists($generatedDir) 
        && is_writable($generatedDir)
        && (class_exists('Imagick') || class_exists('Gmagick')))
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
    else
    {
      $errors[] = "{$generatedDir} exists but is not writable by {$user}";
    }
    
    return $errors;
  }
}
