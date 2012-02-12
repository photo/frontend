<?php
// TODO, remove these
date_default_timezone_set('America/Los_Angeles');

if(isset($_GET['__route__']) && strstr($_GET['__route__'], '.json'))
  header('Content-type: application/json');

$basePath = dirname(dirname(__FILE__));
$epiPath = "{$basePath}/libraries/external/epi";
require "{$epiPath}/Epi.php";
require "{$basePath}/libraries/models/UserConfig.php";

Epi::setSetting('exceptions', true);
Epi::setPath('base', $epiPath);
Epi::setPath('config', "{$basePath}/configs");
Epi::setPath('view', '');
Epi::init('api','cache','config','curl','form','logger','route','session','template','database');

// loads configs and dependencies
$userConfigObj = new UserConfig;
$hasConfig = $userConfigObj->load();

EpiCache::employ(getConfig()->get('epi')->cache);
EpiSession::employ(getConfig()->get('epi')->session);
getSession();

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
  require getConfig()->get('paths')->libraries . '/routes.php';

  // initializes plugins
  getPlugin()->load();
  getPlugin()->invoke('onLoad');
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
  getConfig()->set('paths', $paths);

  if(!$hasConfig)
    require getConfig()->get('paths')->libraries . '/dependencies.php';

  require getConfig()->get('paths')->libraries . '/routes-setup.php';
  require getConfig()->get('paths')->libraries . '/routes-error.php';
  require getConfig()->get('paths')->controllers . '/SetupController.php';
  getConfig()->loadString(file_get_contents(sprintf('%s/html/assets/themes/%s/config/settings.ini', dirname(dirname(__FILE__)), getTheme()->getThemeName())));

  // Before we run the setup in edit mode, we need to validate ownership
  $userObj = new User;
  if(isset($_GET['edit']) && !$userObj->isOwner())
    getRoute()->run('/error/403');
}
