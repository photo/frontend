<?php
/**
 * Front controller for OpenPhoto.
 *
 * This file takes all requests and dispatches them to the appropriate controller.
 * @author Jaisen Mathai <jaisen@jmathai.com>
 */

// TODO, remove these
date_default_timezone_set('America/Los_Angeles');

$basePath = dirname(dirname(__FILE__));
$epiPath = "{$basePath}/libraries/external/epi";
require "{$epiPath}/Epi.php";
Epi::setPath('base', $epiPath);
Epi::setPath('config', "{$basePath}/configs");
Epi::setPath('view', "{$basePath}/views");
//Epi::setSetting('exceptions', true);
Epi::init('api','config','logger','route','session-php','template');
// TODO allow configurable session engine
EpiSession::employ(EpiSession::PHP);
// This initializes the session. Needed for PHP sessions to implicitly call session_start();
getSession();


getConfig()->load('defaults.ini');
$configFile = Epi::getPath('config').'/generated/settings.ini';
if(file_exists($configFile))
{
  getConfig()->load('generated/settings.ini');
  // load all dependencies
  require getConfig()->get('paths')->libraries . '/dependencies.php';
  getRoute()->run();
}
elseif(!file_exists($configFile))
{
  // setup and enable routes for setup
  require getConfig()->get('paths')->libraries . '/setupInit.php';
  getRoute()->run('/setup');
}
