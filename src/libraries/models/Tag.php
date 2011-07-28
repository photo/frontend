<?php
/**
 * Tag model.
 *
 * This handles adding, removing and modifying tags.
 * @author Jaisen Mathai <jaisen@jmathai.com>
 */
class Tag
{
  public static function updateTagCounts($existingTags, $updatedTags)
  {
    $tagsToDecrement = array_diff($existingTags, $updatedTags);
    $tagsToIncrement = array_diff($updatedTags, $existingTags);
    $tagsToUpdate = array();
    foreach($tagsToDecrement as $tg)
      $tagsToUpdate[$tg] = -1;
    foreach($tagsToIncrement as $tg)
      $tagsToUpdate[$tg] = 1;
    getDb()->postTagsCounter($tagsToUpdate);
  }
}

