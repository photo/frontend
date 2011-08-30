<?php
/**
  * Tag controller for HTML endpoints.
  * 
  * @author Jaisen Mathai <jaisen@jmathai.com>
 */
class TagController extends BaseController
{
  /**
    * Display tags (via a tag cloud)
    *
    * @return string HTML
    */
  public static function tags()
  {
    $tags = getApi()->invoke("/tags.json");
    $groupedTags = Tag::groupByWeight($tags['result']);

    $body = getTheme()->get('tags.php', array('tags' => $groupedTags));
    getTheme()->display('template.php', array('body' => $body, 'page' => 'tags'));
  }
}
