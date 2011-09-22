<?php
/**
  * Setup controller for HTML endpoints
  * This controls the setup flow when the software is first installed.
  * The main purpose of this flow is to generate settings.ini.
  *
  * @author Jaisen Mathai <jaisen@jmathai.com>
  * @author Kevin Hornschemeier <khornschemeier@gmail.com>
  */
class SetupController
{
  /**
    * Returns the setup step 1 screen markup.
    *
    * @return string HTML
    */
  public static function setup()
  {
    $step = 1;
    $appId = 'openphoto-frontend';
    getSession('step', 1);

    $imageLibs = array();
    if(class_exists('Imagick'))
      $imageLibs['ImageMagick'] = 'ImageMagick';
    if(class_exists('Gmagick'))
      $imageLibs['GraphicsMagick'] = 'GraphicsMagick';
    if(extension_loaded('gd') && function_exists('gd_info'))
      $imageLibs['GD'] = 'GD';

    $errors = self::verifyRequirements($imageLibs);

    if(count($errors) > 0)
      $step = 0;
    else
      $errors = '';

    $body = getTheme()->get('setup.php', array('imageLibs' => $imageLibs, 'appId' => $appId, 'step' => $step, 'errors' => $errors));
    getTheme()->display('template.php', array('body' => $body, 'page' => 'setup'));
  }

  /**
    * Posts the setup values from step 1 of the form, checks them, and saves in session
    *
    * @return void HTTP redirect (setup step 2)
    */
  public static function setupPost()
  {
    $step = 1;
    $appId = isset($_POST['appId']) ? $_POST['appId'] : '';
    $email = isset($_POST['email']) ? $_POST['email'] : '';
    $input = array(
      array('Email', $email, 'required')
    );

    $errors = getForm()->hasErrors($input);
    if($errors === false)
    {
      getSession()->set('step', 2);
      getSession()->set('appId', $appId);
      getSession()->set('email', $email);

      getRoute()->redirect('/setup/2');
    }

    $body = getTheme()->get('setup.php', array('emai' => $email, 'appId' => $appId, 'step' => $step, 'errors' => $errors));
    getTheme()->display('template.php', array('body' => $body, 'page' => 'setup'));
  }

  /**
    * Returns the setup step 2 screen markup.
    *
    * @return string HTML
    */
  public static function setup2()
  {
    // make sure the user should be on this step
    if(getSession()->get('step') != 2)
        getRoute()->redirect('/setup');

    $step = 2;
    $imageLibs = array();
    if(class_exists('Imagick'))
      $imageLibs['ImageMagick'] = 'ImageMagick';
    if(class_exists('Gmagick'))
      $imageLibs['GraphicsMagick'] = 'GraphicsMagick';
    if(extension_loaded('gd') && function_exists('gd_info'))
      $imageLibs['GD'] = 'GD';

    $body = getTheme()->get('setup.php', array('imageLibs' => $imageLibs, 'appId' => 'openphoto-frontend', 'step' => $step));
    getTheme()->display('template.php', array('body' => $body, 'page' => 'setup'));
  }

  /**
    * Posts the setup values from step 2 of the form, checks them, and saves in session
    *
    * @return void HTTP redirect (setup step 3)
    */
  public static function setup2Post()
  {
      getSession()->set('step', 3);
      getSession()->set('imageLibrary', $_POST['imageLibrary']);
      getSession()->set('database', $_POST['database']);
      getSession()->set('fileSystem', $_POST['fileSystem']);
      getRoute()->redirect('/setup/3');
  }

