<?php
/**
  * Activity controller for API endpoints
  *
  * This controller handles all activity endpoints
  * @author Jaisen Mathai <jaisen@jmathai.com>
  */
class ApiActivityController extends ApiBaseController
{
  /**
    * Call the parent constructor
    *
    * @return void
    */
  public function __construct()
  {
    parent::__construct();
    $this->activity = new Activity;
  }

  public function create()
  {
    getAuthentication()->requireAuthentication();
    getAuthentication()->requireCrumb();
    $attributes = $_POST;
    if(isset($attributes['crumb']))
      unset($attributes['crumb']);

    $elementId = $attributes['elementId'];
    unset($attributes['elementId']);

    $status = $this->activity->create($elementId, $attributes);
    if($status !== false)
      return $this->success('Created activity for user', true);
    else
      return $this->error('Could not create activities', false);
  }

  public function list_($filterOpts = null)
  {
    // parse parameters in request
    extract($this->parseFilters($filterOpts));
    $activities = $this->activity->list_($filters, $pageSize);
    if(isset($_GET['groupBy']))
      $activities = $this->groupActivities($activities, $_GET['groupBy']);

    if($activities !== false)
      return $this->success("User's list of activities", $activities);
    else{      
      return $this->error('Could not get activities', false);
    }
  }

  public function purge()
  {
    getAuthentication()->requireAuthentication();
    getAuthentication()->requireCrumb();
    $status = $this->activity->purge();
    if($status !== false)
      return $this->success('Purged user activities', true);
    else
      return $this->error('Purged user activities', false);
  }

  public function view($id)
  {
    $activity = $this->activity->view($id);
    if($activity !== false)
      return $this->success("Activity {$id}", $activity);
    else
      return $this->error('Could not get activity', false);
    
  }

  protected function groupActivities($activities, $groupBy)
  {
    switch($groupBy)
    {
      case 'hour':
        $fmt = 'YmdH';
        break;
      case 'day':
      default:
        $fmt = 'Ymd';
        break;
    }

    $return = array();
    foreach($activities as $activity)
    {
      $grp = sprintf('%s-%s', date($fmt, $activity['dateCreated']), $activity['type']);
      $return[$grp][] = $activity;
    }

    return $return;
  }

  protected function parseFilters($filterOpts)
  {
    $pageSize = 10;
    $filters = array('sortBy' => 'dateCreated,desc');
    if($filterOpts !== null)
    {
      $filterOpts = (array)explode('/', $filterOpts);
      foreach($filterOpts as $value)
      {
        $dashPosition = strpos($value, '-');
        if(!$dashPosition)
          continue;

        $parameterKey = substr($value, 0, $dashPosition);
        $parameterValue = substr($value, ($dashPosition+1));
        switch($parameterKey)
        {
          case 'pageSize':
            $pageSize = intval($parameterValue);
            break;
          case 'type':
            $filters['type'] = $value;
          default:
            $filters[$parameterKey] = $parameterValue;
            break;
        }
      }
    }
    // merge path parameters with GET parameters. GET parameters override
    if(isset($_GET['pageSize']) && intval($_GET['pageSize']) == $_GET['pageSize'])
      $pageSize = intval($_GET['pageSize']);
    $filters = array_merge($filters, $_GET);

    return array('filters' => $filters, 'pageSize' => $pageSize);

  }
}
