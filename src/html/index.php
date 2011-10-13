<?php
/**
 * Front controller for OpenPhoto.
 *
 * This file takes all requests and dispatches them to the appropriate controller.
 * @author Jaisen Mathai <jaisen@jmathai.com>
 */

require sprintf('%s/libraries/initialize.php', dirname(dirname(__FILE__)));

// if we're not running setup and the config file exists, proceed as normal
// else no config file then load up the setup dependencies
if(!$runSetup && file_exists($configFile))
{
  getRoute()->run();
}
else
{
  // if we're not in the setup path (anything other than /setup) then redirect to the setup
  // otherwise we're on one of the setup steps already, so just run it
  if(!isset($_GET['__route__']) || strpos($_GET['__route__'], '/setup') === false)
    getRoute()->run('/setup');
  else
    getRoute()->run();
}
