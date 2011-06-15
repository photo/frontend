<?php
class GeneralController extends BaseController
{
  public static function home()
  {
    $body = getTemplate()->get('home.php');
    getTemplate()->display('template.php', array('body' => $body));
  }
}
