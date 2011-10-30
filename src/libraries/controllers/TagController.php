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
  public static function list_()
  {
    $tags = getApi()->invoke("/tags/list.json");
    $groupedTags = Tag::groupByWeight($tags['result']);

    $body = getTheme()->get(Utility::getTemplate('tags.php'), array('tags' => $groupedTags));
    getTheme()->display(Utility::getTemplate('template.php'), array('body' => $body, 'page' => 'tags'));
  }
}
