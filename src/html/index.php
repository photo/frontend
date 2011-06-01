<?php
/*
 * Author: Jaisen Mathai <jaisen@jmathai.com>
 * Front controller for OpenPhoto.
 * This file takes all requests and dispatches them to the appropriate controller.
 */

$basePath = dirname(dirname(__FILE__));
$epiPath = "{$basePath}/libraries/epi";
require "{$epiPath}/Epi.php";
Epi::setPath('base', $epiPath);
Epi::setPath('config', "{$basePath}/configs");
//Epi::setPath('view', "{$basePath}/views");
//Epi::setSetting('exceptions', true);
Epi::init('config','route');

getConfig()->load('defaults.ini');
getConfig()->load('override/defaults.ini');

// load all dependencies
require getConfig()->get('paths')->libraries . '/dependencies.php';

getRoute()->run();
