<?php
/**
 * Front controller for OpenPhoto.
 *
 * This file takes all requests and dispatches them to the appropriate controller.
 * @author Jaisen Mathai <jaisen@jmathai.com>
 */

// TODO, remove these
date_default_timezone_set('America/Los_Angeles');

if(isset($_GET['__route__']) && strstr($_GET['__route__'], '.json'))
  header('Content-type: application/json');

$basePath = dirname(dirname(__FILE__));
$epiPath = "{$basePath}/libraries/external/epi";
require "{$epiPath}/Epi.php";
Epi::setPath('base', $epiPath);
Epi::setPath('config', "{$basePath}/configs");
//Epi::setPath('view', "{$basePath}/views");
Epi::setPath('view', '');
//Epi::setSetting('exceptions', true);
Epi::init('api','cache','config','form','logger','route','session-php','template','database');
// TODO allow configurable session engine
EpiSession::employ(EpiSession::PHP);
// This initializes the session. Needed for PHP sessions to implicitly call session_start();
getSession();

getConfig()->load('defaults.ini');
getConfig()->load(sprintf('%s/assets/themes/%s/config/settings.ini', dirname(__FILE__), getConfig()->get('site')->theme));
$configFile = Epi::getPath('config').'/generated/settings.ini';
if(file_exists($configFile))
{
  getConfig()->load('generated/settings.ini');
  // load all dependencies
  require getConfig()->get('paths')->libraries . '/dependencies.php';
  getRoute()->run();
}
elseif(!file_exists($configFile)) // if no config file then load up the setup dependencies
{
  // setup and enable routes for setup
  $baseDir = dirname(dirname(__FILE__));
  $paths = new stdClass;
  $paths->libraries = "{$baseDir}/libraries";
  $paths->controllers = "{$baseDir}/libraries/controllers";
  $paths->external = "{$baseDir}/libraries/external";
  $paths->adapters = "{$baseDir}/libraries/adapters";
  $paths->models = "{$baseDir}/libraries/models";
  $paths->themes = "{$baseDir}/html/assets/themes";
  getConfig()->set('paths', $paths);
  require getConfig()->get('paths')->libraries . '/dependencies.php';
  require getConfig()->get('paths')->libraries . '/routes-setup.php';
  require getConfig()->get('paths')->controllers . '/SetupController.php';

  // if we're not in the setup path (anything other than /setup) then redirect to the setup
  // otherwise we're on one of the setup steps already, so just run it
  if(!isset($_GET['__route__']) || strpos($_GET['__route__'], '/setup') === false)
    getRoute()->run('/setup');
  else
    getRoute()->run();
}
