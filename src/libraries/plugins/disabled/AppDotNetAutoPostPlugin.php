<?php
/**
 * AppDotNetAutoPostPlugin is the parent class for every plugin.
 *
 * @author Jaisen Mathai <jaisen@jmathai.com>
 */
class AppDotNetAutoPostPlugin extends PluginBase
{
  private $adn;
  public function __construct()
  {
    parent::__construct();
    $this->adn = new AppDotNet(null, null);
  }

  public function defineConf()
  {
    return array('accessToken' => null);
  }

  public function onPhotoUploaded()
  {
    parent::onPhotoUploaded();
    $photo = $this->plugin->getData('photo');
    $this->createPost($photo);
  }

  private function createPost($photo)
  {
    $conf = $this->getConf();
    $this->adn->setAccessToken($conf->accessToken);
    $data = array(
      'annotations' => array(
        array(
          'type' => 'net.app.core.oembed',
          'value' => array(
            'type' => 'photo',
            'version' => '1.0',
            'url' => $photo['pathBase'],
            'width' => $photo['width'],
            'height' => $photo['height'],
            'provider_url' => 'https://trovebox.com',
            'thumbnail_url' => $photo['path100x100xCR'],
            'thumbnail_width' => 100,
            'thumbnail_height' => 100
          )
        )
      )
    );
    try
    {
      $this->adn->createPost(sprintf('I just posted a new photo. %s', $photo['url']), $data);
    }
    catch(AppDotNetException $e)
    {
      getLogger()->warn('Could not create ADN post update.', $e);
    }
  }
}
