<?php
/**
 * PluginBase is the parent class for every plugin.
 *
 * @author Jaisen Mathai <jaisen@jmathai.com>
 */
class PluginBase extends BaseModel
{
  private $pluginName, $pluginConf = null;
  public function __construct()
  {
    parent::__construct();
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
    $this->logger->info('Plugin onAction called');
  }

  public function onBodyBegin()
  {
    $this->logger->info('Plugin onBodyBegin called');
  }

  public function onBodyEnd()
  {
    $this->logger->info('Plugin onBodyEnd called');
  }

  public function onHead()
  {
    $this->logger->info('Plugin onHead called');
  }

  public function onLoad()
  {
    $this->logger->info('Plugin onLoad called');
  }

  public function onView()
  {
    $this->logger->info('Plugin onView called');  
  }
}
