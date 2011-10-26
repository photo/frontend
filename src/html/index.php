<?php
/**
 * Front controller for OpenPhoto.
 *
 * This file takes all requests and dispatches them to the appropriate controller.
 * @author Jaisen Mathai <jaisen@jmathai.com>
 */

require sprintf('%s/libraries/initialize.php', dirname(dirname(__FILE__)));

// If custom route is not being used, try some more common routing cases
if (!isset($_GET['__route__']))
{
  if (isset($_SERVER['PATH_INFO']))
  {
    $_GET['__route__'] = $_SERVER['PATH_INFO'];
  }
  else if (isset($_SERVER['REQUEST_URI']))
  {
    if ($request_uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH))
    {
      $_GET['__route__'] = $request_uri;
    }
  } else if (isset($_SERVER['PHP_SELF']))
  {
    $_GET['__route__'] = $_SERVER['PHP_SELF'];
  }
}

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
