<?php
/**
  * Tag controller for API endpoints
  *
  * This controller does much of the dispatching to the Tag controller for all tag requests.
  * @author Jaisen Mathai <jaisen@jmathai.com>
  */
class AssetsController extends BaseController
{
  private $types, $pipeline;

  /**
    * Call the parent constructor
    *
    * @return void
    */
  public function __construct()
  {
    parent::__construct();
    $this->pipeline = getAssetPipeline();
  }

  public function get($type, $compression, $files)
  {
    $files = (array)explode(',', $files);
    foreach($files as $file)
    {
      if($type === 'css')
        $this->pipeline->addCss($file);
      elseif($type === 'js')
        $this->pipeline->addJs($file);
    }

    $this->pipeline->returnHeader($files[0]);
    if($compression === 'm')
      echo $this->pipeline->getMinified($type);
    elseif($compression === 'c')
      echo $this->pipeline->getCombined($type);
  }

  public function staticAsset($file)
  {
    $this->pipeline->returnHeader($file);
    readfile(sprintf('%s/assets/%s', $this->config->paths->docroot, $file));
    die();
  }


}
