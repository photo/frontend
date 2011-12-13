<?php
/**
 * PluginBase is the parent class for every plugin.
 *
 * @author Jaisen Mathai <jaisen@jmathai.com>
 */
class PluginBase
{
  private $pluginName, $pluginConf = null;
  public function __construct()
  {
    $this->pluginName = preg_replace('/Plugin$/', '', get_class($this));
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
    $conf = getPlugin()->loadConf($this->pluginName);
    foreach($conf as $name => $value)
      $this->pluginConf->$name = $value;

    return $this->pluginConf;
  }

  public function onAction($params)
  {
    getLogger()->info('Plugin onAction called');
  }

  public function onBodyBegin()
  {
    getLogger()->info('Plugin onBodyBegin called');
  }

  public function onBodyEnd()
  {
    getLogger()->info('Plugin onBodyEnd called');
  }

  public function onHead()
  {
    getLogger()->info('Plugin onHead called');
  }

  public function onLoad()
  {
    getLogger()->info('Plugin onLoad called');
  }

  public function onView()
  {
    getLogger()->info('Plugin onView called');  
  }
}