  /**
    * Returns the setup step 3 screen markup.
    *
    * @return string HTML
    */
  public static function setup3()
  {
    // make sure the user should be on this step
    if(getSession()->get('step') != 3)
    {
      if(getSession()->get('step') == 2)
        getRoute()->redirect('/setup/2');

      getRoute()->redirect('/setup');
    }

    $step = 3;
    $appId = getSession()->get('appId');
    $usesAws = (getSession()->get('database') == 'SimpleDb' || getSession()->get('fileSystem') == 'S3') ? true : false;
    $usesMySql = (getSession()->get('database') == 'MySql') ? true : false;
    $usesLocalFs = (getSession()->get('fileSystem') == 'LocalFs') ? true : false;
    $usesS3 = (getSession()->get('fileSystem') == 'S3') ? true : false;
    $usesSimpleDb = (getSession()->get('database') == 'SimpleDb') ? true : false;

    $body = getTheme()->get('setup.php', array('step' => $step, 'usesAws' => $usesAws, 'usesMySql' => $usesMySql, 'usesLocalFs' => $usesLocalFs, 'usesS3' => $usesS3, 'usesSimpleDb' => $usesSimpleDb, 'appId' => $appId));
    getTheme()->display('template.php', array('body' => $body, 'page' => 'setup'));
  }

