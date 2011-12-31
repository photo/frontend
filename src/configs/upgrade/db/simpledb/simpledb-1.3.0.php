<?php
$tags = array();
$offset = 0;
$limit = 100;
while(true)
{
  $photos = $this->getPhotos(array(), $limit, $offset);
  if(empty($photos))
    break;
  
  foreach($photos as $photo)
  {
    $publicCountIncrement = 0;
    if(isset($photo['permission']))
      $publicCountIncrement = intval($photo['permission']);

    foreach($photo['tags'] as $tag)
    {
      if(!isset($tags[$tag]))
        $tags[$tag] = array('public' => 0, 'private' => 0);

      $tags[$tag]['private']++;
      if($publicCountIncrement)
        $tags[$tag]['public']++;
    }
  }

  if($offset > $photos[0]['totalRows'])
    break;

  $offset += $limit;
}
foreach($tags as $tag => $counts)
{
  $params = array('countPrivate' => $counts['private'], 'countPublic' => $counts['public']);
  $this->postTag($tag, $params);
}
$user = $this->getUser(1);
$user['version'] = '1.3.0';
$status = $this->postUser($user);
$user = $this->getUser($this->owner);
return $status;
