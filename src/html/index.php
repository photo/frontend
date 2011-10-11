<?php
/**
 * Front controller for OpenPhoto.
 *
 * This file takes all requests and dispatches them to the appropriate controller.
 * @author Jaisen Mathai <jaisen@jmathai.com>
 */

require sprintf('%s/libraries/initialize.php', dirname(dirname(__FILE__)));

if(!$runSetup && getConfig()->get('paths'))
{
  getRoute()->run();
}
elseif(!file_exists($configFile) || $runSetup) // if no config file then load up the setup dependencies
{
  // if we're not in the setup path (anything other than /setup) then redirect to the setup
  // otherwise we're on one of the setup steps already, so just run it
  if(!isset($_GET['__route__']) || strpos($_GET['__route__'], '/setup') === false)
    getRoute()->run('/setup');
  else
    getRoute()->run();
}
