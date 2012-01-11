<?php
$assetsPath = sprintf('%s/assets', getConfig()->get('paths')->docroot);
$cachePath = sprintf('%s/cache', $assetsPath);

if(!is_dir($cachePath) && is_writable($assetsPath))
  @mkdir($cachePath, 0700);

if(!is_dir($cachePath))
{
  getLogger()->crit(sprintf("Could not create directory: %s", $cachePath));
  getRoute()->run('/error/500', EpiRoute::httpGet);
  die();
}
