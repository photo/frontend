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
    $utility = new Utility;
    $photo = $this->plugin->getData('photo');
    $tags = '';
    $title = $photo['title'] !== '' ? $photo['title'] : "{$photo['filenameOriginal']} on OpenPhoto";
    $tags .= $this->addTag('og:site_name', 
      sprintf('%s OpenPhoto site', 
        ucwords(
          $utility->posessive(
            $utility->getEmailHandle($this->config->user->email, false),
            false)
        )
      )
    );;
    $tags .= $this->addTag('og:title', $photo['title']);
    $tags .= $this->addTag('og:url', $photo['url']);
    $tags .= $this->addTag('og:image', $photo['pathBase']);
    return $tags;
  }

  public function renderPhotoDetail()
  {
    parent::renderPhotoDetail();
    $photo = $this->plugin->getData('photo');
    if($this->plugin->getData('page') !== 'photo-detail')
      return;
    if(!isset($photo['permission']) || $photo['permission'] == 0)
      return;

    return <<<MKP
<div class="fb-like"></div>
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
