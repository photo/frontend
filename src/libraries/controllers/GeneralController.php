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
    $body = getTheme()->get('error403.php');
    getTheme()->display('template.php', array('body' => $body, 'page' => 'error'));
  }

  /**
    * 404 Not found page
    *
    * @return string HTML
    */
  public function error404()
  {
    header('HTTP/1.0 404 Not Found');
    $body = getTheme()->get('error404.php');
    getTheme()->display('template.php', array('body' => $body, 'page' => 'error'));
  }

  /**
    * 500 Error page
    *
    * @return string HTML
    */
  public function error500()
  {
    header('HTTP/1.0 500 Internal Server Error');
    $body = getTheme()->get('error500.php');
    getTheme()->display('template.php', array('body' => $body, 'page' => 'error'));
  }

  /**
    * Front page which loads front.php if it exists else redirects to /photos
    *
    * @return string HTML
    */
  public function home()
  {
    $template = Utility::getTemplate('front.php');
    if(!getTheme()->fileExists($template))
      getRoute()->redirect(Url::photosView(null, false));

    $apisToCall = getConfig()->get('frontApis');
    $params = Utility::callApis($apisToCall);
    $body = getTheme()->get($template, $params);
    getTheme()->display(Utility::getTemplate('template.php'), array('body' => $body, 'page' => 'front'));
  }
}
