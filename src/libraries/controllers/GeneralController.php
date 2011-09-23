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
      getRoute()->redirect(Url::photosView(null, false));

    $apisToCall = getConfig()->get('front-apis');
    $params = array();
    foreach($apisToCall as $name => $api)
    {
      $apiParts = explode(' ', $api);
      $apiMethod = strtoupper($apiParts[0]);
      $apiMethod = $apiMethod == 'GET' ? EpiRoute::httpGet : EpiRoute::httpPost;
      $apiUrlParts = parse_url($apiParts[1]);
      $apiParams = array();
      if(isset($apiUrlParts['query']))
        parse_str($apiUrlParts['query'], $apiParams);

      $response = getApi()->invoke($apiUrlParts['path'], $apiMethod, array("_{$apiMethod}" => $apiParams));
      $params[$name] = $response['result'];

    }
    $body = getTheme()->get('front.php', $params);
    getTheme()->display('template.php', array('body' => $body, 'page' => 'front'));
  }

}
