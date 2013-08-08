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
    $utilityObj = new Utility;
    $user = new User;
    $page = $this->plugin->getData('page');
    $username = $utilityObj->safe($user->getNameFromEmail($this->config->user->email), false);
    switch($page)
    {
      case 'photo-detail':
        $photo = $this->plugin->getData('photo');
        $prefix = '';
        if($photo['title'] != '')
          $prefix = sprintf('%s - ', $utilityObj->safe($photo['title'], false));
        elseif($photo['filenameOriginal'] != '')
          $prefix = sprintf('%s - ', $utilityObj->safe($photo['filenameOriginal'], false));
    return <<<MKP
<title>{$prefix}{$username}'s photos - Trovebox</title>
MKP;
        break;
      case 'photos':
    return <<<MKP
<title>{$username}'s photos - Trovebox</title>
MKP;
        break;
      case 'tags':
    return <<<MKP
<title>{$username}'s tags - Trovebox</title>
MKP;
        break;
      default:
    return <<<MKP
<title>{$username}'s Photo site - Trovebox</title>
MKP;
        break;
    }
  }
}
