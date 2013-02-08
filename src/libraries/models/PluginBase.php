<?php
/**
 * PluginBase is the parent class for every plugin.
 *
 * @author Jaisen Mathai <jaisen@jmathai.com>
 */
class PluginBase extends BaseModel
{
  protected $plugin;
  private $pluginName, $pluginConf = null;
  public function __construct($params = null)
  {
    parent::__construct();
    $this->pluginName = preg_replace('/Plugin$/', '', get_class($this));
    if(isset($params['plugin']))
      $this->plugin = $params['plugin'];
    else
      $this->plugin = getPlugin();
  }


  public function defineConf()
  {
    return null;
  }

  // this logic is duplicated in Plugin::registerRoutes
  public function getRouteUrl($name)
  {
    $routes = $this->defineRoutes();
    if(isset($routes[$name]))
      return sprintf('/plugin/%s%s', preg_replace('/Plugin$/', '', get_class($this)), $routes[$name][1]);

    return null;
  }

  public function defineRoutes() { }
  public function defineApis() { }

  public function getConf()
  {
    if($this->pluginConf !== null)
      return $this->pluginConf;

    $this->pluginConf = new stdClass;
    $conf = $this->plugin->loadConf($this->pluginName);
    foreach($conf as $name => $value)
      $this->pluginConf->$name = $value;

    return $this->pluginConf;
  }

  public function onAction() { }

  public function onView() { }

  public function onPhotoUpload() {}

  public function onPhotoUploaded() {}

  public function renderHead() { }

  public function renderBody() { }

  public function renderPhotoDetail() { }

  public function renderPhotoUploaded() {}
  
  public function renderFooter() { }

  public function renderFooterJavascript() { }

  public function routeHandler($route)
  {
    // require authentication for all route urls
    getAuthentication()->requireAuthentication();
  }
}
