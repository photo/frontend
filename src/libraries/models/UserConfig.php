<?php
class UserConfig
{
  private $basePath, $host;

  public function __construct()
  {
    $this->basePath = dirname(dirname(dirname(__FILE__)));
    $this->host = $_SERVER['HTTP_HOST'];
  }

  public function getSiteSettings()
  {
    $configFile = $this->getConfigFile();
    if(!$configFile)
      return false;
    return parse_ini_file($configFile, true);
  }

  public function writeSiteSettings($settings)
  {
    $configFile = $this->getConfigFile();
    if(!$configFile)
      return false;

    $iniString = Utility::generateIniString($settings, true);
    if(!$iniString || empty($iniString))
      return false;

    return file_put_contents($configFile, $iniString);
  }

  public function load()
  {
    getConfig()->load('defaults.ini');
    $configFile = $this->getConfigFile();

    // backwards compatibility for 1.2.1 -> 1.2.2 upgrade
    // TODO remove in 2.0.0

    if($configFile)
    {
      getConfig()->load($configFile);

      // we need to load the deps to get the theme modules
      require getConfig()->get('paths')->libraries . '/dependencies.php';

      getConfig()->load(sprintf('%s/html/assets/themes/%s/config/settings.ini', dirname(dirname(dirname(__FILE__))), getTheme()->getThemeName()));
      if(Utility::isMobile() && file_exists($mobileSettings = sprintf('%s/html/assets/themes/%s/config/settings-mobile.ini', dirname(dirname(dirname(__FILE__))), getTheme(false)->getThemeName())))
        getConfig()->load($mobileSettings);

      return true;
    }
    return false;
  }

  private function getConfigFile()
  {
    $configFile = sprintf('%s/userdata/configs/%s.ini', $this->basePath, $this->host);
    if(!file_exists($configFile))
      $configFile = sprintf('%s/generated/%s.ini', Epi::getPath('config'), $this->host);

    if(!file_exists($configFile))
      return false;
    return $configFile;
  }
}

function getUserConfig()
{
  static $userConfig;
  if($userConfig)
    return $userConfig;

  $userConfig = new UserConfig;
  return $userConfig;
}
