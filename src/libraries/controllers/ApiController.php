<?php
class ApiController extends BaseController
{

  public static function hello()
  {
    return self::success('Hello, world!', $_GET);
  }
}