  /**
    * Posts the setup values from step 3 of the form, checks them, and saves in session
    *
    * @return void HTTP redirect (home)
    */
  public static function setup3Post()
  {
    $step = 3;
    $appId = getSession()->get('appId');
    $usesAws = (getSession()->get('database') == 'SimpleDb' || getSession()->get('fileSystem') == 'S3') ? true : false;
    $usesMySql = (getSession()->get('database') == 'MySql') ? true : false;
    $usesLocalFs = (getSession()->get('fileSystem') == 'LocalFs') ? true : false;
    $usesS3 = (getSession()->get('fileSystem') == 'S3') ? true : false;
    $usesSimpleDb = (getSession()->get('database') == 'SimpleDb') ? true : false;
    $awsErrors = false;
    $mySqlErrors = false;
    $localFsErrors = false;
    $fsErrors = false;
    $dbErrors = false;
    $writeErrors = false;

    if($usesAws)
    {
      $awsKey = $_POST['awsKey'];
      $awsSecret = $_POST['awsSecret'];

      $input = array(
        array('Amazon Access Key ID', $awsKey, 'required'),
        array('Amazon Secret Access Key', $awsSecret, 'required')
      );

      if($usesS3)
      {
        $s3Bucket = $_POST['s3Bucket'];
        $input[] = array('Amazon S3 Bucket Name', $s3Bucket, 'required');
      }

      if($usesSimpleDb)
      {
        $simpleDbDomain = $_POST['simpleDbDomain'];
        $input[] = array('Amazon SimpleDb Domain', $simpleDbDomain, 'required');
      }

      $awsErrors = getForm()->hasErrors($input);
    }

    if($usesMySql)
    {
      $mySqlHost = $_POST['mySqlHost'];
      $mySqlUser = $_POST['mySqlUser'];
      $mySqlPassword = $_POST['mySqlPassword'];
      $mySqlDb = $_POST['mySqlDb'];
      $mySqlTablePrefix = $_POST['mySqlTablePrefix'];
      $input = array(
        array('MySQL Host', $mySqlHost, 'required'),
        array('MySQL Username', $mySqlUser, 'required'),
        array('MySQL Password', $mySqlPassword, 'required'),
        array('MySQL Database', $mySqlDb, 'required'),
        array('MySQL Table Prefix', $mySqlTablePrefix, 'required')
      );
      $mySqlErrors = getForm()->hasErrors($input);
    }

    if($usesLocalFs)
    {
      $fsRoot = $_POST['fsRoot'];
      $fsHost = $_POST['fsHost'];
      $input = array(
        array('File System Root', $fsRoot, 'required'),
        array('File System Host', $fsHost, 'required')
      );

      $localFsErrors = getForm()->hasErrors($input);
    }

    if($awsErrors === false && $mySqlErrors === false && $localFsErrors === false)
    {
      $credentials = new stdClass;
      if($usesAws)
      {
        getSession()->set('awsKey', $awsKey);
        getSession()->set('awsSecret', $awsSecret);
        $credentials->awsKey = $awsKey;
        $credentials->awsSecret = $awsSecret;

        $aws = new stdClass;
        if($usesS3)
        {
          getSession()->set('s3BucketName', $s3Bucket);
          $aws->s3BucketName = $s3Bucket;
          $aws->s3Host = "{$s3Bucket}.s3.amazonaws.com";
        }

        if($usesSimpleDb)
        {
          getSession()->set('simpleDbDomain', $simpleDbDomain);
          $aws->simpleDbDomain = $simpleDbDomain;
        }
      }
      if($usesMySql)
      {
        getSession()->set('mySqlHost', $mySqlHost);
        getSession()->set('mySqlUser', $mySqlUser);
        getSession()->set('mySqlPassword', $mySqlPassword);
        getSession()->set('mySqlDb', $mySqlDb);
        getSession()->set('mySqlTablePrefix', $mySqlTablePrefix);
        $mysql = new stdClass;
        $mysql->mySqlHost = $mySqlHost;
        $mysql->mySqlUser = $mySqlUser;
        $mysql->mySqlPassword = $mySqlPassword;
        $mysql->mySqlDb = $mySqlDb;
        $mysql->mySqlTablePrefix = $mySqlTablePrefix;
      }
      if($usesLocalFs)
      {
        getSession()->set('fsRoot', $fsRoot);
        getSession()->set('fsHost', $fsHost);
        $fs = new stdClass;
        $fs->fsRoot = $fsRoot;
        $fs->fsHost = $fsHost;
      }

      $systems = new stdClass;
      $systems->database = getSession()->get('database');
      $systems->fileSystem = getSession()->get('fileSystem');

      // save the config info
      getConfig()->set('credentials', $credentials);
      if($usesAws)
        getConfig()->set('aws', $aws);
      if($usesMySql)
        getConfig()->set('mysql', $mysql);
      if($usesLocalFs)
        getConfig()->set('localfs', $fs);
      getConfig()->set('systems', $systems);

      $fsObj = getFs();
      $dbObj = getDb();

      $user = exec("whoami");
      if(!$fsObj->initialize())
      {
        if($usesAws)
          $fsErrors[] = 'We were unable to initialize your S3 bucket.<ul><li>Make sure you\'re <a href="http://aws.amazon.com/s3/">signed up for AWS S3</a>.</li><li>Double check your AWS credentials.</li><li>S3 bucket names are globally unique, make sure yours isn\'t already in use by someone else.</li><li>S3 bucket names can\'t have certain special characters. Try using just alpha-numeric characters and periods.</li></ul>';
        else if($usesLocalFs)
          $fsErrors[] = "We were unable to set up your local file system using <em>{$fs->rsRoot}</em>. Make sure that the following user has proper permissions ({$user}).";
        else
          $fsErrors[] = 'An unknown error occurred while setting up your file system. Check your error logs to see if there\'s more information about the error.';
      }
      if(!$dbObj->initialize())
      {
        if($usesAws)
          $dbErrors[] = 'We were unable to initialize your SimpleDb domains.<ul><li>Make sure you\'re <a href="http://aws.amazon.com/simpledb/">signed up for AWS SimpleDb</a>.</li><li>Double check your AWS credentials.</li><li>SimpleDb domains cannot contain special characters such as periods.</li></ul>';
        else if($usesMySql)
          $dbErrors[] = 'We were unable to properly connect to your MySql database server. Please verify that the host, username and password are correct and have proper permissions to create a database.';
        else
          $dbErrors[] = 'An unknown error occurred while setting up your file system. Check your error logsto see if there\'s more information about the error.';
      }

      if($fsErrors === false && $dbErrors === false)
      {
        $writeError = self::writeConfigFile();
        if($writeErrors === false)
          getRoute()->redirect('/?m=welcome');
        else
          $writeErrors[] = "We were unable to save your settings file. Please make sure that the following user has proper permissions to write to src/configs ({$user}).";
      }
    }

    // combine all errors if they exist
    $errors = array();
    if(is_array($awsErrors))
      $errors = array_merge($errors, $awsErrors);
    if(is_array($mySqlErrors))
      $errors = array_merge($errors, $mySqlErrors);
    if(is_array($localFsErrors))
      $errors = array_merge($errors, $localFsErrors);
    if(is_array($fsErrors))
      $errors = array_merge($errors, $fsErrors);
    if(is_array($dbErrors))
      $errors = array_merge($errors, $dbErrors);
    if(is_array($writeErrors))
      $errors = array_merge($errors, $writeErrors);

    $body = getTheme()->get('setup.php', array('step' => $step, 'usesAws' => $usesAws, 'usesMySql' => $usesMySql, 'usesLocalFs' => $usesLocalFs, 'usesS3' => $usesS3, 'usesSimpleDb' => $usesSimpleDb, 's3Bucket' => $s3Bucket, 'simpleDbDomain' => $simpleDbDomain, 'appId' => $appId, 'errors' => $errors));
    getTheme()->display('template.php', array('body' => $body, 'page' => 'setup'));
  }

