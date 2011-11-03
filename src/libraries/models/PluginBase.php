<?php
/**
 * PluginBase is the parent class for every plugin.
 *
 * @author Jaisen Mathai <jaisen@jmathai.com>
 */
class PluginBase
{
  public function __construct()
  {

  }

  public function onAction($params)
  {
    getLogger()->info('Plugin onAction called');
  }

  public function onBody()
  {
    getLogger()->info('Plugin onBody called');
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
