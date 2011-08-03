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
    getRoute()->redirect('/photos');
    die();
    $photos = getApi()->invoke('/photos/sortBy-dateUploaded,desc/pageSize-6.json', EpiRoute::httpGet, array('_GET' => array('returnSizes' => '200x200')));
    $body = getTemplate()->get('home.php', array('photos' => $photos['result']));
    getTemplate()->display('template.php', array('body' => $body));
  }
}
