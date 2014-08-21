<?php
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
          $prefix = sprintf('%s - ', $photo['filenameOriginal']);
    print <<<MKP
<title>{$prefix}{$username}'s photos - Trovebox</title>
MKP;
        break;
      case 'photos':
    print <<<MKP
<title>{$username}'s photos - Trovebox</title>
MKP;
        break;
      case 'tags':
    print <<<MKP
<title>{$username}'s tags - Trovebox</title>
MKP;
        break;
      default:
    print <<<MKP
<title>{$username}'s Photo site - Trovebox</title>
MKP;
        break;
    }
?>
