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
    $this->tag = new Tag;
  }

  /**
    * Display tags (via a tag cloud)
    *
    * @return string HTML
    */
  public function list_()
  {
    $tags = $this->api->invoke("/tags/list.json");
    $groupedTags = $this->tag->groupByWeight($tags['result']);
    $this->plugin->setData('tags', $groupedTags);
    $this->plugin->setData('page', 'tags');
    $body = $this->theme->get($this->utility->getTemplate('tags.php'), array('tags' => $groupedTags));
    $this->theme->display($this->utility->getTemplate('template.php'), array('body' => $body, 'page' => 'tags'));
  }
}
