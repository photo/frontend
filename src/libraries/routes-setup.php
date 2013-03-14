<?php
// we need this for the CSS
$routeObj->get('/assets/.*/stylesheets/lessc', array('AssetsController', 'lessc'));

$routeObj->get('/setup', array('SetupController', 'setup'));
$routeObj->post('/setup', array('SetupController', 'setupPost'));
$routeObj->get('/setup/2', array('SetupController', 'setup2'));
$routeObj->post('/setup/2', array('SetupController', 'setup2Post'));
$routeObj->get('/setup/3', array('SetupController', 'setup3'));
$routeObj->post('/setup/3', array('SetupController', 'setup3Post'));
$routeObj->get('/setup/dropbox', array('SetupController', 'setupDropbox'));
$routeObj->post('/setup/dropbox', array('SetupController', 'setupDropboxPost'));
$routeObj->get('/setup/dropbox/callback', array('SetupController', 'setupDropboxCallback'));
$routeObj->get('/setup/restart', array('SetupController', 'setupRestart'));
