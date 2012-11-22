<?php
class ApiManageController extends ApiBaseController
{
  public function __construct()
  {
    parent::__construct();
    getAuthentication()->requireAuthentication();
  }

  public function settingsPost()
  {
    getAuthentication()->requireAuthentication();
    getAuthentication()->requireCrumb();
    $configFile = $this->utility->getConfigFile();
    $configString = getConfig()->getString($configFile);
    $configArray = parse_ini_string($configString, true);
    foreach($_POST as $key => $value)
    {
      switch($key)
      {
        case 'allowDuplicate':
          $configArray['site']['allowDuplicate'] = (string)intval($value);
          break;
        case 'downloadOriginal':
          $configArray['site']['allowOriginalDownload'] = (string)intval($value);
          break;
        case 'hideFromSearchEngines':
          $configArray['site']['hideFromSearchEngines'] = (string)intval($value);
          break;
        case 'fileSystem':
          // validate this is an existing file system
          try
          {
            $testFs = getFs($value, false);
            $configArray['systems']['fileSystem'] = $value;
          }
          catch(Exception $e)
          {
            $this->logger->warn(sprintf('Unable to find the specified file system adapter (%s)', $value), $e);
          }
          break;
        case 'credentials':
        case 'box':
        case 'aws':
        case 'dropbox':
          if(empty($value))
            continue;

          // if credentials we need to encrypt
          if($key === 'credentials')
          {
            $tmpCredentials = json_decode($value, true);
            foreach($tmpCredentials as $k => $v)
              $tmpCredentials[$k] = $this->utility->encrypt($v);

            $value = json_encode($tmpCredentials);
          }

          // we do a merge here since it's an array of values and we don't want to clobber values not passed in
          if(isset($configArray[$key]))
            $configArray[$key] = array_merge($configArray[$key], json_decode($value, true));
          else
            $configArray[$key] = json_decode($value, true);
          break;
      }
    }
    $res = getConfig()->write($configFile, $this->utility->generateIniString($configArray, true));
    if($res)
      return $this->success('Features successfully updated', true);
    else
      return $this->error('Could not update features', false);
  }
}
