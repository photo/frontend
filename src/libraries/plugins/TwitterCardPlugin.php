<?php
/**
 * TwitterCard
 *
 * @author Jaisen Mathai <jaisen@jmathai.com>
 */
class TwitterCardPlugin extends PluginBase
{
  public function __construct()
  {
    parent::__construct();
  }

  public function defineConf() { }

  public function renderHead()
  {
    $page = $this->plugin->getData('page');
    if($page !== 'photos' && $page !== 'photo-detail' && $page !== 'albums')
      return;

    $photo = null;
    if($page === 'photos')
    {
      $photo = array_shift($this->plugin->getData('photos'));
    }
    elseif($page === 'photo-detail')
    {
      $photo = $this->plugin->getData('photo');
    }
    elseif($page === 'albums')
    {
      $albums = $this->plugin->getData('albums');
      if(count($albums) > 0 && !empty($albums[0]['cover']))
        $photo = $albums[0]['cover'];
    }

    if(empty($photo))
      return;

    $utility = new Utility;

    $tags = '';
    $title = $photo['title'] !== '' ? $photo['title'] : "{$photo['filenameOriginal']} on OpenPhoto";
    $tags .= $this->addTag('twitter:card', 'photo');
    $tags .= $this->addTag('twitter:site', '@openphoto');
    $tags .= $this->addTag('twitter:url', sprintf('%s://%s%s', $utility->getProtocol(false), $utility->getHost(), $utility->getPath()));
    $tags .= $this->addTag('twitter:title', $title);
    $tags .= $this->addTag('twitter:description', 'OpenPhoto lets you keep all your photos from different services and mobile devices safe in one spot.');
    $tags .= $this->addTag('twitter:image', $photo['pathBase']);
    $tags .= $this->addTag('twitter:image:width', '1280');
    return $tags;
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

