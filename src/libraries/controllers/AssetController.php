<?php
class AssetController extends BaseController
{
  public function __construct()
  {
    parent::__construct();
  }

  public function lessc()
  {
    if(!isset($_GET['f']))
    {
      $this->route->run('/error/404');
      return;
    }

    $f = $_GET['f'];
    if(!is_array($f))
      $f = (array)explode(',', $f);

    header('Content-type: text/css');
    $theme = getTheme();
    $less = new lessc;
    foreach($f as $file)
    {
      $fullPath = realpath(sprintf('%s/%s/stylesheets/%s', $this->config->paths->themes, $theme->getThemeName(), $file));
      if(file_exists($fullPath) && preg_match('/\.less$/', $file) === 1 && strpos($fullPath, $this->config->paths->themes) === 0)
        echo $less->compileFile($fullPath);
    }
  }
}
