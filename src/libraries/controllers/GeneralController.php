<?php
/**
  * General controller for HTML endpoints
  *
  * @author Jaisen Mathai <jaisen@jmathai.com>
  */
class GeneralController extends BaseController
{
  /**
    * 403 Forbidden page
    *
    * @return string HTML
    */
  public function error403()
  {
    header('HTTP/1.0 403 Forbidden');
    $body = $this->theme->get('error403.php');
    $this->theme->display('template.php', array('body' => $body, 'page' => 'error'));
  }

  /**
    * 404 Not found page
    *
    * @return string HTML
    */
  public function error404()
  {
    header('HTTP/1.0 404 Not Found');
    $body = $this->theme->get('error404.php');
    $this->theme->display('template.php', array('body' => $body, 'page' => 'error'));
  }

  /**
    * 500 Error page
    *
    * @return string HTML
    */
  public function error500()
  {
    header('HTTP/1.0 500 Internal Server Error');
    $body = $this->theme->get('error500.php');
    $this->theme->display('template.php', array('body' => $body, 'page' => 'error'));
  }

  /**
    * Front page which loads front.php if it exists else redirects to /photos
    *
    * @return string HTML
    */
  public function home()
  {
    $template = $this->utility->getTemplate('front.php');
    if(!$this->theme->fileExists($template))
      $this->route->redirect($this->url->photosView(null, false));

    $apisToCall = $this->config->frontApis;
    $params = $this->utility->callApis($apisToCall);
    $body = $this->theme->get($template, $params);
    $this->plugin->setData('page', 'front');
    $this->theme->display($this->utility->getTemplate('template.php'), array('body' => $body, 'page' => 'front'));
  }

  /**
    * Maintenance page
    *
    * @return string HTML
    */
  public function maintenance()
  {
    $this->theme->setTheme(); // defaults
    $this->theme->display($this->utility->getTemplate('maintenance.php'));
  }

  /**
   * Robots.txt
   * @return string Robots.txt file
   */
  public function robots()
  {
    $params = array(
      'hideFromSearchEngines' => $this->config->site->hideFromSearchEngines,
      'hideFromTwitterBot' => false
    );
    $template = sprintf('%s/robots.txt', $this->config->paths->templates);
    $this->template->display($template, $params);
  }
}
