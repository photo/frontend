<?php
class ApiManageController extends ApiBaseController
{
  public function __construct()
  {
    parent::__construct();
    getAuthentication()->requireAuthentication();
  }

  public function featuresPost()
  {
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
      }
    }
    $res = getConfig()->write($configFile, $this->utility->generateIniString($configArray, true));
    if($res)
      return $this->success('Features successfully updated', true);
    else
      return $this->error('Could not update features', false);
  }
}
