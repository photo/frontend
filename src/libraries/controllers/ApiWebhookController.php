<?php
/**
  * Webhook controller for API endpoints
  *
  * This controller handles all of the webhook subscription APIs.
  * @author Jaisen Mathai <jaisen@jmathai.com>
  */
class ApiWebhookController extends BaseController
{
  /**
    * Create a webhook.
    *
    * @return string Standard JSON envelope
    */
  public static function create()
  {
    //getAuthentication()->requireAuthentication();
    $params = $_POST;
    $id = Webhook::add($params);
    if($id)
      return self::success("Webhook {$id} created", array_merge(array('id' => $id), $params));
    else
      return self::error("Error creating webhook {$id}", false);
  }

  /**
    * Delete a webhook specified by the ID.
    *
    * @param string $id ID of the webhook to be deleted.
    * @return string Standard JSON envelope
    */
  public static function delete($id)
  {
    //getAuthentication()->requireAuthentication();
    $status = Webhook::delete($id);
    if($status)
      return self::success('Webhook deleted successfully', true);
    else
      return self::error('Webhook deletion failure', false);
  }

  /**
    * Update a webhook specified by the ID.
    *
    * @param string $id ID of the webhook to be updated.
    * @return string Standard JSON envelope
    */
  public static function update($id)
  {
    //getAuthentication()->requireAuthentication();
    $params = $_POST;
    $id = Webhook::update($id, $params);
    if($id)
      return self::success("Webhook {$id} updated", array_merge(array('id' => $id), $params));
    else
      return self::error("Error updating webhook {$id}", false);
  }

  /**
    * Retrieve a webhook from the remote datasource.
    *
    * @param string $id ID of the webhook to be viewed.
    * @return string Standard JSON envelope
    */
  public static function view($id)
  {
    //getAuthentication()->requireAuthentication();
    $webhook = Webhook::getById($id);
    if($webhook)
      return self::success("Successfully retrieved webhook ({$id})", $webhook);
    else
      return self::error("Error getting webhook ({$id})", false);
  }

  /**
    * Retrieve a list of the user's webhooks from the remote datasource.
    *
    * @return string Standard JSON envelope
    */
  public static function list_($topic = null)
  {
    //getAuthentication()->requireAuthentication();
    $webhooks = Webhook::getAll($topic);
    if($webhooks)
      return self::success("Successfully retrieved webhooks", $webhooks);
    else
      return self::error("Error getting webhooks", false);
  }
}
