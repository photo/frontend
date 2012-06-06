<?php
/**
 * Front controller for OpenPhoto.
 *
 * This file takes all requests and dispatches them to the appropriate controller.
 * @author Jaisen Mathai <jaisen@jmathai.com>
 */

require sprintf('%s/libraries/initialize.php', dirname(dirname(__FILE__)));

if($configObj->get('site')->maintenance == 1)
{
  getRoute()->run('/maintenance', EpiRoute::httpGet);
}
elseif($assetEndpoint || $loginEndpoint || (!$runUpgrade && !$runSetup && $hasConfig))
{
  // if we're not running setup, don't need an upgrade and the config file exists, proceed as normal
  // else no config file then load up the setup dependencies
  $routeObj->run();
}
elseif($runUpgrade)
{
  $routeObj->run('/upgrade', ($_SERVER['REQUEST_METHOD'] == 'GET' ? EpiRoute::httpGet : EpiRoute::httpPost));
}
elseif($runSetup)
{
  // if we're not in the setup path (anything other than /setup) then redirect to the setup
  // otherwise we're on one of the setup steps already, so just run it
  if(!isset($_GET['__route__']) || strpos($_GET['__route__'], '/setup') === false)
    $routeObj->redirect('/setup');
  else
    $routeObj->run();
}
else
{
  $routeObj->run('/error/500', EpiRoute::httpGet);
}