  /**
    * Clears out the session data and redirects to step 1
    *
    * @return void HTTP redirect (setup step 1)
    */
  public static function setupRestart()
  {
    getSession()->end();
    getRoute()->redirect('/setup');
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

    if(file_exists($generatedDir) && is_writable($generatedDir) && !empty($imageLibs))
      # No errors, return empty array
      return $errors;

    $user = exec("whoami");
    if(empty($user))
      $user = 'Apache user';

    if(!is_writable($configDir))
      $errors[] = "Insufficient privileges to complete setup.<ul><li>Make sure the user <em>{$user}</em> can write to <em>{$configDir}</em>.</li></ul>";

    if(!file_exists($generatedDir))
    {
      $createDir = mkdir($generatedDir, 0700);
      if(!$createDir)
        $errors[] = "Could not create configuration directory.<ul><li>Make sure the user <um>{$user}</em> can write to <em>{$generatedDir}</em>.</li></ul>";
    }
    elseif(!is_writable($generatedDir))
    {
      $errors[] = "Directory exist but is not writable.<ul><li>Make sure the user <um>{$user}</em> can write to <em>{$generatedDir}</em>.</li></ul>";
    }

    if(empty($imageLibs))
      $errors[] = 'No suitable image library exists.<ul><li>Make sure that one of the following are installed: <em><a href="http://php.net/imagick">Imagick</a></em>, <em><a href="http://php.net/gmagick">Gmagick</a></em>, or <em><a href="http://php.net/gd">GD</a></em>.</li></ul>';

    return $errors;
  }

  /**
    * Write out the settings config file
    *
    * @return boolean  TRUE on success, FALSE on error
    */
  private static function writeConfigFile()
  {
    // continue if no errors
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
      '{themes}' => "{$htmlDir}/assets/themes",
      '{exiftran}' => exec('which exiftran'),
      '{localSecret}' => sha1(uniqid(true)),
      '{awsKey}' => "",
      '{awsSecret}' => "", 
      '{s3Bucket}' => getSession()->get('s3BucketName'),
      '{s3Host}' => getSession()->get('s3BucketName') . '.s3.amazonaws.com',
      '{simpleDbDomain}' => "",
      '{mySqlHost}' => "",
      '{mySqlUser}' => "",
      '{mySqlPassword}' => "",
      '{mySqlDb}' => "",
      '{mySqlTablePrefix}' => "",
      '{fsRoot}' => "",
      '{fsHost}' => "",
      '{email}' => getSession()->get('email')
    );

    $pReplace = array();
    $session = getSession()->getAll();
    foreach($session as $key => $val)
      $pReplace["{{$key}}"] = $val;

    $replacements = array_merge($replacements, $pReplace);
    $generatedIni = str_replace(
      array_keys($replacements),
      array_values($replacements),
      file_get_contents("{$configDir}/template.ini")
    );

    $iniWritten = file_put_contents(sprintf("%s/generated/%s.ini", $configDir, getenv('HTTP_HOST')), $generatedIni);
    if(!$iniWritten)
      return false;

    return true;
  }
}
