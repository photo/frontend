<?php
/**
  * Base controller extended by all other controllers.
  *
  * @author Jaisen Mathai <jaisen@jmathai.com>
 */
class BaseController extends ApiBaseController
{

  protected $api, $config, $theme;

  public function __construct()
  {
    $this->api = getApi();
    $this->config = getConfig()->get();
    $this->plugin = getPlugin();
    $this->route = getRoute();
    $this->session = getSession();
    $this->template = getTemplate();
    $this->theme = getTheme();
    $this->utility = new Utility;
    $this->url = new Url;

    $this->template->config = $this->config;
    $this->template->plugin = $this->plugin;
    $this->template->session = $this->session;
    $this->template->theme = $this->theme;
    $this->template->utility = $this->utility;
    $this->template->url = $this->url;
    $this->template->user = new User;

    $class = get_class($this);
    if($class === 'UserController' || $class === 'SetupController')
      $this->theme->setTheme('beisel');
  }
}
