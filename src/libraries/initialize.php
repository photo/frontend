<?php
// TODO, remove these
date_default_timezone_set('America/Los_Angeles');

if(isset($_GET['__route__']) && strstr($_GET['__route__'], '.json'))
  header('Content-type: application/json');

$basePath = dirname(dirname(__FILE__));
$epiPath = "{$basePath}/libraries/external/epi";
require "{$epiPath}/Epi.php";

Epi::setPath('base', $epiPath);
Epi::setPath('config', "{$basePath}/configs");
Epi::setPath('view', '');
Epi::init('api','cache','config','curl','form','logger','route','session-php','template','database');
EpiSession::employ(EpiSession::PHP);
getSession();

getConfig()->load('defaults.ini');
getConfig()->load(sprintf('%s/html/assets/themes/%s/config/settings.ini', dirname(dirname(__FILE__)), getConfig()->get('site')->theme));
$configFile = sprintf('%s/generated/%s.ini', Epi::getPath('config'), getenv('HTTP_HOST'));

$runSetup = false;
if(file_exists($configFile) && strpos($_SERVER['REQUEST_URI'], '/setup') !== false && isset($_GET['edit']))
  $runSetup = true;

if(file_exists($configFile) && !$runSetup)
{
  getConfig()->load(sprintf('generated/%s.ini', getenv('HTTP_HOST')));
  require getConfig()->get('paths')->libraries . '/dependencies.php';
  if(Utility::isMobile() && file_exists($mobileSettings = sprintf('%s/html/assets/themes/%s/config/settings-mobile.ini', dirname(dirname(__FILE__)), getConfig()->get('site')->theme)))
    getConfig()->load($mobileSettings);
}
else
{
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

  // Before we run the setup in edit mode, we need to validate ownership
  if(isset($_GET['edit']) && !User::isOwner())
    getRoute()->run('/error/403');
}
