<?php
class GeneralController extends BaseController
{
  public static function home()
  {
    getRoute()->redirect('/photos');
  }
}
