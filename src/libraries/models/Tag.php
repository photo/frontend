<?php
/**
 * Tag model.
 *
 * This handles adding, removing and modifying tags.
 * @author Jaisen Mathai <jaisen@jmathai.com>
 */
class Tag extends BaseModel
{
  /**
   * Constructor
   */
  public function __construct()
  {
    parent::__construct();
  }

  /**
   * Adjust the counters on a tag when the permission of an element changes
   *
   * @param array $tags An array of tags (strings get converted)
   * @param int $permission Permission of 1 or 0
   */
  public function adjustCounters($tags, $permission)
  {
    if(!is_array($tags))
      $tags = (array)explode(',', $tags);

    // if being marked public then increment the public count (private is already being tracked)
    // if being marked private then derement the public count
    $value = $permission == 1 ? 1 : -1;
    $this->db->postTagsIncrementer($tags, $value);
  }

  /**
    * Add a batch of tags.
    *
    * @param array $tags An array of tags (strings get converted)
    */
  public function createBatch($tags)
  {
    if(!is_array($tags))
      $tags = (array)explode(',', $tags);

    foreach($tags as $tag)
      $this->update($tag, array());
  }

  /**
    * Delete a tag.
    *
    * @param array $id A string of the tag
    * @return array Tag object augmented with a "weight" property.
    */
  public function delete($id)
  {
    return $this->db->deleteTag($id);
  }

  /**
    * Get a single tag.
    *
    * @param string $tag A string of the tag
    * @return array Tag object augmented with a "weight" property.
    */
  public function getTag($tag = null)
  {
    $userObj = new User;
    $tagField = $userObj->isAdmin() ? 'countPrivate' : 'countPublic';
    $tag = $this->db->getTag($tag);
    if(!$tag || $tag[$tagField] == 0)
      return false;

    $tag['count'] = intval($tag[$tagField]);
    unset($tag['countPrivate'], $tag['countPublic']);
    return $tag;
  }

  public function getTags($filters = null)
  {
    return $this->db->getTags($filters);
  }

  /**
    * Groups tags by weight for tag cloud generation
    * Weights are 1-10.
    *
    * @param array $tags An array of Tag objects optionally passed in else queried from the database.
    * @return array Tag object augmented with a "weight" property.
    */
  public function groupByWeight($tags = null)
  {
    if($tags === null)
    {
      $tags = $this->api->invoke("/tags/list.json");
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
      $range = max(($maxTags - $minTags), 1);
      // step needs to be float so we don't divide by zero
      $step = floatval($range / 9);
      foreach($tags as $key => $tag)
        $tags[$key]['weight'] = intval(($tag['count']-$minTags) / $step)+1;
    }
    return $tags;
  }

  public function sanitize($tag)
  {
    return trim(preg_replace('/,/', '-', $tag));
  }

  public function sanitizeTagsAsString($tags)
  {
    $tagsArray = preg_split('/\s*,\s*/', trim($tags), -1, PREG_SPLIT_NO_EMPTY);
    $tagsArray = $this->deduplicate($tagsArray);
    foreach($tagsArray as $key => $val)
      $tagsArray[$key] = $this->sanitize($val);

    natcasesort($tagsArray);
    return implode(',', $tagsArray);
  }

  /**
    * Update a tag.
    *
    * @param string $id A string of the tag
    * @param array $params Tags and related attributes to update.
    * @return array Tag object augmented with a "weight" property.
    */
  public function update($id, $params)
  {
    $params['owner'] = $this->owner;
    $params['actor'] = $this->getActor();
    return $this->db->postTag($id, $params);
  }

  public function validateParams($params)
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

  private function deduplicate($tags)
  {
    $table = array();
    foreach($tags as $tag)
    {
      $key = strtolower($tag);
      if(!isset($table[$key]))
        $table[$key] = array();
      $table[$key][] = $tag;
    }

    $retval = array();
    foreach($table as $tags)
      $retval[] = min($tags);

    return $retval;
  }
}
