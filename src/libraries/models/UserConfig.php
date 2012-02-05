<?php
class UserConfig
{
  protected $utility, $basePath, $host;

  public function __construct($params = null)
  {
    if(isset($params['config']))
    {
      $this->config = $params['config'];
    }
    else
    {
      $path = dirname(dirname(dirname(__FILE__)));
      $params = parse_ini_file(sprintf('%s/configs/defaults.ini', $path), true);
      if(file_exists($overrideIni = sprintf('%s/configs/override.ini', $path)))
      {
        $override = parse_ini_file($overrideIni, true);
        foreach($override as $key => $value)
        {
          if(array_key_exists($key, $params))
          {
            if(is_array($value))
              $params[$key] = array_merge((array)$params[$key], $value);
            else
              $params[$key] = $value;
          }
          else
          {
            $params[$key] = $value;
          }
        }
      }

      $configParams = array($params['epi']['config']);
      if(isset($params['epiConfigParams']))
        $configParams = array_merge($configParams, $params['epiConfigParams']);
      EpiConfig::employ($configParams);
      $this->config = getConfig();
    }

    if(isset($params['utility']))
      $this->utility = $params['utility'];

    $this->basePath = dirname(dirname(dirname(__FILE__)));
    $this->host = $_SERVER['HTTP_HOST']; // TODO this isn't the best idea
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

    $utilityObj = $this->getUtility();
    $iniString = $utilityObj->generateIniString($settings, true);
    if(!$iniString || empty($iniString))
      return false;

    return file_put_contents($configFile, $iniString);
  }

  public function load()
  {
    $path = dirname(dirname(dirname(__FILE__)));
    $this->config->loadString(file_get_contents(sprintf('%s/configs/defaults.ini', $path)));
    $configFile = $this->getConfigFile();

    // backwards compatibility for 1.2.1 -> 1.2.2 upgrade
    // TODO remove in 2.0.0

    if($configFile)
    {
      $this->config->load($configFile);
      if(file_exists(sprintf('%s/override.ini', $this->config->get('paths')->configs)))
        $this->config->load('override.ini');

      // we need to load the deps to get the theme modules
      require $this->config->get('paths')->libraries . '/dependencies.php';

      $this->config->loadString(file_get_contents(sprintf('%s/html/assets/themes/%s/config/settings.ini', dirname(dirname(dirname(__FILE__))), getTheme()->getThemeName())));
      $utilityObj = $this->getUtility();
      if($utilityObj->isMobile() && file_exists($mobileSettings = sprintf('%s/html/assets/themes/%s/config/settings-mobile.ini', dirname(dirname(dirname(__FILE__))), getTheme(false)->getThemeName())))
        $this->config->load($mobileSettings);

      return true;
    }
    return false;
  }

  protected function getUtility()
  {
    if(isset($this->utility))
      return $this->utility;

    $this->utility = new Utility;
    return $this->utility;
  }

  private function getConfigFile()
  {
    $configFile = sprintf('%s/userdata/configs/%s.ini', $this->basePath, $this->host);
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
