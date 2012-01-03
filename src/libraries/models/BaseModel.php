<?php
/**
 * BaseModel model.
 *
 * Parent class for all models
 * @author Jaisen Mathai <jaisen@jmathai.com>
 */
class BaseModel
{
  /*
   * Constructor
   * Don't put any models in the constructor
   */
  public function __construct()
  {
    $this->api = getApi();
    $this->config = getConfig()->get();
    $this->db = getDb();
    $this->fs = getFs();
    $this->logger = getLogger();
    $this->route = getRoute();
    $this->session = getSession();
  }
}
