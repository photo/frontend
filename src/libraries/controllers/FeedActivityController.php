<?php
/**
  * Activity controller for feed endpoints
  *
  * @author Michel Valdrighi <michelv@gmail.com>
  */
class FeedActivityController extends ApiActivityController
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

    $this->theme = getTheme();
    $this->template->utility = $this->utility;
    $this->template->url = $this->url;
  }

  /**
    * Retrieve a list of the user's photo uploads from the remote datasource.
    * The $filterOpts are values from the path but can also be in _GET.
    * /photos/page-2/tags-favorites.json is identical to /photos.json?page=2&tags=favorites
    *
    * @param string $filterOpts Options on how to filter the list of photos.
    * @return string Standard JSON envelope
    */
  public function list_($filterOpts = null)
  {
    $args = func_get_args();
    $feed_format = $args[1];
    if ($feed_format === null)
      $feed_format = 'atom';

    // parse parameters in request
    extract($this->parseFilters($filterOpts));
    $activities = $this->activity->list_($filters, $pageSize);
    if(isset($_GET['groupBy']))
      $activities = $this->groupActivities($activities, $_GET['groupBy']);
    else 
      $activities = $this->groupActivities($activities, 'hour');

    if($activities !== false) {
      $last_activity = current($activities);
      $last_activity_item = current($last_activity);
      $last_updated_timestamp = $last_activity_item['data']['dateUploaded'];
    } else {
      $last_updated_timestamp = time();
    }

    if($this->config->user)
    {
      $author_email = $this->config->user->email;
      if ($this->config->user->name)
        $author_name = '';
      else
        $author_name = $this->utility->getEmailHandle($author_email, false);
    }
    else
    {
      $author_email = '';
      $author_name = '';
    }

    if($this->config->site->baseUrl)
      $site_base_url = $this->config->site->baseUrl;
    else
      $site_base_url = '';

    if($feed_format == 'atom')
    {
      $data = array(
        'title' => getConfig()->get('titles')->default,
        'link' => $site_base_url . '/activities/list.atom',
        'updated' => gmdate('Y-m-d\TH:i:s\Z', $last_updated_timestamp),
        'author' => array(
          'name' => $author_name,
          'email' => $author_email
        ),
        'base_url' => $site_base_url,
        'id' => $site_base_url . '/'
      );

      header('Content-type: application/atom+xml');

      $this->theme->display($this->utility->getTemplate('feed-atom.php'), array('items' => $this->prepareFeedItems($activities, $site_base_url), 'data' => $data));
    }

    exit(0);
  }

  protected function prepareFeedItems($activities, $site_base_url)
  {
    $feed_items = array();
    $photoSize = $this->config->feed->photoSize;
    if ($photoSize === null)
      $photoSize = '100x100xCR';

    if (count($activities) > 0) {
      foreach ($activities as $activity_title => $activity) {
        $feed_item = array();

        $titles = array();
        $photos = array();
        $tags = array();

        foreach ($activity as $item) {
          // TODO: support other activity types in the future? -- mv
          if ($item['type'] == 'photo-upload') {
            $data = $item['data'];

            if (isset($data['title']))
              $titles[] = $data['title'];
            else
              $titles[] = $data['filenameOriginal'];

            $photo = array(
              'url' => $site_base_url . $this->url->photoView($data['id'], null, false),
              'src' => $data[sprintf('path%s', $photoSize)],
              'title' => $data['title'],
              'description' => $data['description'],
            );

            $photos[] = $photo;

            // add tags
            $tags = array_merge($tags, $data['tags']);

            // use the first photo's url and upload date for the item
            if (!isset($feed_item['link'])) {
              $feed_item['link'] = $photo['url'];
              $feed_item['updated'] = $data['dateUploaded'];
              $feed_item['license'] = $data['license'];
            }
          }
        }

        $feed_item['title'] = implode(', ', $titles);
        $feed_item['photos'] = $photos;
        $feed_item['tags'] = array_unique($tags);

        $feed_items[] = $feed_item;
      }
    }

    return $feed_items;
  }

  protected function parseFilters($filterOpts)
  {
    $pageSize = $this->config->feed->pageSize;
    if ($pageSize === null)
      $pageSize = 20;

    $filters = array('sortBy' => 'dateCreated,desc', 'groupBy' => 'hour', 'type' => 'photo-upload');
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

    // force only public items
    $filters['permission'] = 0;

    return array('filters' => $filters, 'pageSize' => $pageSize);
  }
}
