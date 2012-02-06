<?php
/**
 * HelloWorldPlugin is the parent class for every plugin.
 *
 * @author Jaisen Mathai <jaisen@jmathai.com>
 */
class HelloWorldPlugin extends PluginBase
{
  public function __construct()
  {
    parent::__construct();
  }

  public function onBodyBegin($params = null)
  {
    parent::onBodyBegin();
  }

  public function onView($params = null)
  {
    parent::onView();
  }
}
