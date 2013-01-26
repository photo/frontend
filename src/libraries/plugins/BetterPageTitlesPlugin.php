<?php
/**
 * BetterPageTitles
 *
 * @author Jaisen Mathai <jaisen@jmathai.com>
 */
class BetterPageTitlesPlugin extends PluginBase
{
  public function __construct()
  {
    parent::__construct();
  }

  public function renderHead()
  {
    parent::renderHead();
    $user = new User;
    $page = $this->plugin->getData('page');
    $username = $user->getNameFromEmail($this->config->user->email);
    switch($page)
    {
      case 'photo-detail':
        $photo = $this->plugin->getData('photo');
        $prefix = '';
        if($photo['title'] != '')
          $prefix = sprintf('%s - ', $photo['title']);
        elseif($photo['filenameOriginal'] != '')
          $prefix = sprintf('%s - ', $photo['filenameOriginal']);
    return <<<MKP
<title>{$prefix}{$username}'s photos - The Photo Project</title>
MKP;
        break;
      case 'photos':
    return <<<MKP
<title>{$username}'s photos - The Photo Project</title>
MKP;
        break;
      case 'tags':
    return <<<MKP
<title>{$username}'s tags - The Photo Project</title>
MKP;
        break;
      default:
    return <<<MKP
<title>{$username}'s Photo site - The Photo Project</title>
MKP;
        break;
    }
  }
}
