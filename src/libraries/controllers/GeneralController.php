<?php
class GeneralController extends BaseController
{
  public static function home()
  {
    $photos = getApi()->invoke('/photos/sortBy-dateUploaded,desc/pageSize-6.json');
    $body = getTemplate()->get('home.php', array('photos' => $photos['result']));
    getTemplate()->display('template.php', array('body' => $body));
  }
}
