<?php
class ApiPhotoV1Controller extends ApiPhotoController
{

  public function __construct()
  {
    parent::__construct();
  }

  public function list_($filterOpts = null)
  {
    extract($this->parseFilters($filterOpts));
    $apiRes = parent::list_($filterOpts);
    if(empty($apiRes['result']))
    {
      $apiRes['result'][0]['currentPage'] = intval($page);
      $apiRes['result'][0]['currentRows'] = 0;
      $apiRes['result'][0]['pageSize'] = intval($pageSize);
      $apiRes['result'][0]['totalPages'] = 0;
      $apiRes['result'][0]['totalRows'] = 0;
    }

    return $apiRes;
  }
}
