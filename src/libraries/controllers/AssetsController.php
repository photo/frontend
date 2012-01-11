<?php
/**
  * Tag controller for API endpoints
  *
  * This controller does much of the dispatching to the Tag controller for all tag requests.
  * @author Jaisen Mathai <jaisen@jmathai.com>
  */
class AssetsController extends BaseController
{
  /**
    * Call the parent constructor
    *
    * @return void
    */
  public function __construct()
  {
    parent::__construct();
  }

  public function get($type, $compression, $files)
  {
    $files = (array)explode(',', $files);
    $pipeline = getAssetPipeline();
    foreach($files as $file)
    {
      if($type === 'css')
        $pipeline->addCss($file);
      elseif($type === 'js')
        $pipeline->addJs($file);
    }

    if($type === 'css')
      header('Content-type: text/css');
    elseif($type === 'js')
      header('Content-type: text/javascript');

    if($compression === 'm')
      echo $pipeline->getMinified($type);
    elseif($compression === 'c')
      echo $pipeline->getCombined($type);
  }
}
