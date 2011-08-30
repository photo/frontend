<?php
/**
  * General controller for HTML endpoints
  *
  * @author Jaisen Mathai <jaisen@jmathai.com>
  */
class GeneralController extends BaseController
{
  /**
   * Not documenting this since it's temporary.
    * Wait, I just did. :\
    */
  public static function home()
  {
    if(!getTheme()->fileExists('templates/front.php'))
      getRoute()->redirect('/photos');

    $body = getTheme()->get('front.php');
    getTheme()->display('template.php', array('body' => $body, 'page' => 'front'));
  }
}
