<?php
// TODO, remove these
date_default_timezone_set('America/Los_Angeles');

if(isset($_GET['__route__']) && strstr($_GET['__route__'], '.json'))
  header('Content-type: application/json');

$basePath = dirname(dirname(__FILE__));
$epiPath = "{$basePath}/libraries/external/epi";
require "{$epiPath}/Epi.php";
require "{$basePath}/libraries/compatability.php";
require "{$basePath}/libraries/models/UserConfig.php";

Epi::setSetting('exceptions', true);
Epi::setPath('base', $epiPath);
Epi::setPath('config', "{$basePath}/configs");
Epi::setPath('view', '');
Epi::init('api','debug','cache','config','curl','form','logger','route','session','template','database');

$routeObj = getRoute();
$apiObj = getApi();

// loads configs and dependencies
$userConfigObj = new UserConfig;
$hasConfig = $userConfigObj->load();
$configObj = getConfig();

// set log level
$levels = (array)explode(',', $configObj->get('epi')->logLevels);
call_user_func_array('EpiLogger::employ', $levels);

// initialize session
EpiCache::employ($configObj->get('epi')->cache);
$sessionParams = array($configObj->get('epi')->session);
if($configObj->get('epiSessionParams')) {
  $sessionParams = array_merge($sessionParams, (array)$configObj->get('epiSessionParams'));
  // for TLDs we need to override the cookie domain if specified
  if(isset($sessionParams['domain']) && stristr($_SERVER['HTTP_HOST'], $sessionParams['domain']) === false)
    $sessionParams['domain'] = $_SERVER['HTTP_HOST'];

  $sessionParams = array_values($sessionParams); // reset keys
}
EpiSession::employ($sessionParams);
getSession();

// load theme after everything is initialized
// this initializes user which extends BaseModel and gets session, config and cache objects
$userConfigObj->loadTheme();

// determine if this is a login endpoint
$loginEndpoint = $assetEndpoint = false;
if(isset($_GET['__route__']) && preg_match('#/user/(.*)(login|logout)#', $_GET['__route__']))
  $loginEndpoint = true;

if(isset($_GET['__route__']) && preg_match('#^/assets#', $_GET['__route__']))
  $assetEndpoint = true;

// determine if this is a setup endpoint
$runSetup = false;
if($hasConfig && isset($_SERVER['REQUEST_URI']) && strpos($_SERVER['REQUEST_URI'], '/setup') !== false && isset($_GET['edit']))
  $runSetup = true;


// if the config file exists and we're not running the setup, proceed as normal
if($hasConfig && !$runSetup)
{
  // check if the system needs to upgraded for new code
  $runUpgrade = false;
  if(!getUpgrade()->isCurrent())
    $runUpgrade = true;

  Request::setApiVersion();

  // initializes plugins
  getPlugin()->load();

  require $configObj->get('paths')->libraries . '/routes.php';
}
else
{
  $runUpgrade = false;
  $runSetup = true;

  // setup and enable routes for setup
  $baseDir = dirname(dirname(__FILE__));
  $paths = new stdClass;
  $paths->libraries = "{$baseDir}/libraries";
  $paths->configs = "{$baseDir}/configs";
  $paths->controllers = "{$baseDir}/libraries/controllers";
  $paths->docroot = "{$baseDir}/html";
  $paths->external = "{$baseDir}/libraries/external";
  $paths->adapters = "{$baseDir}/libraries/adapters";
  $paths->models = "{$baseDir}/libraries/models";
  $paths->templates = "{$baseDir}/templates";
  $paths->themes = "{$baseDir}/html/assets/themes";
  $configObj->set('paths', $paths);

  if(!$hasConfig)
    require $configObj->get('paths')->libraries . '/dependencies.php';

  require $configObj->get('paths')->libraries . '/routes-setup.php';
  require $configObj->get('paths')->libraries . '/routes-error.php';
  require $configObj->get('paths')->controllers . '/SetupController.php';
  $configObj->loadString(file_get_contents(sprintf('%s/html/assets/themes/%s/config/settings.ini', dirname(dirname(__FILE__)), getTheme()->getThemeName())));

  // Before we run the setup in edit mode, we need to validate ownership
  $userObj = new User;
  if(isset($_GET['edit']) && !$userObj->isOwner())
  {
    $routeObj->run('/error/403');
    die();
  }
}
