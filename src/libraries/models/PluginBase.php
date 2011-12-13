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

  public function defineConf()
  {
    return null;
  }

  public function onAction($params)
  {
    getLogger()->info('Plugin onAction called');
  }

  public function onBodyBegin()
  {
    getLogger()->info('Plugin onBodyBegin called');
  }

  public function onBodyBeginEnd()
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
