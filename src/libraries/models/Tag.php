<?php
/**
 * Tag model.
 *
 * This handles adding, removing and modifying tags.
 * @author Jaisen Mathai <jaisen@jmathai.com>
 */
class Tag
{
  /**
    * Delete a tag.
    *
    * @param array $tags An array of Tag objects optionally passed in else queried from the database.
    * @return array Tag object augmented with a "weight" property.
    */
  public static function delete($id)
  {
    return getDb()->deleteTag($id);
  }

  /**
    * Updates count values in tags when an object is updates.
    * Keeps track of # of objects tagged in the Tag object itself.
    * Both params are a full set of tags before and after the update.
    *
    * @param array $existingTags The tags previously on the object.
    * @param array $updatedTags The tags currently being updated on the object.
    * @param int $permission Permission of the photo.
    * @return boolean
    */
  public static function updateTagCounts($existingTags, $updatedTags, $permission)
  {
    // we increment public photos by 1 only if they are public
    $publicIncrement = $permission == 1 ? 1 : 0;

    $tagsToDecrement = array_diff($existingTags, $updatedTags);
    $tagsToIncrement = array_diff($updatedTags, $existingTags);
    $tagsToUpdate = array();
    foreach($tagsToDecrement as $tg)
      $tagsToUpdate[self::sanitize($tg)] = -1;
    foreach($tagsToIncrement as $tg)
      $tagsToUpdate[self::sanitize($tg)] = 1;

    $tagsFromDb = array();
    $allTags = getDb()->getTags(array('permission' => 0));
    if(!empty($allTags))
    {
      foreach($allTags as $k => $t)
      {
        if(isset($tagsToUpdate[$t['id']]))
          $tagsFromDb[] = $t;
      }
    }

    // track the tags which need to be updated
    // start with ones which already exist in the database and increment them accordingly
    $updatedTags = array();
    foreach($tagsFromDb as $tagFromDb)
    {
      $thisTag = $tagFromDb['id'];
      $changeBy = $tagsToUpdate[$thisTag];
      $updatedTags[] = array('id' => $thisTag, 'countPrivate' => $tagFromDb['countPrivate']+$changeBy, 'countPublic' => $tagFromDb['countPublic']+($changeBy*$publicIncrement));
      // unset so we can later loop over tags which didn't already exist
      unset($tagsToUpdate[$thisTag]);
    }
    // these are new tags
    foreach($tagsToUpdate as $tag => $count)
    {
      if($count == 0)
        self::delete($tag);
      $updatedTags[] = array('id' => $tag, 'countPrivate' => $count, 'countPublic' => ($count*$publicIncrement));
    }

    return getDb()->postTags($updatedTags);
  }

  /**
    * Groups tags by weight for tag cloud generation
    * Weights are 1-10.
    *
    * @param array $tags An array of Tag objects optionally passed in else queried from the database.
    * @return array Tag object augmented with a "weight" property.
    */
  public static function groupByWeight($tags = null)
  {
    $tagField = User::isOwner() ? 'countPrivate' : 'countPublic';
    if($tags === null)
    {
      $tags = getApi()->invoke("/tags/list.json");
      $tags = $tags['result'];
    }

    $maxTags = 0;
    $minTags = PHP_INT_MAX;
    if(count($tags) > 0)
    {
      foreach($tags as $tag)
      {
        if($tag[$tagField] < $minTags)
          $minTags = $tag[$tagField];
        if($tag[$tagField] > $maxTags)
          $maxTags = $tag[$tagField];
      }

      // we create 10 groups based on count using %s
      $range = max(($maxTags - $minTags), 1);
      // step needs to be float so we don't divide by zero
      $step = floatval($range / 9);
      foreach($tags as $key => $tag)
        $tags[$key]['weight'] = intval(($tag[$tagField]-$minTags) / $step)+1;
    }
    return $tags;
  }

  public static function sanitize($tag)
  {
    return preg_replace('/,/', '', $tag);
  }

  public static function sanitizeTagsAsString($tags)
  {
    $tagsArray = (array)explode(',', $tags);
    natcasesort($tagsArray);
    foreach($tagsArray as $key => $val)
      $tagsArray[$key] = self::sanitize($val);

    return implode(',', $tagsArray);
  }

  public static function validateParams($params)
  {
    $fields = array('countPrivate' => 1, 'countPublic' => 1);
    $noSql = array('email' => 1, 'latitude' => 1, 'longitude' => 1);
    $json = null;
    foreach($params as $key => $param)
    {
      if(isset($noSql[$key]))
        $json[$key] = $param;

      if(!isset($fields[$key]))
        unset($params[$key]);
    }

    if($json)
      $params['params'] = json_encode($json);
    return $params;
  }
}
