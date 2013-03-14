<?php
/**
  * Webhook controller for API endpoints
  *
  * This controller handles all of the webhook subscription APIs.
  * @author Jaisen Mathai <jaisen@jmathai.com>
  */
class ApiWebhookController extends ApiBaseController
{
  /**
    * Call the parent constructor
    *
    * @return void
    */
  public function __construct()
  {
    parent::__construct();
    $this->webhook = new Webhook;
  }

  /**
    * Create a webhook.
    *
    * @return string Standard JSON envelope
    */
  public function create()
  {
    getAuthentication()->requireAuthentication();
    $params = $_POST;
    $id = $this->webhook->create($params);
    if($id)
      return $this->success("Webhook {$id} created", array_merge(array('id' => $id), $params));
    else
      return $this->error("Error creating webhook {$id}", false);
  }

  /**
    * Delete a webhook specified by the ID.
    *
    * @param string $id ID of the webhook to be deleted.
    * @return string Standard JSON envelope
    */
  public function delete($id)
  {
    getAuthentication()->requireAuthentication();
    $status = $this->webhook->delete($id);
    if($status)
      return $this->noContent('Webhook deleted successfully', true);
    else
      return $this->error('Webhook deletion failure', false);
  }

  /**
    * Update a webhook specified by the ID.
    *
    * @param string $id ID of the webhook to be updated.
    * @return string Standard JSON envelope
    */
  public function update($id)
  {
    getAuthentication()->requireAuthentication();
    $params = $_POST;
    $id = $this->webhook->update($id, $params);
    if($id)
      return $this->success("Webhook {$id} updated", array_merge(array('id' => $id), $params));
    else
      return $this->error("Error updating webhook {$id}", false);
  }

  /**
    * Retrieve a webhook from the remote datasource.
    *
    * @param string $id ID of the webhook to be viewed.
    * @return string Standard JSON envelope
    */
  public function view($id)
  {
    getAuthentication()->requireAuthentication();
    $webhook = $this->webhook->getById($id);
    if($webhook)
      return $this->success("Successfully retrieved webhook ({$id})", $webhook);
    else
      return $this->error("Error getting webhook ({$id})", false);
  }

  /**
    * Retrieve a list of the user's webhooks from the remote datasource.
    *
    * @return string Standard JSON envelope
    */
  public function list_($topic = null)
  {
    getAuthentication()->requireAuthentication();
    $webhooks = $this->webhook->getAll($topic);
    if($webhooks)
      return $this->success("Successfully retrieved webhooks", $webhooks);
    else
      return $this->error("Error getting webhooks", false);
  }
}
