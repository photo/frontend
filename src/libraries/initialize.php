<?php
// TODO, remove these
date_default_timezone_set('America/Los_Angeles');

if(isset($_GET['__route__']) && strstr($_GET['__route__'], '.json'))
  header('Content-type: application/json');

$basePath = dirname(dirname(__FILE__));
$epiPath = "{$basePath}/libraries/external/epi";
require "{$epiPath}/Epi.php";

Epi::setPath('base', $epiPath);
Epi::setPath('config', "{$basePath}/configs");
Epi::setPath('view', '');
Epi::init('api','cache','config','form','logger','route','session-php','template','database');
EpiSession::employ(EpiSession::PHP);
getSession();

getConfig()->load('defaults.ini');
getConfig()->load(sprintf('%s/html/assets/themes/%s/config/settings.ini', dirname(dirname(__FILE__)), getConfig()->get('site')->theme));
$configFile = sprintf('%s/generated/%s.ini', Epi::getPath('config'), getenv('HTTP_HOST'));
if(file_exists($configFile))
{
  getConfig()->load(sprintf('generated/%s.ini', getenv('HTTP_HOST')));
  require getConfig()->get('paths')->libraries . '/dependencies.php';
}
