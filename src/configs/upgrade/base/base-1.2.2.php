<?php
$userdataPath = sprintf('%s/userdata', dirname(getConfig()->get('paths')->libraries));
$pluginPath = sprintf('%s/plugins', $userdataPath);
$configPath = sprintf('%s/configs', $userdataPath);
$docrootPath = sprintf('%s/html', dirname(getConfig()->get('paths')->libraries));
$pluginSystemPath = sprintf('%s/plugins', getConfig()->get('paths')->libraries);

@mkdir($pluginPath);
@mkdir($configPath);

if(!is_dir($pluginPath) || !is_dir($configPath))
{
  getLogger()->crit(sprintf("Could not create directories: %s or %s", $pluginPath, $configPath));
  getRoute()->run('/error/500', EpiRoute::httpGet);
  die();
}

$currentConfigFilePath = sprintf('%s/generated/%s.ini', getConfig()->get('paths')->configs, getenv('HTTP_HOST'));
$configFile = file_get_contents($currentConfigFilePath);

$photosPath = getConfig()->get('paths')->photos;
$pluginLine = "\n".sprintf('%s="%s"', 'plugins', $pluginSystemPath);
$configFile = preg_replace(sprintf('#photos="%s"#', $photosPath), "\\0$pluginLine", $configFile);

$tempPath = getConfig()->get('paths')->temp;
$userdataLine = "\n".sprintf('%s="%s"', 'userdata', $userdataPath);
$configFile = preg_replace(sprintf('#temp="%s"#', $tempPath), "\\0$userdataLine", $configFile);

$controllersPath = getConfig()->get('paths')->controllers;
$controllersLine = "\n".sprintf('%s="%s"', 'docroot', $docrootPath);
$configFile = preg_replace(sprintf('#controllers="%s"#', $controllersPath), "\\0$controllersLine", $configFile);

$configFile = str_replace('[systems]', "[plugins]\nactivePlugins=\"\"\n\n[systems]", $configFile);

$newConfigFilePath = sprintf('%s/%s.ini', $configPath, getenv('HTTP_HOST'));
copy($currentConfigFilePath, $newConfigFilePath);

// write the new config file and delete the old
$fileWritten = file_put_contents($newConfigFilePath, $configFile);
if($fileWritten !== false)
  unlink($currentConfigFilePath);
