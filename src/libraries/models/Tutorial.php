<?php
class Tutorial extends BaseModel
{
  private $inited, $all = 'tutorialAll';
  public function __construct()
  {
    parent::__construct();
  }

  public function getUnseen()
  {
    $this->init();
    $all = $this->all;

    $user = new User;
    $utility = new Utility;

    if(!$user->isAdmin())
    {
      return false;
    }

    // first check "All"
    $all = $this->getAllUnseen();
    if(!empty($all))
      return $all;

    if($utility->isActiveTab('albums'))
      $section = 'tutorialAlbums';
    elseif($utility->isActiveTab('photos'))
      $section = 'tutorialPhotos';
    elseif($utility->isActiveTab('upload'))
      $section = 'tutorialUpload';
    else
      return false;

    // initialize the output array
    $out = array();

    // get any entries from the current section
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

    if($pos !== false)
    {
      $unseen = array_slice($config, $pos, 4);
      foreach($unseen as $k => $v)
        $out[] = array_merge(json_decode($v, 1), array('key' => $k, 'section' => $section));
    }

    return $out;
  }

  // not *all* but All
  private function getAllUnseen()
  {
    $all = $section = $this->all;
    $user = new User;
    // get any entries from the "all" section
    $entryAll = $user->getAttribute($all);
    $isInitAll = false;
    if(!$entryAll)
    {
      $entryAll = '1';
      $isInitAll = true;
    }
    $configAll = (array)$this->config->$all;
    $posAll = array_search($entryAll,array_keys($configAll));
    // for the first tutorial we start at 1 then we get the 
    //  tutorial after the last one they saw (++)
    if($isInitAll === false)
      $posAll++;

    $out = array();
    if($posAll !== false)
    {
      $unseenAll = array_slice($configAll, $posAll, 4);
      foreach($unseenAll as $k => $v)
        $out[] = array_merge(json_decode($v, 1), array('key' => $k, 'section' => $section));
    }
    
    return $out;
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
