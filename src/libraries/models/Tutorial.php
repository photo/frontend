<?php
class Tutorial extends BaseModel
{
  private $inited;
  public function __construct()
  {
    parent::__construct();
  }

  public function getUnseen()
  {
    $user = new User;
    $utility = new Utility;

    if(!$user->isAdmin())
    {
      return false;
    }

    if($utility->isActiveTab('photos'))
    {
      $this->init();
      $section = 'tutorialPhotos';
    }
    else
    {
      return false;
    }
    
    $entry = $user->getAttribute($section);
    $isInit = false;
    if(!$entry)
    {
      $entry = '1';
      $isInit = true;
    }

    $config = (array)$this->config->$section;
    $pos = array_search($entry,array_keys($config));
    // for the first tutorial we start at 1 then we get the 
    //  tutorial after the last one they saw (++)
    if($isInit === false)
      $pos++;

    if($pos !== false) {
      $unseen = array_slice($config, $pos);
      $out = array();
      foreach($unseen as $k => $v)
        $out[] = array_merge(json_decode($v, 1), array('key' => $k, 'section' => $section));
      return $out;
    }
  }

  private function init()
  {
    if($this->inited)
      return;

    if(file_exists($mobileSettings = sprintf('%s/%s/config/tutorial.ini', $this->config->paths->themes, getTheme()->getThemeName())))
      getConfig()->loadString(file_get_contents($mobileSettings)); // Can't use $this->config since it's an object of config values

    $this->inited = true;
  }
}
