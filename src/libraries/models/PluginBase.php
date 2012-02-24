<?php
/**
 * PluginBase is the parent class for every plugin.
 *
 * @author Jaisen Mathai <jaisen@jmathai.com>
 */
class PluginBase extends BaseModel
{
  private $plugin, $pluginName, $pluginConf = null;
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

  public function onAction($params)
  {
  }

  public function onBodyBegin($params = null)
  {
  }

  public function onBodyEnd($params = null)
  {
  }

  public function onHead($params = null)
  {
  }

  public function onLoad($params = null)
  {
  }

  public function onView($params)
  {
  }
}
