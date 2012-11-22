<?php
/**
  * Setup controller for HTML endpoints
  * This controls the setup flow when the software is first installed.
  * The main purpose of this flow is to generate settings.ini.
  *
  * @author Jaisen Mathai <jaisen@jmathai.com>
  * @author Kevin Hornschemeier <khornschemeier@gmail.com>
  */
class SetupController extends BaseController
{

  private $actualTheme;

  /**
    * Call the parent constructor
    *
    * @return void
    */
  public function __construct()
  {
    parent::__construct();
    $this->actualTheme = $this->theme->getThemeName();
    $this->theme->setTheme(); // defaults
    $this->user = new User;
  }
  
  /**
    * Returns the setup step 1 screen markup.
    *
    * @return string HTML
    */
  public function setup()
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
    $theme = $this->actualTheme;
    $themes = $this->theme->getThemes();

    $errors = $this->verifyRequirements($imageLibs);

    if(count($errors) > 0)
      $step = 0;
    else
      $errors = '';

    $email = '';
    if(getConfig()->get('user') != null)
      $email = getConfig()->get('user')->email;
    elseif($this->user->isLoggedIn())
      $email = getSession()->get('email');

    $qs = '';
    if(isset($_GET['edit']))
      $qs = '?edit';

    $template = sprintf('%s/setup.php', getConfig()->get('paths')->templates);
    $body = $this->template->get($template, array('filesystem' => $filesystem, 'database' => $database, 'themes' => $themes, 'theme' => $theme,
      'imageLibs' => $imageLibs, 'imageLibrary' => $imageLibrary, 'appId' => $appId, 'step' => $step, 'email' => $email, 'password' => '', 'qs' => $qs, 'errors' => $errors));
    $this->theme->display('template.php', array('body' => $body, 'page' => 'setup'));
  }

  /**
    * Gets Dropbox info from the user
    *
    * @return string HTML
    */
  public function setupDropbox()
  {
    extract($this->getDefaultConfigParams());
    $secret = $this->getSecret();
    $credentials = getConfig()->get('credentials');
    $dropbox = getConfig()->get('dropbox');
    if($credentials !== null)
    {
      if(isset($credentials->dropboxKey) && !empty($credentials->dropboxKey))
        $dropboxKey = $this->utility->decrypt($credentials->dropboxKey, $secret);
      if(isset($credentials->dropboxSecret) && !empty($credentials->dropboxSecret))
        $dropboxSecret = $this->utility->decrypt($credentials->dropboxSecret, $secret);
      if(isset($dropbox->dropboxFolder))
        $dropboxFolder = $dropbox->dropboxFolder;
    }

    $qs = '';
    if(isset($_GET['edit']))
      $qs = '?edit';

    $template = sprintf('%s/setup-dropbox.php', getConfig()->get('paths')->templates);
    $body = $this->template->get($template, array('dropboxKey' => $dropboxKey, 'dropboxSecret' => $dropboxSecret, 'dropboxFolder' => $dropboxFolder, 'qs' => $qs));
    $this->theme->display('template.php', array('body' => $body, 'page' => 'setup'));
  }

  /**
    * Handles callback from Dropbox oauth flow
    *
    * @return void HTTP redirect (to dropbox.com)
    */
  public function setupDropboxCallback()
  {
    $secret = $this->getSecret();
    try
    {
      $dropboxToken = getSession()->get('dropboxToken');
      $dropboxKey = $this->utility->decrypt(getSession()->get('flowDropboxKey'), $secret);
      $dropboxSecret = $this->utility->decrypt(getSession()->get('flowDropboxSecret'), $secret);
      $oauth = new Dropbox_OAuth_PHP($dropboxKey, $dropboxSecret);
      $oauth->setToken($dropboxToken);
      $accessToken = $oauth->getAccessToken();
      getSession()->set('dropboxFolder', getSession()->get('flowDropboxFolder'));
      getSession()->set('dropboxKey', getSession()->get('flowDropboxKey'));
      getSession()->set('dropboxSecret', getSession()->get('flowDropboxSecret'));
      getSession()->set('dropboxToken', $this->utility->encrypt($accessToken['token'], $secret));
      getSession()->set('dropboxTokenSecret', $this->utility->encrypt($accessToken['token_secret'], $secret));

      $qs = '';
      if(isset($_GET['edit']))
        $qs = '?edit';

      $this->route->redirect(sprintf('%s%s', '/setup/3', $qs));
    }
    catch(Dropbox_Exception $e)
    {
      getLogger()->crit(sprintf('An error occured getting the Dropbox authorize url. Message: %s', $e->getMessage()));
      $this->route->run('/error/500');
    }
  }

  /**
    * Gets authorize URL and redirects to dropbox
    *
    * @return void HTTP redirect (to dropbox.com)
    */
  public function setupDropboxPost()
  {
    $qs = '';
    if(isset($_GET['edit']))
      $qs = '?edit';
    $secret = $this->getSecret();

    try
    {
      getSession()->set('flowDropboxKey', $this->utility->encrypt($_POST['dropboxKey'], $secret));
      getSession()->set('flowDropboxSecret', $this->utility->encrypt($_POST['dropboxSecret'], $secret));
      getSession()->set('flowDropboxFolder', $_POST['dropboxFolder']);
      $callback = urlencode(sprintf('%s://%s%s%s', $this->utility->getProtocol(false), getenv('HTTP_HOST'), '/setup/dropbox/callback', $qs));
      $oauth = new Dropbox_OAuth_PHP($_POST['dropboxKey'], $_POST['dropboxSecret']);
      getSession()->set('dropboxToken', $oauth->getRequestToken());
      $url = $oauth->getAuthorizeUrl($callback);
      $this->route->redirect($url, null, true);
    }
    catch(Dropbox_Exception $e)
    {
      getLogger()->crit('An error occured getting the Dropbox authorize url.', $e);
      $this->route->run('/error/500', EpiRoute::httpGet);
    }
  }

  /**
    * Posts the setup values from step 1 of the form, checks them, and saves in session
    *
    * @return void HTTP redirect (setup step 2)
    */
  public function setupPost()
  {
    $step = 1;
    $appId = isset($_POST['appId']) ? $_POST['appId'] : '';
    $email = isset($_POST['email']) ? $_POST['email'] : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';
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
      getSession()->set('password', $password);
      getSession()->set('theme', $theme);

      $qs = '';
      if(isset($_GET['edit']))
        $qs = '?edit';

      $this->route->redirect('/setup/2' . $qs);
    }

    $template = sprintf('%s/setup.php', getConfig()->get('paths')->templates);
    $body = $this->template->get($template, array('email' => $email, 'password' => $password, 'appId' => $appId, 'step' => $step, 'errors' => $errors));
    $this->theme->display('template.php', array('body' => $body, 'page' => 'setup'));
  }

  /**
    * Returns the setup step 2 screen markup.
    *
    * @return string HTML
    */
  public function setup2()
  {
    // make sure the user should be on this step
    if(getSession()->get('step') != 2)
        $this->route->redirect('/setup');

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
    $body = $this->template->get($template, array('themes' => array(), 'imageLibs' => $imageLibs, 'appId' => 'openphoto-frontend', 'imageLibrary' => $imageLibrary, 'database' => $database, 'filesystem' => $filesystem, 'qs' => $qs, 'step' => $step));
    $this->theme->display('template.php', array('body' => $body, 'page' => 'setup'));
  }

  /**
    * Posts the setup values from step 2 of the form, checks them, and saves in session
    *
    * @return void HTTP redirect (setup step 3)
    */
  public function setup2Post()
  {
      getSession()->set('step', 3);
      getSession()->set('imageLibrary', $_POST['imageLibrary']);
      getSession()->set('database', $_POST['database']);
      getSession()->set('fileSystem', $_POST['fileSystem']);

      $qs = '';
      if(isset($_GET['edit']))
        $qs = '?edit';
      if(stristr($_POST['fileSystem'], 'Dropbox') !== false)
        $this->route->redirect('/setup/dropbox' . $qs);
      else
        $this->route->redirect('/setup/3' . $qs);
  }

  /**
    * Returns the setup step 3 screen markup.
    *
    * @return string HTML
    */
  public function setup3()
  {
    // make sure the user should be on this step
    if(getSession()->get('step') != 3)
    {
      if(getSession()->get('step') == 2)
        $this->route->redirect('/setup/2');

      $this->route->redirect('/setup');
    }

    extract($this->getDefaultConfigParams());
    $secret = $this->getSecret();
    $step = 3;
    $password = getSession()->get('password');
    $appId = getSession()->get('appId');
    $database = getSession()->get('database');
    $filesystem = getSession()->get('fileSystem');
    $usesAws = (getSession()->get('database') == 'SimpleDb' || preg_match('/S3|DreamObjects/', getSession()->get('fileSystem'))) ? true : false;
    $usesMySql = (getSession()->get('database') == 'MySql') ? true : false;
    $usesLocalFs = (stristr(getSession()->get('fileSystem'), 'Local') !== false) ? true : false;
    $usesS3 = (preg_match('/S3|DreamObjects/', getSession()->get('fileSystem')) !== false) ? true : false;
    $usesDropbox = (stristr(getSession()->get('fileSystem'), 'Dropbox') !== false) ? true : false;
    $usesSimpleDb = (getSession()->get('database') == 'SimpleDb') ? true : false;

    $dropboxKey = getSession()->get('dropboxKey');
    if(!empty($dropboxKey))
    {
      $dropboxFolder = getSession()->get('dropboxFolder');
      $dropboxKey = $this->utility->decrypt(getSession()->get('dropboxKey'), $secret);
      $dropboxSecret = $this->utility->decrypt(getSession()->get('dropboxSecret'), $secret);
      $dropboxToken = $this->utility->decrypt(getSession()->get('dropboxToken'), $secret);
      $dropboxTokenSecret = $this->utility->decrypt(getSession()->get('dropboxTokenSecret'), $secret);
    }
    if(getConfig()->get('credentials') != null)
    {
      $credentials = getConfig()->get('credentials');
      if(isset($credentials->awsKey))
        $awsKey = $this->utility->decrypt($credentials->awsKey, $secret);
      if(isset($credentials->awsSecret))
        $awsSecret = $this->utility->decrypt($credentials->awsSecret, $secret);
      if(empty($dropboxKey))
      {
        if(isset($credentials->dropboxKey))
          $dropboxKey = $this->utility->decrypt($credentials->dropboxKey, $secret);
        if(isset($credentials->dropboxSecret))
          $dropboxSecret = $this->utility->decrypt($credentials->dropboxSecret, $secret);
        if(isset($credentials->dropboxToken))
          $dropboxToken = $this->utility->decrypt($credentials->dropboxToken, $secret);
        if(isset($credentials->dropboxTokenSecret))
          $dropboxTokenSecret = $this->utility->decrypt($credentials->dropboxTokenSecret, $secret);
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
      $mySqlPassword = $this->utility->decrypt($mysql->mySqlPassword, $secret);
      $mySqlDb = $mysql->mySqlDb;
      $mySqlTablePrefix = $mysql->mySqlTablePrefix;
    }

    if(getConfig()->get('localfs') != null)
    {
      $fsRoot = getConfig()->get('localfs')->fsRoot;
      $fsHost = getConfig()->get('localfs')->fsHost;
    }
    else
    {
      $fsHost = sprintf('%s/photos', getenv('HTTP_HOST'));
      $fsRoot = sprintf('%s/html/photos', dirname(dirname(dirname(__FILE__))));
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
    $body = $this->template->get($template, array('step' => $step, 'password' => $password,'themes' => $themes, 'usesAws' => $usesAws, 'usesMySql' => $usesMySql,
      'database' => $database, 'filesystem' => $filesystem, 'usesLocalFs' => $usesLocalFs, 'usesS3' => $usesS3,
      'usesSimpleDb' => $usesSimpleDb, 'awsKey' => $awsKey, 'awsSecret' => $awsSecret, 's3Bucket' => $s3Bucket,
      'simpleDbDomain' => $simpleDbDomain, 'mySqlHost' => $mySqlHost, 'mySqlUser' => $mySqlUser, 'mySqlDb' => $mySqlDb,
      'mySqlPassword' => $mySqlPassword, 'mySqlTablePrefix' => $mySqlTablePrefix, 'fsRoot' => $fsRoot, 'fsHost' => $fsHost,
      'usesDropbox' => $usesDropbox, 'dropboxKey' => $dropboxKey, 'dropboxSecret' => $dropboxSecret, 'dropboxToken' => $dropboxToken,
      'dropboxTokenSecret' => $dropboxTokenSecret, 'dropboxFolder' => $dropboxFolder, 'qs' => $qs, 'appId' => $appId, 'errors' => $errors));

    $this->theme->display('template.php', array('body' => $body, 'page' => 'setup'));
  }

  /**
    * Posts the setup values from step 3 of the form, checks them, and saves in session
    *
    * @return void HTTP redirect (home)
    */
  public function setup3Post()
  {
    getSession()->set('isEditMode', isset($_GET['edit']));
    $isEditMode = getSession()->get('isEditMode');
    extract($this->getDefaultConfigParams());
    $step = 3;
    $secret = $this->getSecret();
    $database = getSession()->get('database');
    $filesystem = getSession()->get('fileSystem');
    $appId = getSession()->get('appId');
    $password = getSession()->get('password');
    $usesAws = (getSession()->get('database') == 'SimpleDb' || preg_match('/S3|DreamObjects/', $filesystem)) ? true : false;
    $usesMySql = (getSession()->get('database') == 'MySql') ? true : false;
    $usesSimpleDb = (getSession()->get('database') == 'SimpleDb') ? true : false;
    $usesLocalFs = (stristr(getSession()->get('fileSystem'), 'Local') !== false) ? true : false;
    $usesS3 = (preg_match('/S3|DreamObjects/', $filesystem) !== false) ? true : false;
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
        array('MySQL Password', $mySqlPassword),
        array('MySQL Database', $mySqlDb, 'required')
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
        getSession()->set('awsKey', $this->utility->encrypt($awsKey, $secret));
        getSession()->set('awsSecret', $this->utility->encrypt($awsSecret, $secret));
        $credentials->awsKey = $this->utility->encrypt($awsKey, $secret);
        $credentials->awsSecret = $this->utility->encrypt($awsSecret, $secret);

        $aws = new stdClass;
        if($usesS3)
        {
          getSession()->set('s3BucketName', $s3Bucket);
          $aws->s3BucketName = $s3Bucket;
          if($filesystem === 'DreamObjects')
            $aws->s3Host = "{$s3Bucket}.objects.dreamhost.com";
          else
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
        getSession()->set('mySqlPassword', $this->utility->encrypt($mySqlPassword, $secret));
        getSession()->set('mySqlDb', $mySqlDb);
        getSession()->set('mySqlTablePrefix', $mySqlTablePrefix);
        $mysql = new stdClass;
        $mysql->mySqlHost = $mySqlHost;
        $mysql->mySqlUser = $mySqlUser;
        $mysql->mySqlPassword = $this->utility->encrypt($mySqlPassword, $secret);
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
        getSession()->set('dropboxKey', $this->utility->encrypt($dropboxKey, $secret));
        getSession()->set('dropboxSecret', $this->utility->encrypt($dropboxSecret, $secret));
        getSession()->set('dropboxToken', $this->utility->encrypt($dropboxToken, $secret));
        getSession()->set('dropboxTokenSecret', $this->utility->encrypt($dropboxTokenSecret, $secret));
        getSession()->set('dropboxFolder', $dropboxFolder);
        $credentials->dropboxKey = $this->utility->encrypt($dropboxKey, $secret);
        $credentials->dropboxSecret = $this->utility->encrypt($dropboxSecret, $secret);
        $credentials->dropboxToken = $this->utility->encrypt($dropboxToken, $secret);
        $credentials->dropboxTokenSecret = $this->utility->encrypt($dropboxTokenSecret, $secret);
        $dropbox = new stdClass;
        $dropbox->dropboxFolder = $dropboxFolder;
      }

      $systems = new stdClass;
      $systems->database = getSession()->get('database');
      $systems->fileSystem = getSession()->get('fileSystem');
      $secrets = new stdClass;
      $secrets->secret = $this->getSecret();

      $user = new stdClass;
      $user->email = getSession()->get('ownerEmail');

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
      getConfig()->set('user', $user);

      $fsObj = getFs();
      $dbObj = getDb();

      $serverUser = exec("whoami");
      if(!$fsObj->initialize($isEditMode))
      {
        if($usesS3)
          $fsErrors[] = 'We were unable to initialize your S3 bucket.<ul><li>Make sure you\'re <a href="http://aws.amazon.com/s3/">signed up for AWS S3</a>.</li><li>Double check your AWS credentials.</li><li>S3 bucket names are globally unique, make sure yours isn\'t already in use by someone else.</li><li>S3 bucket names can\'t have certain special characters. Try using just alpha-numeric characters and periods.</li></ul>';
        else if($usesLocalFs)
          $fsErrors[] = "We were unable to set up your local file system using <em>{$fsObj->getRoot()}</em>. Make sure that the following user has proper permissions ({$serverUser}).";
        else
          $fsErrors[] = 'An unknown error occurred while setting up your file system. Check your error logs to see if there\'s more information about the error.';
      }
      if(!$dbObj->initialize($isEditMode))
      {
        if($usesSimpleDb)
          $dbErrors[] = 'We were unable to initialize your SimpleDb domains.<ul><li>Make sure you\'re <a href="http://aws.amazon.com/simpledb/">signed up for AWS SimpleDb</a>.</li><li>Double check your AWS credentials.</li><li>SimpleDb domains cannot contain special characters such as periods.</li><li>Sometimes the SimpleDb create domain API is unstable. Try again later or check the error log if you have access to it.</li></ul>';
        else if($usesMySql)
          $dbErrors[] = 'We were unable to initialize your account in MySql. <ul><li>Please verify that the host, username and password are correct and have proper permissions to create a database.</li><li>Make sure your email address is not already in use.</li></ul>';
        else
          $dbErrors[] = 'An unknown error occurred while setting up your database. Check your error logsto see if there\'s more information about the error.';

        $dbErrors = array_merge($dbErrors, $dbObj->errors());
      }
      try {
        if(getConfig()->get('site')->allowOpenPhotoLogin == 1) {
          if($isEditMode)
            $dbObj->postUser(array('password' => sha1(sprintf('%s-%s', $password, getConfig()->get('secrets')->passwordSalt))));
          else
            $dbObj->putUser(array('password' => sha1(sprintf('%s-%s', $password, getConfig()->get('secrets')->passwordSalt))));
        }
        else {
          if($isEditMode)
            $dbObj->postUser(array('password' => ''));
          else
            $dbObj->putUser(array('password' => ''));
        }
      }
      catch(Exception $e) {
        getLogger()->warn($e->getMessage());
      }

      if($fsErrors === false && $dbErrors === false)
      {
        $writeError = $this->writeConfigFile();
        if($writeErrors === false)
        {
          if(isset($_GET['edit']))
          {
            $this->route->redirect('/?m=welcome');
          }
          else
          {
            // setting up a new site, we should log them in and redirect them to the upload form (Gh-290)
            $this->user->setEmail($user->email);
            $this->route->redirect('/photos/upload?m=welcome');
          }
        }
        else
        {
          $writeErrors[] = "We were unable to save your settings file. Please make sure that the following user has proper permissions to write to src/configs ({$user}).";
        }
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
    $body = $this->template->get($template, array('step' => $step, 'password' => $password,'themes' => $themes, 'usesAws' => $usesAws, 'usesMySql' => $usesMySql,
      'database' => $database, 'filesystem' => $filesystem, 'usesLocalFs' => $usesLocalFs, 'usesS3' => $usesS3,
      'usesSimpleDb' => $usesSimpleDb, 'awsKey' => $awsKey, 'awsSecret' => $awsSecret, 's3Bucket' => $s3Bucket,
      'simpleDbDomain' => $simpleDbDomain, 'mySqlHost' => $mySqlHost, 'mySqlUser' => $mySqlUser, 'mySqlDb' => $mySqlDb,
      'mySqlPassword' => $mySqlPassword, 'mySqlTablePrefix' => $mySqlTablePrefix, 'fsRoot' => $fsRoot, 'fsHost' => $fsHost,
      'usesDropbox' => $usesDropbox, 'dropboxKey' => $dropboxKey, 'dropboxSecret' => $dropboxSecret, 'dropboxToken' => $dropboxToken,
      'dropboxTokenSecret' => $dropboxTokenSecret, 'dropboxFolder' => $dropboxFolder, 'qs' => $qs, 'appId' => $appId, 'errors' => $errors));
    $this->theme->display('template.php', array('body' => $body, 'page' => 'setup'));
  }

  /**
    * Clears out the session data and redirects to step 1
    *
    * @return void HTTP redirect (setup step 1)
    */
  public function setupRestart()
  {
    getSession()->end();
    $this->route->redirect('/setup');
  }

  public function getSecret()
  {
    if(getConfig()->get('secrets') !== null && isset(getConfig()->get('secrets')->secret))
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

  private function getDefaultConfigParams()
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
  private function verifyRequirements($imageLibs)
  {
    $errors = array();
    $configDir = $this->utility->getBaseDir() . '/userdata';
    $generatedDir = "{$configDir}/configs";
    $assetsDir = $this->utility->getBaseDir() . '/html/assets/cache';

    // No errors, return empty array
    if(function_exists('exif_read_data') && file_exists($generatedDir) && is_writable($generatedDir) && file_exists($assetsDir) && is_writable($assetsDir) && !empty($imageLibs))
      return $errors;

    $user = exec("whoami");
    if(empty($user))
      $user = 'Apache user';

    if(!function_exists('exif_read_data'))
      $errors[] = 'We could not find PHP\'s exif functions. Please install php5-exif.';

    if(!is_writable($configDir))
      $errors[] = "Insufficient privileges to complete setup.<ul><li>Make sure the user <em>{$user}</em> can write to <em>{$configDir}</em>.</li></ul>";

    if(!file_exists($generatedDir))
    {
      $createDir = @mkdir($generatedDir, 0700);
      if(!$createDir)
        $errors[] = "Could not create configuration directory.<ul><li>Make sure the user <em>{$user}</em> can write to <em>{$generatedDir}</em>.</li></ul>";
    }
    elseif(!is_writable($generatedDir))
    {
      $errors[] = "Directory exist but is not writable.<ul><li>Make sure the user <em>{$user}</em> can write to <em>{$generatedDir}</em>.</li></ul>";
    }

    if(!file_exists($assetsDir))
    {
      $createDir = @mkdir($assetsDir, 0700);
      if(!$createDir)
        $errors[] = "Could not create assets cache directory.<ul><li>Make sure the user <em>{$user}</em> can write to <em>{$assetsDir}</em>.</li></ul>";
    }
    elseif(!is_writable($assetsDir))
    {
      $errors[] = "Directory exist but is not writable.<ul><li>Make sure the user <em>{$user}</em> can write to <em>{$assetsDir}</em>.</li></ul>";
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
  private function writeConfigFile()
  {
    // continue if no errors
    $secret = $this->getSecret();
    $baseDir = $this->utility->getBaseDir();
    $htmlDir = "{$baseDir}/html";
    $libDir = "{$baseDir}/libraries";
    $configDir = "{$baseDir}/configs";
    $replacements = array(
      '{adapters}' => "{$libDir}/adapters",
      '{configs}' => $configDir,
      '{controllers}' => "{$libDir}/controllers",
      '{docroot}' => "{$htmlDir}",
      '{external}' => "{$libDir}/external",
      '{libraries}' => "{$libDir}",
      '{models}' => "{$libDir}/models",
      '{photos}' => "{$htmlDir}/photos",
      '{plugins}' => "{$libDir}/plugins",
      '{templates}' => "{$baseDir}/templates",
      '{themes}' => "{$htmlDir}/assets/themes",
      '{temp}' => sys_get_temp_dir(),
      '{userdata}' => "{$baseDir}/userdata",
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
      '{lastCodeVersion}' => getConfig()->get('defaults')->currentCodeVersion,
      '{theme}' => getSession()->get('theme'),
      '{email}' => getSession()->get('ownerEmail')
    );
    // Session keys whose value it is ok to log.
    // Other session keys available at this point are:
    //   awsKey, awsSecret, dropboxKey, dropboxSecret, dropboxToken, dropboxTokenSecret,
    //   flowDropboxKey, flowDropboxSecret, mySqlPassword, mySqlUser, password, secret, step
    // It is safer to explicitly list keys that are ok to log, rather than exclude those that are
    // sensitive, as one might forget to exclude new keys.
    $settingsToLog = array('step', 'appId', 'ownerEmail', 'isEditMode', 'theme', 'imageLibrary', 'database', 'simpleDbDomain', 'mySqlDb', 'mySqlHost', 'mySqlTablePrefix', 'fileSystem', 'fsHost', 'fsRoot', 'dropboxFolder', 'flowDropboxFolder', 's3BucketName');

    $pReplace = array();
    $session = getSession()->getAll();
    foreach($session as $key => $val)
    {
      if($key != 'email' && $key != 'password')
        $pReplace["{{$key}}"] = $val;

      // Write keys to the log file. If key is in whitelist then log the value as well.
      if(in_array($key, $settingsToLog))
        $logMessage = sprintf("Storing `%s` as '%s'", $key, $val);
      else
        $logMessage = sprintf("Storing `%s`", $key);

      getLogger()->info($logMessage);
    }

    $replacements = array_merge($replacements, $pReplace);
    $generatedIni = str_replace(
      array_keys($replacements),
      array_values($replacements),
      file_get_contents("{$configDir}/template.ini")
    );

    $iniWritten = getConfig()->write(sprintf("%s/userdata/configs/%s.ini", $baseDir, getenv('HTTP_HOST')), $generatedIni);
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
