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

    $imageLibrary = '';
    if(getConfig()->get('modules') != null)
      $imageLibrary = getConfig()->get('modules')->image;

    $database = '';
    $filesystem = '';
    if(getConfig()->get('systems') != null)
    {
      $database = getConfig()->get('systems')->database;
      $filesystem = getConfig()->get('systems')->fileSystem;
    }
    $theme = getTheme()->getThemeName();
    $themes = getTheme()->getThemes();

    $errors = self::verifyRequirements($imageLibs);

    if(count($errors) > 0)
      $step = 0;
    else
      $errors = '';

    $email = '';
    if(getConfig()->get('user') != null)
      $email = getConfig()->get('user')->email;
    elseif(User::isLoggedIn())
      $email = getSession()->get('email');

    $qs = '';
    if(isset($_GET['edit']))
      $qs = '?edit';

    $template = sprintf('%s/setup.php', getConfig()->get('paths')->templates);
    $body = getTemplate()->get($template, array('filesystem' => $filesystem, 'database' => $database, 'themes' => $themes, 'theme' => $theme, 
      'imageLibs' => $imageLibs, 'imageLibrary' => $imageLibrary, 'appId' => $appId, 'step' => $step, 'email' => $email, 'qs' => $qs, 'errors' => $errors));
    getTheme()->display('template.php', array('body' => $body, 'page' => 'setup'));
  }

  /**
    * Gets Dropbox info from the user
    *
    * @return string HTML
    */
  public static function setupDropbox()
  {
    extract(self::getDefaultConfigParams());
    $secret = self::getSecret();
    $credentials = getConfig()->get('credentials');
    $dropbox = getConfig()->get('dropbox');
    if($credentials !== null)
    {
      if(isset($credentials->dropboxKey) && !empty($credentials->dropboxKey))
        $dropboxKey = Utility::decrypt($credentials->dropboxKey, $secret);
      if(isset($credentials->dropboxSecret) && !empty($credentials->dropboxSecret))
        $dropboxSecret = Utility::decrypt($credentials->dropboxSecret, $secret);
      if(isset($dropbox->dropboxFolder))
        $dropboxFolder = $dropbox->dropboxFolder;
    }

    $qs = '';
    if(isset($_GET['edit']))
      $qs = '?edit';

    $template = sprintf('%s/setupDropbox.php', getConfig()->get('paths')->templates);
    $body = getTemplate()->get($template, array('dropboxKey' => $dropboxKey, 'dropboxSecret' => $dropboxSecret, 'dropboxFolder' => $dropboxFolder, 'qs' => $qs));
    getTheme()->display('template.php', array('body' => $body, 'page' => 'setup'));
  }

  /**
    * Handles callback from Dropbox oauth flow
    *
    * @return void HTTP redirect (to dropbox.com)
    */
  public static function setupDropboxCallback()
  {
    $secret = self::getSecret();
    try
    {
      $dropboxToken = getSession()->get('dropboxToken');
      $dropboxKey = Utility::decrypt(getSession()->get('flowDropboxKey'), $secret);
      $dropboxSecret = Utility::decrypt(getSession()->get('flowDropboxSecret'), $secret);
      $oauth = new Dropbox_OAuth_PHP($dropboxKey, $dropboxSecret);
      $oauth->setToken($dropboxToken);
      $accessToken = $oauth->getAccessToken();
      getSession()->set('dropboxFolder', getSession()->get('flowDropboxFolder'));
      getSession()->set('dropboxKey', getSession()->get('flowDropboxKey'));
      getSession()->set('dropboxSecret', getSession()->get('flowDropboxSecret'));
      getSession()->set('dropboxToken', Utility::encrypt($accessToken['token'], $secret));
      getSession()->set('dropboxTokenSecret', Utility::encrypt($accessToken['token_secret'], $secret));

      $qs = '';
      if(isset($_GET['edit']))
        $qs = '?edit';

      getRoute()->redirect(sprintf('%s%s', '/setup/3', $qs));
    }
    catch(Dropbox_Exception $e)
    {
      getLogger()->crit(sprintf('An error occured getting the Dropbox authorize url. Message: %s', $e->getMessage()));
      getRoute()->run('/error/500');
    }
  }

  /**
    * Gets authorize URL and redirects to dropbox
    *
    * @return void HTTP redirect (to dropbox.com)
    */
  public static function setupDropboxPost()
  {
    $qs = '';
    if(isset($_GET['edit']))
      $qs = '?edit';
    $secret = self::getSecret();

    try
    {
      getSession()->set('flowDropboxKey', Utility::encrypt($_POST['dropboxKey'], $secret));
      getSession()->set('flowDropboxSecret', Utility::encrypt($_POST['dropboxSecret'], $secret));
      getSession()->set('flowDropboxFolder', $_POST['dropboxFolder']);
      $callback = urlencode(sprintf('%s://%s%s%s', Utility::getProtocol(false), getenv('HTTP_HOST'), '/setup/dropbox/callback', $qs));
      $oauth = new Dropbox_OAuth_PHP($_POST['dropboxKey'], $_POST['dropboxSecret']);
      getSession()->set('dropboxToken', $oauth->getRequestToken());
      $url = $oauth->getAuthorizeUrl($callback);
      getRoute()->redirect($url, null, true);
    }
    catch(Dropbox_Exception $e)
    {
      getLogger()->crit(sprintf('An error occured getting the Dropbox authorize url. Message: %s', $e->getMessage()));
      getRoute()->run('/error/500', EpiRoute::httpGet);
    }
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
    $theme = isset($_POST['theme']) ? $_POST['theme'] : '';
    $input = array(
      array('Email', $email, 'required')
    );

    $errors = getForm()->hasErrors($input);
    if($errors === false)
    {
      getSession()->set('step', 2);
      getSession()->set('appId', $appId);
      getSession()->set('ownerEmail', $email);
      getSession()->set('theme', $theme);

      $qs = '';
      if(isset($_GET['edit']))
        $qs = '?edit';

      getRoute()->redirect('/setup/2' . $qs);
    }

    $template = sprintf('%s/setup.php', getConfig()->get('paths')->templates);
    $body = getTemplate()->get($template, array('email' => $email, 'appId' => $appId, 'step' => $step, 'errors' => $errors));
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

    $imageLibrary = '';
    if(getConfig()->get('modules') != null)
      $imageLibrary = getConfig()->get('modules')->image;

    $database = '';
    $filesystem = '';
    if(getConfig()->get('systems') != null)
    {
      $database = getConfig()->get('systems')->database;
      $filesystem = getConfig()->get('systems')->fileSystem;
    }

    $qs = '';
    if(isset($_GET['edit']))
      $qs = '?edit';

    $template = sprintf('%s/setup.php', getConfig()->get('paths')->templates);
    $body = getTemplate()->get($template, array('themes' => array(), 'imageLibs' => $imageLibs, 'appId' => 'openphoto-frontend', 'imageLibrary' => $imageLibrary, 'database' => $database, 'filesystem' => $filesystem, 'qs' => $qs, 'step' => $step));
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

      $qs = '';
      if(isset($_GET['edit']))
        $qs = '?edit';
      if(stristr($_POST['fileSystem'], 'Dropbox') !== false)
        getRoute()->redirect('/setup/dropbox' . $qs);
      else
        getRoute()->redirect('/setup/3' . $qs);
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

    extract(self::getDefaultConfigParams());
    $secret = self::getSecret();
    $step = 3;
    $appId = getSession()->get('appId');
    $database = getSession()->get('database');
    $filesystem = getSession()->get('filesystem');
    $usesAws = (getSession()->get('database') == 'SimpleDb' || stristr(getSession()->get('fileSystem'), 'S3') !== false) ? true : false;
    $usesMySql = (getSession()->get('database') == 'MySql') ? true : false;
    $usesLocalFs = (stristr(getSession()->get('fileSystem'), 'Local') !== false) ? true : false;
    $usesS3 = (stristr(getSession()->get('fileSystem'), 'S3') !== false) ? true : false;
    $usesDropbox = (stristr(getSession()->get('fileSystem'), 'Dropbox') !== false) ? true : false;
    $usesSimpleDb = (getSession()->get('database') == 'SimpleDb') ? true : false;

    $dropboxKey = getSession()->get('dropboxKey');
    if(!empty($dropboxKey))
    {
      $dropboxFolder = getSession()->get('dropboxFolder');
      $dropboxKey = Utility::decrypt(getSession()->get('dropboxKey'), $secret);
      $dropboxSecret = Utility::decrypt(getSession()->get('dropboxSecret'), $secret);
      $dropboxToken = Utility::decrypt(getSession()->get('dropboxToken'), $secret);
      $dropboxTokenSecret = Utility::decrypt(getSession()->get('dropboxTokenSecret'), $secret);
    }
    if(getConfig()->get('credentials') != null)
    {
      $credentials = getConfig()->get('credentials');
      if(isset($credentials->awsKey))
        $awsKey = Utility::decrypt($credentials->awsKey, $secret);
      if(isset($credentials->awsSecret))
        $awsSecret = Utility::decrypt($credentials->awsSecret, $secret);
      if(empty($dropboxKey))
      {
        if(isset($credentials->dropboxKey))
          $dropboxKey = Utility::decrypt($credentials->dropboxKey, $secret);
        if(isset($credentials->dropboxSecret))
          $dropboxSecret = Utility::decrypt($credentials->dropboxSecret, $secret);
        if(isset($credentials->dropboxToken))
          $dropboxToken = Utility::decrypt($credentials->dropboxToken, $secret);
        if(isset($credentials->dropboxTokenSecret))
          $dropboxTokenSecret = Utility::decrypt($credentials->dropboxTokenSecret, $secret);
      }
    }

    if(getConfig()->get('aws') != null)
    {
      $s3Bucket = getConfig()->get('aws')->s3BucketName;
      $simpleDbDomain = getConfig()->get('aws')->simpleDbDomain;
    }

    if(getConfig()->get('mysql') != null)
    {
      $mysql = getConfig()->get('mysql');
      $mySqlHost = $mysql->mySqlHost;
      $mySqlUser = $mysql->mySqlUser;
      $mySqlPassword = Utility::decrypt($mysql->mySqlPassword, $secret);
      $mySqlDb = $mysql->mySqlDb;
      $mySqlTablePrefix = $mysql->mySqlTablePrefix;
    }

    if(getConfig()->get('localfs') != null)
    {
      $fsRoot = getConfig()->get('localfs')->fsRoot;
      $fsHost = getConfig()->get('localfs')->fsHost;
    }

    if(!isset($dropboxFolder) && getConfig()->get('dropbox') != null)
    {
      $dropboxFolder = getConfig()->get('dropbox')->dropboxFolder;
    }

    $qs = '';
    if(isset($_GET['edit']))
      $qs = '?edit';

    $template = sprintf('%s/setup.php', getConfig()->get('paths')->templates);
    // copied to/from setup3Post()
    $body = getTemplate()->get($template, array('step' => $step, 'themes' => $themes, 'usesAws' => $usesAws, 'usesMySql' => $usesMySql, 
      'database' => $database, 'filesystem' => $filesystem, 'usesLocalFs' => $usesLocalFs, 'usesS3' => $usesS3, 
      'usesSimpleDb' => $usesSimpleDb, 'awsKey' => $awsKey, 'awsSecret' => $awsSecret, 's3Bucket' => $s3Bucket, 
      'simpleDbDomain' => $simpleDbDomain, 'mySqlHost' => $mySqlHost, 'mySqlUser' => $mySqlUser, 'mySqlDb' => $mySqlDb, 
      'mySqlPassword' => $mySqlPassword, 'mySqlTablePrefix' => $mySqlTablePrefix, 'fsRoot' => $fsRoot, 'fsHost' => $fsHost, 
      'usesDropbox' => $usesDropbox, 'dropboxKey' => $dropboxKey, 'dropboxSecret' => $dropboxSecret, 'dropboxToken' => $dropboxToken, 
      'dropboxTokenSecret' => $dropboxTokenSecret, 'dropboxFolder' => $dropboxFolder, 'qs' => $qs, 'appId' => $appId, 'errors' => $errors));

    getTheme()->display('template.php', array('body' => $body, 'page' => 'setup'));
  }

  /**
    * Posts the setup values from step 3 of the form, checks them, and saves in session
    *
    * @return void HTTP redirect (home)
    */
  public static function setup3Post()
  {
    extract(self::getDefaultConfigParams());
    $step = 3;
    $secret = self::getSecret();
    $database = getSession()->get('database');
    $filesystem = getSession()->get('filesystem');
    $appId = getSession()->get('appId');
    $usesAws = (getSession()->get('database') == 'SimpleDb' || stristr(getSession()->get('fileSystem'), 'S3') !== false) ? true : false;
    $usesMySql = (getSession()->get('database') == 'MySql') ? true : false;
    $usesSimpleDb = (getSession()->get('database') == 'SimpleDb') ? true : false;
    $usesLocalFs = (stristr(getSession()->get('fileSystem'), 'Local') !== false) ? true : false;
    $usesS3 = (stristr(getSession()->get('fileSystem'), 'S3') !== false) ? true : false;
    $usesDropbox = (stristr(getSession()->get('fileSystem'), 'Dropbox') !== false) ? true : false;
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

    if($usesDropbox)
    {
      $dropboxKey = $_POST['dropboxKey'];
      $dropboxSecret = $_POST['dropboxSecret'];
      $dropboxToken = $_POST['dropboxToken'];
      $dropboxTokenSecret = $_POST['dropboxTokenSecret'];
      $dropboxFolder = $_POST['dropboxFolder'];
    }

    if($awsErrors === false && $mySqlErrors === false && $localFsErrors === false)
    {
      $credentials = new stdClass;
      if($usesAws)
      {
        getSession()->set('awsKey', Utility::encrypt($awsKey, $secret));
        getSession()->set('awsSecret', Utility::encrypt($awsSecret, $secret));
        $credentials->awsKey = Utility::encrypt($awsKey, $secret);
        $credentials->awsSecret = Utility::encrypt($awsSecret, $secret);

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
        getSession()->set('mySqlPassword', Utility::encrypt($mySqlPassword, $secret));
        getSession()->set('mySqlDb', $mySqlDb);
        getSession()->set('mySqlTablePrefix', $mySqlTablePrefix);
        $mysql = new stdClass;
        $mysql->mySqlHost = $mySqlHost;
        $mysql->mySqlUser = $mySqlUser;
        $mysql->mySqlPassword = Utility::encrypt($mySqlPassword, $secret);
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

      if($usesDropbox)
      {
        getSession()->set('dropboxKey', Utility::encrypt($dropboxKey, $secret));
        getSession()->set('dropboxSecret', Utility::encrypt($dropboxSecret, $secret));
        getSession()->set('dropboxToken', Utility::encrypt($dropboxToken, $secret));
        getSession()->set('dropboxTokenSecret', Utility::encrypt($dropboxTokenSecret, $secret));
        getSession()->set('dropboxFolder', $dropboxFolder);
        $credentials->dropboxKey = Utility::encrypt($dropboxKey, $secret);
        $credentials->dropboxSecret = Utility::encrypt($dropboxSecret, $secret);
        $credentials->dropboxToken = Utility::encrypt($dropboxToken, $secret);
        $credentials->dropboxTokenSecret = Utility::encrypt($dropboxTokenSecret, $secret);
        $dropbox = new stdClass;
        $dropbox->dropboxFolder = $dropboxFolder;
      }

      $systems = new stdClass;
      $systems->database = getSession()->get('database');
      $systems->fileSystem = getSession()->get('fileSystem');
      $secrets = new stdClass;
      $secrets->secret = self::getSecret();

      // save the config info
      getConfig()->set('credentials', $credentials);
      if($usesAws)
        getConfig()->set('aws', $aws);
      if($usesMySql)
        getConfig()->set('mysql', $mysql);
      if($usesLocalFs)
        getConfig()->set('localfs', $fs);
      if($usesDropbox)
        getConfig()->set('dropbox', $dropbox);
      getConfig()->set('systems', $systems);
      getConfig()->set('secrets', $secrets);

      $fsObj = getFs();
      $dbObj = getDb();

      $user = exec("whoami");
      if(!$fsObj->initialize())
      {
        if($usesAws)
          $fsErrors[] = 'We were unable to initialize your S3 bucket.<ul><li>Make sure you\'re <a href="http://aws.amazon.com/s3/">signed up for AWS S3</a>.</li><li>Double check your AWS credentials.</li><li>S3 bucket names are globally unique, make sure yours isn\'t already in use by someone else.</li><li>S3 bucket names can\'t have certain special characters. Try using just alpha-numeric characters and periods.</li></ul>';
        else if($usesLocalFs)
          $fsErrors[] = "We were unable to set up your local file system using <em>{$fsObj->fsRoot}</em>. Make sure that the following user has proper permissions ({$user}).";
        else
          $fsErrors[] = 'An unknown error occurred while setting up your file system. Check your error logs to see if there\'s more information about the error.';
      }
      if(!$dbObj->initialize())
      {
        if($usesAws)
          $dbErrors[] = 'We were unable to initialize your SimpleDb domains.<ul><li>Make sure you\'re <a href="http://aws.amazon.com/simpledb/">signed up for AWS SimpleDb</a>.</li><li>Double check your AWS credentials.</li><li>SimpleDb domains cannot contain special characters such as periods.</li><li>Sometimes the SimpleDb create domain API is unstable. Try again later or check the error log if you have access to it.</li></ul>';
        else if($usesMySql)
          $dbErrors[] = 'We were unable to properly connect to your MySql database server. Please verify that the host, username and password are correct and have proper permissions to create a database.';
        else
          $dbErrors[] = 'An unknown error occurred while setting up your file system. Check your error logsto see if there\'s more information about the error.';

        $dbErrors = array_merge($dbErrors, $dbObj->errors());
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

    $qs = '';
    if(isset($_GET['edit']))
      $qs = '?edit';

    $template = sprintf('%s/setup.php', getConfig()->get('paths')->templates);
    // copied to/from setup3()
    $body = getTemplate()->get($template, array('step' => $step, 'themes' => $themes, 'usesAws' => $usesAws, 'usesMySql' => $usesMySql, 
      'database' => $database, 'filesystem' => $filesystem, 'usesLocalFs' => $usesLocalFs, 'usesS3' => $usesS3, 
      'usesSimpleDb' => $usesSimpleDb, 'awsKey' => $awsKey, 'awsSecret' => $awsSecret, 's3Bucket' => $s3Bucket, 
      'simpleDbDomain' => $simpleDbDomain, 'mySqlHost' => $mySqlHost, 'mySqlUser' => $mySqlUser, 'mySqlDb' => $mySqlDb, 
      'mySqlPassword' => $mySqlPassword, 'mySqlTablePrefix' => $mySqlTablePrefix, 'fsRoot' => $fsRoot, 'fsHost' => $fsHost, 
      'usesDropbox' => $usesDropbox, 'dropboxKey' => $dropboxKey, 'dropboxSecret' => $dropboxSecret, 'dropboxToken' => $dropboxToken, 
      'dropboxTokenSecret' => $dropboxTokenSecret, 'dropboxFolder' => $dropboxFolder, 'qs' => $qs, 'appId' => $appId, 'errors' => $errors));
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

  public static function getSecret()
  {
    if(getConfig()->get('secrets') !== null)
    {
      $secret = getConfig()->get('secrets')->secret;
      getSession()->set('secret', $secret);
      return $secret;
    }

    $secret = getSession()->get('secret');
    if(!$secret)
    {
      $secret = sha1(uniqid(true));
      getSession()->set('secret', $secret);
    }

    return $secret;
  }

  private static function getDefaultConfigParams()
  {
    return array('themes' => array(), 'awsKey' => '', 'awsSecret' => '', 's3Bucket' => '', 'simpleDbDomain' => '', 'mySqlHost' => '', 
      'mySqlUser' => '', 'mySqlPassword' => '', 'mySqlDb' => '', 'mySqlTablePrefix' => '', 
      'fsRoot' => '', 'fsHost' => '', 'dropboxFolder' => '', 'dropboxKey' => '', 'dropboxSecret' => '', 
      'dropboxKey' => '', 'dropboxToken' => '', 'dropboxTokenSecret' => '', 'errors' => '');
  }

  /**
    * Verify the server requirements are available on this host.
    *
    * @return mixed  TRUE on success, array on error
    */
  private static function verifyRequirements($imageLibs)
  {
    $errors = array();
    $configDir = Utility::getBaseDir() . '/configs';
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
        $errors[] = "Could not create configuration directory.<ul><li>Make sure the user <em>{$user}</em> can write to <em>{$generatedDir}</em>.</li></ul>";
    }
    elseif(!is_writable($generatedDir))
    {
      $errors[] = "Directory exist but is not writable.<ul><li>Make sure the user <em>{$user}</em> can write to <em>{$generatedDir}</em>.</li></ul>";
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
    $secret = self::getSecret();
    $baseDir = Utility::getBaseDir();
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
      '{templates}' => "{$baseDir}/templates",
      '{themes}' => "{$htmlDir}/assets/themes",
      '{exiftran}' => exec('which exiftran'),
      '{autoTagWithDate}' => '1',
      '{localSecret}' => $secret,
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
      '{dropboxKey}' => "",
      '{dropboxSecret}' => "",
      '{dropboxToken}' => "",
      '{dropboxTokenSecret}' => "",
      '{dropboxFolder}' => "",
      '{fsRoot}' => "",
      '{fsHost}' => "",
      '{temp}' => sys_get_temp_dir(),
      '{theme}' => getSession()->get('theme'),
      '{email}' => getSession()->get('ownerEmail')
    );

    $pReplace = array();
    $session = getSession()->getAll();
    foreach($session as $key => $val)
    {
      if($key != 'email')
        $pReplace["{{$key}}"] = $val;

      getLogger()->info(sprintf('Storing %s as %s', $key, $val));
    }

    $replacements = array_merge($replacements, $pReplace);
    $generatedIni = str_replace(
      array_keys($replacements),
      array_values($replacements),
      file_get_contents("{$configDir}/template.ini")
    );

    $iniWritten = file_put_contents(sprintf("%s/generated/%s.ini", $configDir, getenv('HTTP_HOST')), $generatedIni);
    if(!$iniWritten)
      return false;

    // clean up the session
    foreach($session as $key => $val)
    {
      if($key != 'email')
        getSession()->set($key, '');
    }
    return true;
  }
}
