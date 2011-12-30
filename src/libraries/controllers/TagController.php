<?php
/**
  * Tag controller for HTML endpoints.
  *
  * @author Jaisen Mathai <jaisen@jmathai.com>
 */
class TagController extends BaseController
{
  /**
    * Call the parent constructor
    *
    * @return void
    */
  public function __construct()
  {
    parent::__construct();
  }

  /**
    * Display tags (via a tag cloud)
    *
    * @return string HTML
    */
  public function list_()
  {
    $tags = $this->api->invoke("/tags/list.json");
    $groupedTags = Tag::groupByWeight($tags['result']);
    $body = $this->theme->get(Utility::getTemplate('tags.php'), array('tags' => $groupedTags));
    $this->theme->display(Utility::getTemplate('template.php'), array('body' => $body, 'page' => 'tags'));
  }
}
