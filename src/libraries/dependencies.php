<?php
// for unit tests
if(!isset($pathsObj))
  $pathsObj = getConfig()->get('paths');

// global functions including autoloader
require $pathsObj->libraries . '/functions.php';
// register external autoloader(s)
spl_autoload_register('openphoto_autoloader');
require $pathsObj->external . '/aws/sdk.class.php';
require $pathsObj->external . '/swift-mailer/swift_required.php';
require $pathsObj->external . '/Dropbox/autoload.php';
require $pathsObj->external . '/password_compat/password.php';

// exceptions
require $pathsObj->libraries . '/exceptions.php';
