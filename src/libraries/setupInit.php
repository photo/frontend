<?php
$baseDir = dirname(dirname(__FILE__));
$paths = new stdClass;
$paths->libraries = "{$baseDir}/libraries";
$paths->controllers = "{$baseDir}/libraries/controllers";
$paths->external = "{$baseDir}/libraries/external";
$paths->adapters = "{$baseDir}/libraries/adapters";
$paths->models = "{$baseDir}/libraries/models";
getConfig()->set('paths', $paths);
require getConfig()->get('paths')->libraries . '/dependencies.php';
require getConfig()->get('paths')->libraries . '/routes-setup.php';
require getConfig()->get('paths')->controllers . '/SetupController.php';
