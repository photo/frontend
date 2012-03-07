<?php
/**
 * FacebookLike
 *
 * @author Jaisen Mathai <jaisen@jmathai.com>
 */
class FacebookLikePlugin extends PluginBase
{
  public function __construct()
  {
    parent::__construct();
  }

  public function defineConf() { }

  public function renderHead()
  {
    $photo = $this->plugin->getData('photo');
    $tags = '';
    $tags .= $this->addTag('og:title', $photo['title']);
    $tags .= $this->addTag('og:url', $photo['url']);
    $tags .= $this->addTag('og:image', $photo['pathBase']);
    return $tags;
  }

  public function renderPhotoDetail()
  {
    parent::renderPhotoDetail();
    if($this->plugin->getData('page') !== 'photo-detail')
      return;

    return <<<MKP
<fb:like ref="top_left"></fb:like>
MKP;
  }

  private function addTag($name, $value)
  {
    if(!empty($value))
    {
      $name = htmlspecialchars($name);
      $value = htmlspecialchars($value);
      return <<<MKP
<meta property="{$name}" content="{$value}"/>
MKP;
    }
  }
}
