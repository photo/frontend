<?php
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
