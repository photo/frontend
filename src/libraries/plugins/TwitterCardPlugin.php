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
    $userObj = new User;
    $utilityObj = new Utility;
    $page = $this->plugin->getData('page');
    if($page !== 'photos' && $page !== 'photo-detail' && $page !== 'albums')
      return;

    $metaTags = '';
    $username = $utilityObj->safe($userObj->getNameFromEmail($this->config->user->email), false);

    if($page === 'photos')
    {
      $photos = array_slice($this->plugin->getData('photos'), 0, 4);
      $filters = $this->plugin->getData('filters');

      $metaTags .= $this->addTag('twitter:card', 'gallery');

      $title = sprintf('%s\'s photos on @Trovebox', $username);
      if(array_search('album', $filters) !== false)
      {
        $album = $this->plugin->getData('album');
        $title = sprintf('%s from %s on @Trovebox', $utilityObj->safe($album['name'], false), $username);
      }
      elseif(array_search('tags', $filters) !== false)
      {
        $tags = implode(',', $this->plugin->getData('tags'));
        $title = sprintf('Photos tagged with %s from %s on @Trovebox', $utilityObj->safe($tags, false), $username);
      }

      $cnt = 0;
      foreach($photos as $photo)
        $metaTags .= $this->addTag(sprintf('twitter:image%d', $cnt++), $photo['pathBase']);
    }
    elseif($page === 'photo-detail')
    {
      $photo = $this->plugin->getData('photo');
      $photoTitle = $photo['title'] !== '' ? $utilityObj->safe($photo['title'], false) : $photo['filenameOriginal'];
      $title = sprintf('%s from %s on @Trovebox', $photoTitle, $username);
      $metaTags .= $this->addTag('twitter:card', 'photo');
      $metaTags .= $this->addTag('twitter:image', $photo['pathBase']);
    }
    elseif($page === 'albums')
    {
      $albums = $this->plugin->getData('albums');
      if(count($albums) > 0 && !empty($albums[0]['cover']))
        $photo = $albums[0]['cover'];

      $title = sprintf('%s\'s albums on @Trovebox', $username);
      $metaTags .= $this->addTag('twitter:card', 'photo');
      $metaTags .= $this->addTag('twitter:image', $photo['pathBase']);
    }

    if(empty($photo))
      return;

    $metaTags .= $this->addTag('twitter:site', '@Trovebox');
    $metaTags .= $this->addTag('twitter:url', sprintf('%s://%s%s', $utilityObj->getProtocol(false), $utilityObj->getHost(), $utilityObj->getPath()));
    $metaTags .= $this->addTag('twitter:title', $title);
    $metaTags .= $this->addTag('twitter:description', 'Trovebox lets you keep all your photos from different services and mobile devices safe in one spot.');
    $metaTags .= $this->addTag('twitter:image:width', '1280');
    return $metaTags;
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

