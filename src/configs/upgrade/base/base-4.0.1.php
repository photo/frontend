<?php
$utilityObj = new Utility;

$configFile = sprintf('%s/configs/%s.ini', getConfig()->get('paths')->userdata, $utilityObj->getHost());

$currentConfigAsString = getConfig()->getString($configFile);
// this regex is covered in src/texts/libraries/models/UpgradeTest.php gh-1279
$updatedConfigAsString = preg_replace('/^name ?\= ?"?.+"?$/m', 'name="fabrizio1.0"', $currentConfigAsString);

$status = getConfig()->write($configFile, $updatedConfigAsString);

return $status;
