<?php
// TODO, remove these
date_default_timezone_set('America/Los_Angeles');

if(isset($_GET['__route__']) && strstr($_GET['__route__'], '.json'))
  header('Content-type: application/json');

$basePath = dirname(dirname(__FILE__));
$epiPath = "{$basePath}/libraries/external/epi";
require "{$epiPath}/Epi.php";

Epi::setSetting('exceptions', true);
Epi::setPath('base', $epiPath);
Epi::setPath('config', "{$basePath}/configs");
Epi::setPath('view', '');
Epi::init('api','cache','config','curl','form','logger','route','session-php','template','database');
EpiSession::employ(EpiSession::PHP);
getSession();

getConfig()->load('defaults.ini');
$configFile = sprintf('%s/generated/%s.ini', Epi::getPath('config'), getenv('HTTP_HOST'));

$loginEndpoint = false;
if(isset($_GET['__route__']) && preg_match('#/user/(login|logout)#', $_GET['__route__']))
  $loginEndpoint = true;
$runSetup = false;
if(file_exists($configFile) && strpos($_SERVER['REQUEST_URI'], '/setup') !== false && isset($_GET['edit']))
  $runSetup = true;

// if the config file exists and we're not running the setup, proceed as normal
if(file_exists($configFile) && !$runSetup)
{
  getConfig()->load(sprintf('generated/%s.ini', getenv('HTTP_HOST')));
  require getConfig()->get('paths')->libraries . '/dependencies.php';

  // check if the system needs to upgraded for new code
  $runUpgrade = false;
  if(!getUpgrade()->isCurrent())
    $runUpgrade = true;
  require getConfig()->get('paths')->libraries . '/routes.php';

  getConfig()->load(sprintf('%s/html/assets/themes/%s/config/settings.ini', dirname(dirname(__FILE__)), getTheme()->getThemeName()));
  if(Utility::isMobile() && file_exists($mobileSettings = sprintf('%s/html/assets/themes/%s/config/settings-mobile.ini', dirname(dirname(__FILE__)), getTheme(false)->getThemeName())))
    getConfig()->load($mobileSettings);
}
else
{
  // if we're running setup and the config file exists, load it to prepopulate the form
  if(file_exists($configFile))
    getConfig()->load(sprintf('generated/%s.ini', getenv('HTTP_HOST')));
    
  // setup and enable routes for setup
  $baseDir = dirname(dirname(__FILE__));
  $paths = new stdClass;
  $paths->libraries = "{$baseDir}/libraries";
  $paths->controllers = "{$baseDir}/libraries/controllers";
  $paths->external = "{$baseDir}/libraries/external";
  $paths->adapters = "{$baseDir}/libraries/adapters";
  $paths->models = "{$baseDir}/libraries/models";
  $paths->templates = "{$baseDir}/templates";
  $paths->themes = "{$baseDir}/html/assets/themes";
  getConfig()->set('paths', $paths);
  require getConfig()->get('paths')->libraries . '/routes-setup.php';
  require getConfig()->get('paths')->libraries . '/dependencies.php';
  require getConfig()->get('paths')->controllers . '/SetupController.php';
  getConfig()->load(sprintf('%s/html/assets/themes/%s/config/settings.ini', dirname(dirname(__FILE__)), getTheme()->getThemeName()));

  // Before we run the setup in edit mode, we need to validate ownership
  if(isset($_GET['edit']) && !User::isOwner())
    getRoute()->run('/error/403');
}
