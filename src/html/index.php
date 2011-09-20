<?php
/**
 * Front controller for OpenPhoto.
 *
 * This file takes all requests and dispatches them to the appropriate controller.
 * @author Jaisen Mathai <jaisen@jmathai.com>
 */

require sprintf('%s/libraries/initialize.php', dirname(dirname(__FILE__)));

if(getConfig()->get('paths'))
{
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
  require getConfig()->get('paths')->libraries . '/routes-setup.php';
  require getConfig()->get('paths')->libraries . '/dependencies.php';
  require getConfig()->get('paths')->controllers . '/SetupController.php';

  // if we're not in the setup path (anything other than /setup) then redirect to the setup
  // otherwise we're on one of the setup steps already, so just run it
  if(!isset($_GET['__route__']) || strpos($_GET['__route__'], '/setup') === false)
    getRoute()->run('/setup');
  else
    getRoute()->run();
}
