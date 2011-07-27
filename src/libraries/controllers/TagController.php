<?php
/**
  * Tag controller for HTML endpoints.
  * 
  * @author Jaisen Mathai <jaisen@jmathai.com>
 */
class TagController extends BaseController
{
  public static function tags($filterOpts = null)
  {
    if($filterOpts)
      $tags = getApi()->invoke("/tags/{$filterOpts}.json");
    else
      $tags = getApi()->invoke("/tags.json");

    $body = getTemplate()->get('tags.php', array('tags' => $tags['result']));
    getTemplate()->display('template.php', array('body' => $body));
  }
}
