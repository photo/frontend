<?php
/**
  * Tag controller for API endpoints
  *
  * This controller does much of the dispatching to the Tag controller for all tag requests.
  * @author Jaisen Mathai <jaisen@jmathai.com>
  */
class AssetsController extends BaseController
{
  private $types;
  public $returnAsHeader = true;

  /**
    * Call the parent constructor
    *
    * @return void
    */
  public function __construct()
  {
    parent::__construct();
    $this->types = array(
      'css' => 'text/css',
      'gif' => 'image/gif',
      'jpg' => 'image/jpeg',
      'jpeg' => 'image/jpeg',
      'js' => 'text/javascript',
      'ico' => 'image/vnd.microsoft.icon',
      'png' => 'image/png',
      'tiff' => 'image/tiff',
    );
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

  public function staticAsset($file)
  {
    $this->returnHeader($file);
    readfile(sprintf('%s/assets/%s', $this->config->paths->docroot, $file));
    die();
  }

  private function getContentType($file)
  {
    $ext = pathinfo($file, PATHINFO_EXTENSION);
    if(isset($this->types[$ext]))
      return $this->types[$ext];

    return 'text/plain';
  }

  private function returnHeader($file)
  {
    $header = sprintf('Content-Type: %s', $this->getContentType($file));
    if($this->returnAsHeader)
      header($header);
    else
      return $header;
  }
}
