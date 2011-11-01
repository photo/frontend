<?php
/**
  * Group controller for API endpoints.
  *
  * @author Jaisen Mathai <jaisen@jmathai.com>
 */
class ApiGroupController extends BaseController
{
  /**
    * Create a new group
    * Returns the newly created group or false as the response data
    *
    * @return string Standard JSON envelope
    */
  public static function create()
  {
    getAuthentication()->requireAuthentication();
    // TODO add crumb check
    $groupId = Group::create($_POST);

    if($groupId !== false)
    {
      $res = getApi()->invoke(sprintf('/group/%s/view.json', $groupId), EpiRoute::httpGet);
      if($res['code'] === 200)
        return self::success('Groups for this user', $res['result']);
    }

    return self::error('Could not create a group', false);
  }

  /**
    * Delete a group
    *
    * @return string Standard JSON envelope
    */
  public static function delete($id)
  {
    getAuthentication()->requireAuthentication();
    $res = Group::delete($id);

    if($res === false)
      return self::error('Could not delete group', false);

    return self::error('Successfully deleted group', true);
  }

  /**
    * Update an existing group
    * Returns the newly created group or false as the response data
    *
    * @return string Standard JSON envelope
    */
  public static function update($id)
  {
    getAuthentication()->requireAuthentication();
    // TODO add crumb check
    $res = Group::update($id, $_POST);

    if($res)
    {
      $group = getApi()->invoke("/group/{$id}/view.json", EpiRoute::httpGet);
      return self::success("Updated group {$id}.", $group['result']);
    }
    else
    {
      return self::error('Could not update this group.', false);
    }
  }

  /**
    * Get the owner's groups
    *
    * @return string Standard JSON envelope
    */
  public static function list_()
  {
    getAuthentication()->requireAuthentication();

    if(!User::isOwner())
      return self::forbidden('You do not have permission to access this API.', false);

    $groups = Group::getGroups();
    if($groups === false)
      return self::error('An error occurred trying to get your groups', false);

    return self::success('A list of your groups', (array)$groups);
  }

  /**
    * Get the owner's group as specified by the groupId
    *
    * @param string $id The id of the group
    * @return string Standard JSON envelope
    */
  public static function view($id)
  {
    getAuthentication()->requireAuthentication();

    $group = Group::getGroup($id);
    if($group === false)
      return self::error('An error occurred trying to get your group', false);

    return self::success('Your group', (array)$group);
  }
}
