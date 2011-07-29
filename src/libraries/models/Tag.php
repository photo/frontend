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
    * Updates count values in tags when an object is updates.
    * Keeps track of # of objects tagged in the Tag object itself.
    * Both params are a full set of tags before and after the update.
    *
    * @param array $existingTags The tags previously on the object.
    * @param array $updatedTags The tags currently being updated on the object.
    * @return boolean
    */
  public static function updateTagCounts($existingTags, $updatedTags)
  {
    $tagsToDecrement = array_diff($existingTags, $updatedTags);
    $tagsToIncrement = array_diff($updatedTags, $existingTags);
    $tagsToUpdate = array();
    foreach($tagsToDecrement as $tg)
      $tagsToUpdate[$tg] = -1;
    foreach($tagsToIncrement as $tg)
      $tagsToUpdate[$tg] = 1;
    return getDb()->postTagsCounter($tagsToUpdate);
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
    if($tags === null)
    {
      $tags = getApi()->invoke("/tags.json");
      $tags = $tags['result'];
    }

    $maxTags = 0;
    $minTags = PHP_INT_MAX;
    if(count($tags) > 0)
    {
      foreach($tags as $tag)
      {
        if($tag['count'] < $minTags)
          $minTags = $tag['count'];
        if($tag['count'] > $maxTags)
          $maxTags = $tag['count'];
      }

      // we create 10 groups based on count using %s
      $range = $maxTags - $minTags;
      // step needs to be float so we don't divide by zero
      $step = floatval($range / 9);
      foreach($tags as $key => $tag)
        $tags[$key]['weight'] = intval(($tag['count']-$minTags) / $step)+1;
    }
    return $tags;
  }
}

