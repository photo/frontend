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
  public static function error403()
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
  public static function error404()
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
  public static function error500()
  {
    $body = getTheme()->get('error500.php');
    getTheme()->display('template.php', array('body' => $body, 'page' => 'front'));
  }

  /**
    * Front page which loads front.php if it exists else redirects to /photos
    *
    * @return string HTML
    */
  public static function home()
  {
    if(!getTheme()->fileExists('templates/front.php'))
      getRoute()->redirect('/photos');

    $body = getTheme()->get('front.php');
    getTheme()->display('template.php', array('body' => $body, 'page' => 'front'));
  }
}
