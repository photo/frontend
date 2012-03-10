<?php
/**
 * Amazon AWS SimpleDb implementation for DatabaseInterface
 *
 * This class defines the functionality defined by DatabaseInterface for AWS SimpleDb.
 * @author Jaisen Mathai <jaisen@jmathai.com>
 */
class DatabaseDynamoDb implements DatabaseInterface
{
  /**
    * Member variables holding the names to the SimpleDb domains needed and the database object itself.
    * @access private
    */
  private $config, $db, $domainAction, $domainCredential, $domainPhoto, 
    $domainTag, $domainUser, $domainWebhook, $errors = array(), $owner;

  private $SCAN_LIMIT = 500;

  /**
    * Constructor
    *
    * @return void
    */
  public function __construct($config = null, $params = null)
  {
    $this->config = !is_null($config) ? $config : getConfig()->get();


    $utilityObj = new Utility;
    if(!is_null($params) && isset($params['db']))
      $this->db = $params['db'];
    else
      $aws_info = array("default_cache_config" => "/tmp", "key" => $utilityObj->decrypt($this->config->credentials->awsKey), "secret" => $utilityObj->decrypt($this->config->credentials->awsSecret));
      $this->db = new AmazonDynamoDB($aws_info);

    $this->domainPhoto = $this->config->aws->dynamoDbPrefix.'Photo';;
    $this->domainAction = $this->config->aws->dynamoDbPrefix.'Action';
    $this->domainCredential = $this->config->aws->dynamoDbPrefix.'Credential';
    $this->domainGroup = $this->config->aws->dynamoDbPrefix.'Group';
    $this->domainUser = $this->config->aws->dynamoDbPrefix.'User';
    $this->domainTag = $this->config->aws->dynamoDbPrefix.'Tag';
    $this->domainWebhook = $this->config->aws->dynamoDbPrefix.'Webhook';

    if(isset($this->config->user))
      $this->owner = $this->config->user->email;
  }

  /**
    * TODO remove this crap and use postPhoto instead
    */
  public function addAttribute($id, $keyValuePairs, $replace = true)
  {
    getLogger()->info("Starting addAttribute");
    $res = $this->db->put_attributes($this->domainPhoto, $id, $keyValuePairs, $replace);
    return $res->isOK();
  }

  /**
    * Delete an action from the database
    *
    * @param string $id ID of the action to delete
    * @return boolean
    */
  public function deleteAction($id)
  {
    getLogger()->info("Starting deleteAction");

    $res = $this->db->delete_item(array(
	'TableName' => $this->domainAction,
	'Key' => array(
		'HashKeyElement' => array( // "id" column
			AmazonDynamoDB::TYPE_STRING => $id
		),
	)
    ));

    getLogger()->info("End of deleteAction");
    $this->logErrors($res);
    return $res->isOK();

  }

  /**
    * Delete credential
    *
    * @return boolean
    */
  public function deleteCredential($id)
  {
    getLogger()->info("Starting deleteCredential");

    $res = $this->db->delete_item(array(
	'TableName' => $this->domainCredential,
	'Key' => array(
		'HashKeyElement' => array( // "id" column
			AmazonDynamoDB::TYPE_STRING => $id
		),
	)
    ));

    getLogger()->info("End of deleteCredential");
    $this->logErrors($res);
    return $res->isOK();
  }

  /**
    * Delete a group from the database
    *
    * @param string $id ID of the group to delete
    * @return boolean
    */
  public function deleteGroup($id)
  {
    getLogger()->info("Starting deleteGroup");

    $res = $this->db->delete_item(array(
	'TableName' => $this->domainGroup,
	'Key' => array(
		'HashKeyElement' => array( // "id" column
			AmazonDynamoDB::TYPE_STRING => $id
		),
	)
    ));

    getLogger()->info("End of deleteGroup");
    $this->logErrors($res);
    return $res->isOK();
  }

  /**
    * Delete a photo from the database
    *
    * @param string $id ID of the photo to delete
    * @return boolean
    */
  public function deletePhoto($photo)
  {
    getLogger()->info("Starting deletePhoto");
    if(!isset($photo['id']))
      return false;

    $res = $this->db->delete_item(array(
	'TableName' => $this->domainPhoto,
	'Key' => array(
		'HashKeyElement' => array( // "id" column
			AmazonDynamoDB::TYPE_STRING => $photo['id']
		),
	)
    ));

    getLogger()->info(print_r($res,1));
    getLogger()->info("End of deletePhoto");
    $this->logErrors($res);
    return $res->isOK();
  }

  /**
    * Delete a tag from the database
    *
    * @param string $id ID of the tag to delete
    * @return boolean
    */
  public function deleteTag($id)
  {
    getLogger()->info("Starting deleteTag");

    $res = $this->db->delete_item(array(
	'TableName' => $this->domainTag,
	'Key' => array(
		'HashKeyElement' => array( // "id" column
			AmazonDynamoDB::TYPE_STRING => $id
		),
	)
    ));

    getLogger()->info("End of deleteTag");
    $this->logErrors($res);
    return $res->isOK();
  }

  /**
    * Delete a webhook from the database
    *
    * @param string $id ID of the webhook to delete
    * @return boolean
    */
  public function deleteWebhook($id)
  {
    getLogger()->info("Starting deleteWebhook");

    $res = $this->db->delete_item(array(
	'TableName' => $this->domainWebhook,
	'Key' => array(
		'HashKeyElement' => array( // "id" column
			AmazonDynamoDB::TYPE_STRING => $id
		),
	)
    ));

    getLogger()->info("End of deleteWebhook");
    $this->logErrors($res);
    return $res->isOK();
  }

  /**
    * Gets diagnostic information for debugging.
    *
    * @return array
    */
  public function diagnostics()
  {
    $diagnostics = array();
    $utilityObj = new Utility;
    $domains = array('', 'Action', 'Credential', 'Group', 'User', 'Tag', 'Webhook');
    $queue = $this->getBatchRequest();
    foreach($domains as $domain)
      $this->db->batch($queue)->domain_metadata("{$this->domainPhoto}{$domain}");
    $responses = $this->db->batch($queue)->send();
    if($responses->areOK())
    {
      $diagnostics[] = $utilityObj->diagnosticLine(true, 'All SimpleDb domains are accessible.');
    }
    else
    {
      foreach($responses as $key => $res)
      {
        if((int)$res->status !== 200)
          $diagnostics[] = $utilityObj->diagnosticLine(false, sprintf('The SimpleDb domains "%s" is NOT accessible.', $domains[$key]));
      }
    }
    return $diagnostics;
  }

  /**
    * Get a list of errors
    *
    * @return array
    */
  public function errors()
  {
    return $this->errors;
  }

  /**
    * Executes an upgrade script
    *
    * @return void
    */
  public function executeScript($file, $database)
  {
    if($database != 'dynamodb')
      return;

    $status = include $file;
    return $status;
  }

  /**
    * Retrieve an action with $id
    *
    * @param string $id ID of the action to get
    * @return mixed Array on success, FALSE on failure
    */
  public function getAction($id)
  {
    getLogger()->info("Starting getAction");

    $res = $this->db->scan(array(
	'TableName' => $this->domainAction,
	'ScanFilter' => array(
		'status' => array(
			'ComparisonOperator' => AmazonDynamoDB::CONDITION_EQUAL,
			'AttributeValueList' => array(
				array( AmazonDynamoDB::TYPE_STRING => '1' )
			)
		),
		'id' => array(
			'ComparisonOperator' => AmazonDynamoDB::CONDITION_EQUAL,
			'AttributeValueList' => array(
				array( AmazonDynamoDB::TYPE_STRING => $id)
			)
		),
	)
    ));

    # Scan for more results if there are any
    if (isset($res->body->LastEvaluatedKey))
    {
	    $res2 = $this->db->scan(array(
		'TableName' => $this->domainAction,
		'ScanFilter' => array(
			'status' => array(
				'ComparisonOperator' => AmazonDynamoDB::CONDITION_EQUAL,
				'AttributeValueList' => array(
					array( AmazonDynamoDB::TYPE_STRING => '1' )
				)
			),
			'id' => array(
				'ComparisonOperator' => AmazonDynamoDB::CONDITION_EQUAL,
				'AttributeValueList' => array(
					array( AmazonDynamoDB::TYPE_STRING => $id)
				)
			),
		)
	    ));
    }

    getLogger()->info(print_r($res->body,1));
    $this->logErrors($res);
    if(isset($res->body->Items))
    {
	foreach($res->body->Items->{0} as $val)
		$actions[] = self::normalizeAction($val);
	return $actions[0];
    }
    else
      return false;

  }

  /**
    * Retrieves activity
    *
    * @return mixed Array on success, FALSE on failure
    */
  public function getActivity($id)
  {
    return false;
    $res = $this->db->select("SELECT * FROM `{$this->domainActivities}` WHERE itemName()='{$id}'", array('ConsistentRead' => 'true'));
    $this->logErrors($res);
    if(isset($res->body->SelectResult->Item))
      return self::normalizeActivity($res->body->SelectResult->Item);
    else
      return false;
  }

  /**
    * Retrieves activities
    *
    * @return mixed Array on success, FALSE on failure
    */
  public function getActivities()
  {
    return false;
    $res = $this->db->select("SELECT * FROM `{$this->domainActivities}`", array('ConsistentRead' => 'true'));
    $this->logErrors($res);
    if(isset($res->body->SelectResult->Item))
      return self::normalizeActivity($res->body->SelectResult->Item);
    else
      return false;
  }

  /**
    * Retrieves album
    *
    * @param string $id ID of the album to get
    * @param string $email email of viewer to determine which albums they have access to
    * @return mixed Array on success, FALSE on failure
    */
  public function getAlbum($id, $email)
  {
    return false;
  }

  /**
    * Retrieve elements for an album
    *
    * @param string $id ID of the album to get elements of
    * @return mixed Array on success, FALSE on failure
    */
  public function getAlbumElements($id)
  {
    return false;
  }

  /**
    * Retrieve albums
    *
    * @param string $email email of viewer to determine which albums they have access to
    * @return mixed Array on success, FALSE on failure
    */
  public function getAlbums($email)
  {
  }


  /**
    * Retrieve a credential with $id
    *
    * @param string $id ID of the credential to get
    * @return mixed Array on success, FALSE on failure
    */
  public function getCredential($id)
  {
    getLogger()->info("Starting getCredential");

    $res = $this->db->get_item(array(
	'TableName' => $this->domainCredential,
	'Key' => array(
		'HashKeyElement' => array( // "id" column
			AmazonDynamoDB::TYPE_STRING => $id
		),
	),
    ));

    $this->logErrors($res);
    if(isset($res->body->Item))
      return self::normalizeCredential($res->body->Item);
    else
      return false;
  }

  /**
    * Retrieve a credential by userToken
    *
    * @param string $userToken userToken of the credential to get
    * @return mixed Array on success, FALSE on failure
    */
  public function getCredentialByUserToken($userToken)
  {
    getLogger()->info("Starting getCredentialByUserToken");

    $res = $this->db->scan(array(
	'TableName' => $this->domainCredential,
	'ScanFilter' => array(
		'status' => array(
			'ComparisonOperator' => AmazonDynamoDB::CONDITION_EQUAL,
			'AttributeValueList' => array(
				array( AmazonDynamoDB::TYPE_STRING => '1' )
			)
		),
		'userToken' => array(
			'ComparisonOperator' => AmazonDynamoDB::CONDITION_EQUAL,
			'AttributeValueList' => array(
				array( AmazonDynamoDB::TYPE_STRING => $userToken )
			)
		),
	)
    ));

    getLogger()->info(print_r($res->body,1));
    $this->logErrors($res);
    if(isset($res->body->Items))
    {
	foreach($res->body->Items->{0} as $val)
	{
		$credentials[] = self::normalizeCredential($val);
		getLogger()->info(print_r($credentials,1));
	}
	return $credentials[0];
    }
    else
      return false;
  }

  /**
    * Retrieve credentials
    *
    * @return mixed Array on success, FALSE on failure
    */
  public function getCredentials()
  {
	getLogger()->info("Starting getCredentials");

    	$scanResults = array();

	$res = $this->db->scan(array(
		'TableName' => $this->domainCredential,
		'ScanFilter' => array(
			'status' => array(
				'ComparisonOperator' => AmazonDynamoDB::CONDITION_EQUAL,
				'AttributeValueList' => array(
					array( AmazonDynamoDB::TYPE_STRING => '1' )
				)
			),
		)
	));

	foreach($res->body->Items->{0} as $result)
		$scanResults[] = $result;

	# Scan for more results if there are any
	while (isset($res->body->LastEvaluatedKey)) 
	{
		$res = $this->db->scan(array(
			'TableName' => $this->domainCredential,
			'ScanFilter' => $buildQuery['where'],
			'Limit' => $this->SCAN_LIMIT,
			'ExclusiveStartKey' => $res->body->LastEvaluatedKey->to_array()->getArrayCopy()
		));

		foreach($res->body->Items->{0} as $result)
			$scanResults[] = $result;
	}

	if(!$res->isOK())
		return false;

	$credentials = array();
	if(isset($res->body->Items))
	{
		foreach($res->body->Items->{0} as $val)
		{
			$credentials[] = self::normalizeCredential($val);
			getLogger()->info(print_r($credentials,1));
		}
		return $credentials;
	}
	else
	{
		return false;
	}

  }

  /**
    * Retrieve group from the database specified by $id
    *
    * @param string $id id of the group to return
    * @return mixed Array on success, FALSE on failure
    */
  public function getGroup($id = null)
  {
    getLogger()->info("Starting getGroup");

    $res = $this->db->get_item(array(
	'TableName' => $this->domainGroup,
	'Key' => array(
		'HashKeyElement' => array( // "id" column
			AmazonDynamoDB::TYPE_STRING => $id
		),
	),
    ));

    getLogger()->info(print_r($res,1));
    $this->logErrors($res);
    if(isset($res->body->Item))
      return self::normalizeGroup($res->body->Item);
    else
      return false;

    getLogger()->info("Ending getGroup");

  }

  /**
    * Retrieve groups from the database optionally filter by member (email)
    *
    * @param string $email email address to filter by
    * @return mixed Array on success, NULL on empty, FALSE on failure
    */
  public function getGroups($email = null)
  {
    getLogger()->info("Starting getGroups");

    if(empty($email))
	$res = $this->db->scan(array(
		'TableName' => $this->domainGroup,
	));
    else
	$res = $this->db->scan(array(
		'TableName' => $this->domainGroup,
		'ScanFilter' => array(
			'members' => array(
				'ComparisonOperator' => AmazonDynamoDB::CONDITION_CONTAINS,
				'AttributeValueList' => array(
					array( AmazonDynamoDB::TYPE_STRING => $email )
				)
			),

		)
	));

    $this->logErrors($res);

    if(isset($res->body->Items->{0}))
    {
      $groups = array();
      foreach($res->body->Items->{0} as $group)
        $groups[] = self::normalizeGroup($group);

      usort($groups, function($func_a, $func_b) {
	if ($func_a['name'] == $func_b['name']) return 0;
	return ($func_a['name'] > $func_b['name']) ? -1 : 1;
      });

      return $groups;
    }

    return false;
  }

  /**
    * Get a photo specified by $id
    *
    * @param string $id ID of the photo to retrieve
    * @return mixed Array on success, FALSE on failure
    */
  public function getPhoto($id)
  {
    getLogger()->info("Starting getPhoto");

    $res = $this->db->get_item(array(
	'TableName' => $this->domainPhoto,
	'Key' => array(
		'HashKeyElement' => array( // "id" column
			AmazonDynamoDB::TYPE_STRING => $id
		),
	),
    ));

    $this->logErrors($res);
    if(isset($res->body->Item))
      return self::normalizePhoto($res->body->Item);
    else
      return false;
  }

  /**
    * Retrieve the next and previous photo surrounding photo with $id
    *
    * @param string $id ID of the photo to get next and previous for
    * @return mixed Array on success, FALSE on failure
    */
  public function getPhotoNextPrevious($id, $filterOpts = null)
  {
    $photo = $this->getPhoto($id);
    if(!$photo)
      return false;

    if (isset($filterOpts['tags']))
    {
	$tags = explode(",", $filterOpts['tags']);
	unset($filterOpts['tags']);
    }

    $buildQuery = self::buildQuery($filterOpts, null, null);

    $res = $this->db->scan(array(
	'TableName' => $this->domainPhoto,
	'ScanFilter' => $buildQuery['where'],
    ));

    $this->logErrors($res);
    if(!$res->isOK())
      return false;

    $photos = array();
    foreach($res->body->Items->{0} as $photo)
    {
      $photo = $this->normalizePhoto($photo);
      if (isset($tags))
      {
	if ($tags === array_intersect($tags, $photo['tags']))
      		$photos[] = $photo;
      }
      else
      	$photos[] = $photo;
    }

    usort($photos, function($func_a, $func_b) {
	if ($func_a['dateTaken'] == $func_b['dateTaken']) return 0;
	return ($func_a['dateTaken'] > $func_b['dateTaken']) ? -1 : 1;
    });

    $key=0;
    foreach($photos as $item)
	if ($item['id'] == $id)
		break;
	else
		$key++;

    $ret = array();
    if(isset($photos[$key-1]))
      $ret['previous'] = $photos[$key-1];
    if(isset($photos[$key+1]))
      $ret['next'] = $photos[$key+1];

    return $ret;
  }

  /**
    * Retrieve a photo from the database and include the actions on the photo.
    * Actions are stored in a separate domain so the calls need to be made in parallel
    *
    * @param string $id ID of the photo to retrieve
    * @return mixed Array on success, FALSE on failure
    */
  public function getPhotoWithActions($id)
  {
    getLogger()->info("Starting getPhotoWithActions");

    $res = $this->db->get_item(array(
	'TableName' => $this->domainPhoto,
	'Key' => array(
		'HashKeyElement' => array( // "id" column
			AmazonDynamoDB::TYPE_STRING => $id
		),
	),
    ));


    $this->logErrors($res);
    if(isset($res->body->Item))
      $photo = self::normalizePhoto($res->body->Item);

    $res = $this->db->scan(array(
	'TableName' => $this->domainAction,
	'ScanFilter' => array(
		'targetType' => array(
			'ComparisonOperator' => AmazonDynamoDB::CONDITION_EQUAL,
			'AttributeValueList' => array(
				array( AmazonDynamoDB::TYPE_STRING => 'photo' )
			)
		),
		'targetId' => array(
			'ComparisonOperator' => AmazonDynamoDB::CONDITION_EQUAL,
			'AttributeValueList' => array(
				array( AmazonDynamoDB::TYPE_STRING => '{$id}' )
			)
		),
	)
    ));

    $photo['actions'] = array();
    if(isset($res->body->Items))
    {
    	foreach($res->body->Items->{0} as $val)
          $photo['actions'][] = self::normalizeAction($val);
    }

    return $photo;

  }

  /**
    * Get a list of a user's photos filtered by $filter, $limit and $offset
    *
    * @param array $filters Filters to be applied before obtaining the result
    * @param int $limit Total results to return
    * @param offset $offset Starting point of results to return
    * @return mixed Array on success, FALSE on failure
    */
  public function getPhotos($filters = array(), $limit = 20, $offset = null)
  {
    getLogger()->info("Starting getPhotos");
    getLogger()->info(print_r($filters,1));

    if (isset($filters['tags']))
    {
	$tags = explode(",", $filters['tags']);
	unset($filters['tags']);
    }

    $buildQuery = self::buildQuery($filters, $limit, $offset);

    $scanResults = array();

    $res = $this->db->scan(array(
	'TableName' => $this->domainPhoto,
	'ScanFilter' => $buildQuery['where'],
	'Limit' => $this->SCAN_LIMIT,
    ));

    foreach($res->body->Items->{0} as $result)
	$scanResults[] = $result;

    # Scan for more results if there are any
    while (isset($res->body->LastEvaluatedKey)) 
    {
	$res = $this->db->scan(array(
		'TableName' => $this->domainPhoto,
		'ScanFilter' => $buildQuery['where'],
		'Limit' => $this->SCAN_LIMIT,
		'ExclusiveStartKey' => $res->body->LastEvaluatedKey->to_array()->getArrayCopy()
	));

   	foreach($res->body->Items->{0} as $result)
		$scanResults[] = $result;
    }

    $this->logErrors($res);
    if(!$res->isOK())
      return false;

    $photos = array();
    foreach($scanResults as $photo)
    {
      $photo = $this->normalizePhoto($photo);
      if (isset($tags))
      {
	if ($tags === array_intersect($tags, $photo['tags']))
	{
      		$photos[] = $photo;
	}
      }
      else
      	$photos[] = $photo;
    }

    usort($photos, function($func_a, $func_b) {
	if ($func_a['dateTaken'] == $func_b['dateTaken']) return 0;
	return ($func_a['dateTaken'] > $func_b['dateTaken']) ? -1 : 1;
    });

    $totalPhotos = count($photos);

    if (array_key_exists("page", $filters))
    {
	switch($filters['page'])
	{
		case 1:
			$start = 0;
			$end = $start + $limit;
			if ($end > $totalPhotos)
				$end = $totalPhotos;
			break;

		case 2:
			$start = $limit;
			$end = $start + $limit;
			if ($end > $totalPhotos)
				$end = $totalPhotos;
			break;

		default:
			$start = $limit * ($filters['page'] - 1);
			$end = $start + $limit;
			if ($end > $totalPhotos)
				$end = $totalPhotos;
			break;
	}
    }
    else
    {
	$start = 0;
	$end = $limit;
	if ($end > $totalPhotos)
		$end = $totalPhotos;
    }

    $return = array();
    for ($i = $start; $i < $end; $i++)
	$return[] = $photos[$i];

    if(!empty($return))
      $return[0]['totalRows'] = $totalPhotos;

    return $return;
  }

  /**
    * Get a tag
    * Consistent read set to false
    *
    * @param string $tag tag to be retrieved
    * @return mixed Array on success, FALSE on failure
    */
  public function getTag($tag)
  {
    getLogger()->info("Starting getTag");

    $res = $this->db->get_item(array(
	'TableName' => $this->domainTag,
	'Key' => array(
		'HashKeyElement' => array( // "id" column
			AmazonDynamoDB::TYPE_STRING => $owner
		),
	),
    ));

    $item = $res->body->Item;
    $this->logErrors($res);

    if(isset($res->body->Item))
      return self::normalizeTag($res->body->Item);
    else
      return null;
  }

  /**
    * Get tags filtered by $filter
    * Consistent read set to false
    *
    * @param array $filters Filters to be applied to the list
    * @return mixed Array on success, FALSE on failure
    */
  public function getTags($filters = array())
  {
    getLogger()->info("Starting getTags");
    getLogger()->info(print_r($filters,1));
    $countField = 'countPublic';
    if(isset($filters['permission']) && $filters['permission'] == 0)
      $countField = 'countPrivate';

    if(isset($filters['search']) && $filters['search'] != '')
    {
	getLogger()->info("Searching for tags");
	$res = $this->db->scan(array(
		'TableName' => $this->domainTag,
		'ScanFilter' => array(
			$countField => array(
				'ComparisonOperator' => AmazonDynamoDB::CONDITION_GREATER_THAN,
				'AttributeValueList' => array(
					array( AmazonDynamoDB::TYPE_STRING => '0' )
				)
			),
			'id' => array(
				'ComparisonOperator' => AmazonDynamoDB::CONDITION_CONTAINS,
				'AttributeValueList' => array(
					array( AmazonDynamoDB::TYPE_STRING => $filters['search'] )
				)
			),

		)
	));
    }
    else
    {
	getLogger()->info("Getting all tags");
	$res = $this->db->scan(array(
		'TableName' => $this->domainTag,
		'ScanFilter' => array(
			$countField => array(
				'ComparisonOperator' => AmazonDynamoDB::CONDITION_GREATER_THAN,
				'AttributeValueList' => array(
					array( AmazonDynamoDB::TYPE_STRING => '0' )
				)
			),
		)
	));
    }

    if(!$res->isOK())
      return false;

    $tags = array();
    if(isset($res->body->Items))
    	foreach($res->body->Items->{0} as $val)
          $tags[] = self::normalizeTag($val);

    # Sort the returned tags
    usort($tags, function($func_a, $func_b) {
	if ($func_a['id'] == $func_b['id']) return 0;
	return ($func_a['id'] < $func_b['id']) ? -1 : 1;
    });

    return $tags;
  }

  /**
    * Get the user record entry.
    *
    * @return mixed Array on success, NULL if user record is empty, FALSE on error
    */
  public function getUser($owner = null)
  {
    getLogger()->info("Starting getUser");

    if($owner === null)
      $owner = $this->owner;

    $res = $this->db->get_item(array(
	'TableName' => $this->domainUser,
	'Key' => array(
		'HashKeyElement' => array( // "id" column
			AmazonDynamoDB::TYPE_STRING => $owner
		),
	),
    ));

    $item = $res->body->Item;
    $this->logErrors($res);

    if(isset($res->body->Item))
      return self::normalizeUser($res->body->Item);
    else
      return null;
  }

  /**
    * Get a webhook specified by $id
    *
    * @param string $id ID of the webhook to retrieve
    * @return mixed Array on success, FALSE on failure
    */
  public function getWebhook($id)
  {
    getLogger()->info("Starting getWebhook");

    $res = $this->db->get_item(array(
	'TableName' => $this->domainWebhook,
	'Key' => array(
		'HashKeyElement' => array( // "id" column
			AmazonDynamoDB::TYPE_STRING => $id
		),
	),
    ));

    $item = $res->body->Item;
    $this->logErrors($res);

    if(isset($res->body->Item))
      return self::normalizeWebhook($res->body->Item);
    else
      return null;

  }

  /**
    * Get all webhooks for a user
    *
    * @return mixed Array on success, FALSE on failure
    */
  public function getWebhooks($topic = null)
  {
    getLogger()->info("Starting getWebhooks");

    if($topic)
    {
	$res = $this->db->scan(array(
		'TableName' => $this->domainWebhook,
		'ScanFilter' => array(
			'topic' => array(
				'ComparisonOperator' => AmazonDynamoDB::CONDITION_EQUAL,
				'AttributeValueList' => array(
					array( AmazonDynamoDB::TYPE_STRING => '$topic' )
				)
			),
		)
	));
    }
    else
    {
	$res = $this->db->scan(array(
		'TableName' => $this->domainWebhook,
	));
    }

    $this->logErrors($res);
    if(!$res->isOK())
      return false;

    if(isset($res->body->Items))
    {
      if(isset($res->body->Item->{0}))
      {
        $webhooks = array();
        foreach($res->body->Item->{0} as $webhook)
          $webhooks[] = $this->normalizeWebhook($webhook);

    	getLogger()->info("Ending getWebhooks");
        return $webhooks;
      }

      getLogger()->info("Ending getWebhooks");
      return null;
    }
    getLogger()->info("Ending getWebhooks");
    return false;
  }

  /**
    * Initialize the database by creating the domains needed.
    * This is called from the Setup controller.
    *
    * @return boolean
    */
  public function initialize($isEditMode)
  {
    if($this->version() !== '0.0.0' && $isEditMode === false)
      return true;

    // simpledb-base.php sets $status
    $status = $this->executeScript(sprintf('%s/upgrade/db/dynamodb/dynamodb-base.php', $this->config->paths->configs), 'dynamodb');
    return $status;
  }

  /**
    * Identification method to return array of strings.
    *
    * @return array
    */
  public function identity()
  {
    return array('dynamodb');
  }

  /**
    * Allows injection of member variables.
    * Primarily used for unit testing with mock objects.
    *
    * @param string $name Name of the member variable
    * @param mixed $value Value of the member variable
    * @return void
    */
  public function inject($name, $value)
  {
    $this->$name = $value;
  }

  /**
    * Add an element to an album
    *
    * @param string $albumId ID of the album to update.
    * @param string $type Type of element
    * @param array $elementIds IDs of the elements to update.
    * @return boolean
    */
  public function postAlbumAdd($albumId, $type, $elementIds)
  {
    return false;
  }

  /**
    * Remove an element from an album
    *
    * @param string $albumId ID of the album to update.
    * @param string $type Type of element
    * @param array $elementIds IDs of the elements to update.
    * @return boolean
    */
  public function postAlbumRemove($albumId, $type, $elementIds)
  {
    return false;
  }

  /**
    * Update the information for an existing credential.
    * This method overwrites existing values present in $params.
    *
    * @param string $id ID of the credential to update.
    * @param array $params Attributes to update.
    * @return boolean
    */
  public function postCredential($id, $params)
  {
    getLogger()->info("Start of postCredential");

    $updates = array();
    foreach (array_keys($params) as $key)
	$updates[$key] = array('Action' => AmazonDynamoDB::ACTION_PUT, 'Value' => array( AmazonDynamoDB::TYPE_STRING => (string)$params[$key] ) );

    getLogger()->info(print_r($updates,1));

    $res = $this->db->update_item(array(
	'TableName' => $this->domainCredential,
	'Key' => array(
		'HashKeyElement' => array( // id column
			AmazonDynamoDB::TYPE_STRING => $id
		)
	),
	'AttributeUpdates' => $updates
    ));

    getLogger()->info("End of postCredential");
    return $res->isOK();
  }

  /**
    * Update a group.
    *
    * @param string $id ID of the group to update.
    * @param array $params Attributes to update.
    * @return boolean
    */
  public function postGroup($id, $params)
  {
    getLogger()->info("Start of postGroup");

    if(empty($id))
      return false;
    elseif(empty($params))
      return true;

    $params = self::prepareGroup($id, $params);
    # Remove the id param as we cannot update it
    unset($params['id']);

    $updates = array();
    foreach (array_keys($params) as $key)
	$updates[$key] = array('Action' => AmazonDynamoDB::ACTION_PUT, 'Value' => array( AmazonDynamoDB::TYPE_STRING => (string)$params[$key] ) );

    $res = $this->db->update_item(array(
	'TableName' => $this->domainGroup,
	'Key' => array(
		'HashKeyElement' => array( // id column
			AmazonDynamoDB::TYPE_STRING => $id
		)
	),
	'AttributeUpdates' => $updates
    ));

    getLogger()->info(print_r($res,1));
    getLogger()->info("End of postGroup");

    $this->logErrors($res);
    return $res->isOK();
  }

  /**
    * Update the information for an existing photo.
    * This method overwrites existing values present in $params.
    *
    * @param string $id ID of the photo to update.
    * @param array $params Attributes to update.
    * @return boolean
    */
  public function postPhoto($id, $params)
  {
    getLogger()->info("Start of postPhoto");

    if(empty($id))
      return false;
    elseif(empty($params))
      return true;

    $params = self::preparePhoto($id, $params);
    # Remove the id param as we cannot update it
    unset($params['id']);

    $updates = array();
    foreach (array_keys($params) as $key)
	$updates[$key] = array('Action' => AmazonDynamoDB::ACTION_PUT, 'Value' => $params[$key]);

    $res = $this->db->update_item(array(
	'TableName' => $this->domainPhoto,
	'Key' => array(
		'HashKeyElement' => array( // id column
			AmazonDynamoDB::TYPE_STRING => $id
		)
	),
	'AttributeUpdates' => $updates
    ));

    getLogger()->info("End of postPhoto");
    return $res->isOK();
  }

  /**
    * Update a single tag.
    * The $params should include the tag in the `id` field.
    * [{id: tag1, count:10, longitude:12.34, latitude:56.78},...]
    *
    * @param array $params Tags and related attributes to update.
    * @return boolean
    */
  public function postTag($id, $params)
  {
    getLogger()->info("Start of postTag");

    $res = $this->db->put_item(array(
	'TableName' => $this->domainTag,
	'Item' => array(
		'id'		=> array( AmazonDynamoDB::TYPE_STRING => $tagObj['id']			),
		'owner'		=> array( AmazonDynamoDB::TYPE_STRING => $this->owner			),
		'countPublic'	=> array( AmazonDynamoDB::TYPE_STRING => $tagObj['countPublic']		),
		'countPrivate'	=> array( AmazonDynamoDB::TYPE_STRING => $tagObj['countPrivate']	),
		'count'		=> array( AmazonDynamoDB::TYPE_STRING => $tagObj['count']		)
		)
    ));

    $this->logErrors($res);
    getLogger()->info("End of postTag");
    return $res->isOK();
  }

  /**
    * Update multiple tags.
    * The $params should include the tag in the `id` field.
    * [{id: tag1, count:10, longitude:12.34, latitude:56.78},...]
    *
    * @param array $params Tags and related attributes to update.
    * @return boolean
    */
  public function postTags($params)
  {
    getLogger()->info("Start of postTags");

    foreach($params as $tagObj)
    {
      if(!isset($tagObj['id']) || empty($tagObj['id']))
        continue;
      $tag = $tagObj['id'];
      unset($tagObj['id']);
      // TODO determine if updating tags requires count to be passed. Doesn't feel like it should.
      if(!isset($tagObj['count']))
        $tagObj['count'] = 0;
      else
        $tagObj['count'] = max(0, intval($tagObj['count']));

      $res = $this->db->put_item(array(
	'TableName' => $this->domainTag,
	'Item' => array(
		'id'		=> array( AmazonDynamoDB::TYPE_STRING => (string)$tag			),
		'owner'		=> array( AmazonDynamoDB::TYPE_STRING => (string)$this->owner		),
		'countPublic'	=> array( AmazonDynamoDB::TYPE_STRING => (string)$tagObj['countPublic']	),
		'countPrivate'	=> array( AmazonDynamoDB::TYPE_STRING => (string)$tagObj['countPrivate']),
		'count'		=> array( AmazonDynamoDB::TYPE_STRING => (string)$tagObj['count']	)
		)
      ));

    }
    $this->logErrors($res);
    getLogger()->info(print_r($res,1));
    getLogger()->info("End of postTags");
    return $res->isOK();
  }

  /**
    * Update the information for the user record.
    * This method overwrites existing values present in $params.
    *
    * @param string $id ID of the user to update which is always 1.
    * @param array $params Attributes to update.
    * @return boolean
    */
  public function postUser($params)
  {
    getLogger()->info("Start of postUser");
    $params = self::prepareUser($params);

    // make sure we don't overwrite an existing user record
    $res = $this->db->update_item(array(
	'TableName' => $this->domainUser,
	'Key' => array(
		'HashKeyElement' => array( // "id" column
			AmazonDynamoDB::TYPE_STRING => $this->owner
		),
	),
	'AttributeUpdates' => array(
		'extra'	=> array(
			'Action' => AmazonDynamoDB::ACTION_PUT,
			'Value' => array(AmazonDynamoDB::TYPE_STRING => $params)
		),
	),
    ));
    $this->logErrors($res);
    return $res->isOK();
  }

  /**
    * Update the information for the webhook record.
    *
    * @param string $id ID of the webhook to update which is always 1.
    * @param array $params Attributes to update.
    * @return boolean
    */
  public function postWebhook($id, $params)
  {
    getLogger()->info("Start of postWebhook");

    $updates = array();
    foreach (array_keys($params) as $key)
	$updates[$key] = array('Action' => AmazonDynamoDB::ACTION_PUT, 'Value' => array( AmazonDynamoDB::TYPE_STRING => (string)$params[$key] ) );

    $res = $this->db->update_item(array(
	'TableName' => $this->domainWebhook,
	'Key' => array(
		'HashKeyElement' => array( // id column
			AmazonDynamoDB::TYPE_STRING => $id
		)
	),
	'AttributeUpdates' => $updates
    ));

    getLogger()->info("End of postWebhook");

    $this->logErrors($res);
    return $res->isOK();
  }

  /**
    * Add a new action to the database
    * This method does not overwrite existing values present in $params - hence "new action".
    *
    * @param string $id ID of the action to update which is always 1.
    * @param array $params Attributes to update.
    * @return boolean
    */
  public function putAction($id, $params)
  {
    getLogger()->info("Start of putAction");
    $res = $this->db->put_attributes($this->domainAction, $id, $params);
    $this->logErrors($res);
    return $res->isOK();
  }

  /**
    * Add a new album to the database
    * This method does not overwrite existing values present in $params - hence "new action".
    *
    * @param string $id ID of the action to update which is always 1.
    * @param array $params Attributes to update.
    * @return boolean
    */
  public function putAlbum($id, $params)
  {
    return false;
  }

  /**
    * Add a new activity to the database
    * This method does not overwrite existing values present in $params - hence "new action".
    *
    * @param string $id ID of the action to update which is always 1.
    * @param array $params Attributes to update.
    * @return boolean
    */
  public function putActivity($id, $params)
  {
    return false;
    $res = $this->db->put_attributes($this->domainActivity, $id, $params);
    $this->logErrors($res);
    return $res->isOK();
  }


  /**
    * Add a new credential to the database
    * This method does not overwrite existing values present in $params - hence "new credential".
    *
    * @param string $id ID of the credential to update which is always 1.
    * @param array $params Attributes to update.
    * @return boolean
    */
  public function putCredential($id, $params)
  {
    getLogger()->info("Start of putCredential");
    getLogger()->info(print_r($params,1));

    $res = $this->db->put_item(array(
	'TableName' => $this->domainCredential,
	'Item' => array(
		'id'			=> array( AmazonDynamoDB::TYPE_STRING => $id ),
		'owner'			=> array( AmazonDynamoDB::TYPE_STRING => $this->owner ),
		'name'			=> array( AmazonDynamoDB::TYPE_STRING => $params['name'] ),
		'clientSecret'		=> array( AmazonDynamoDB::TYPE_STRING => $params['clientSecret'] ),
		'userToken'		=> array( AmazonDynamoDB::TYPE_STRING => $params['userToken'] ),
		'userSecret'		=> array( AmazonDynamoDB::TYPE_STRING => $params['userSecret'] ),
		'verifier'		=> array( AmazonDynamoDB::TYPE_STRING => $params['verifier'] ),
		'type'			=> array( AmazonDynamoDB::TYPE_STRING => $params['type'] ),
		'status'		=> array( AmazonDynamoDB::TYPE_STRING => $params['status'] ),
	)
    ));
    getLogger()->info(print_r($res,1));
    $this->logErrors($res);
    return $res->isOK();
  }

  /**
    * Alias of postGroup
    */
  public function putGroup($id, $params)
  {
    getLogger()->info("Start of putGroup");
    return $this->postGroup($id, $params);
  }

  /**
    * Add a new photo to the database
    * This method does not overwrite existing values present in $params - hence "new photo".
    *
    * @param string $id ID of the photo to update which is always 1.
    * @param array $params Attributes to update.
    * @return boolean
    */
  public function putPhoto($id, $params)
  {
    getLogger()->info("Start of putPhoto");
    $params = self::preparePhoto($id, $params);

    $res = $this->db->put_item(array(
	'TableName' => $this->domainPhoto,
	'Item' => $params
    ));

    getLogger()->info(print_r($res->body,1));
    $this->logErrors($res);
    getLogger()->info("End of putPhoto");
    return $res->isOK();
  }

  /**
    * Add a new tag to the database
    * This method does not overwrite existing values present in $params - hence "new user".
    *
    * @param string $id ID of the user to update which is always 1.
    * @param array $params Attributes to update.
    * @return boolean
    */
  public function putTag($id, $params)
  {
    getLogger()->info("Start of putTag");
    if(!isset($params['count']))
      $params['count'] = 0;
    else
      $params['count'] = max(0, intval($params['count']));
    return $this->postTag($id, $params);
  }

  /**
    * Add a new user to the database
    * This method does not overwrite existing values present in $params - hence "new user".
    *
    * @param string $id ID of the user to update which is always 1.
    * @param array $params Attributes to update.
    * @return boolean
    */
  public function putUser($params)
  {
    getLogger()->info("Start of putUser");
    getLogger()->info($this->owner);

    $params = self::prepareUser($params);
    $res = $this->db->put_item(array(
	'TableName' => $this->domainUser,
	'Item' => array(
		'id'			=> array( AmazonDynamoDB::TYPE_STRING => $this->owner ),
		'extra'			=> array( AmazonDynamoDB::TYPE_STRING => $params ),
	)
    ));
    getLogger()->info(print_r($res,1));
    getLogger()->info("End of putUser");
    $this->logErrors($res);
    return $res->isOK();
  }

  /**
    * Add a new webhook to the database
    *
    * @param string $id ID of the webhook to update which is always 1.
    * @param array $params Attributes to update.
    * @return boolean
    */
  public function putWebhook($id, $params)
  {
    getLogger()->info("Start of putWebhook");
    return $this->postWebhook($id, $params);
  }

  /**
    * Get the current database version
    *
    * @return string Version number
    */
  public function version()
  {
    $user = $this->getUser();
    if(!$user || !isset($user['version']))
      return '0.0.0';

    return $user['version'];
  }

  /**
    * Query builder for searching photos.
    *
    * @param array $filters Filters to be applied before obtaining the result
    * @param int $limit Total results to return
    * @param offset $offset Starting point of results to return
    * @return array Components required to build the query
    */
  private function buildQuery($filters, $limit, $offset)
  {
    // TODO: support logic for multiple conditions
    $where = '';
    $sortBy = null;
    if(!empty($filters) && is_array($filters))
    {
      foreach($filters as $name => $value)
      {
        switch($name)
        {
          case 'groups':
            $where = $this->buildWhere($where, "(groups IN('" . implode("','", $value) . "') OR permission='1')");
            break;
          case 'page':
            if($value > 1)
            {
              $value = min($value, 40); // 40 pages at max of 2,500 recursion limit means 100k photos
              $offset = ($limit * $value) - $limit;
              $page = $value;
            }
            break;
          case 'permission':
            $where = $this->buildWhere($where, array('field' => "permission", 'condition' => AmazonDynamoDB::CONDITION_EQUAL, 'value' => '1'));
            break;
          case 'sortBy':
            $sortBy = 'ORDER BY ' . str_replace(',', ' ', $value);
            $field = substr($value, 0, strpos($value, ','));
            $where = $this->buildWhere($where, array('field' => $field, 'condition' => AmazonDynamoDB::CONDITION_NOT_NULL, 'value' => ""));
            break;
          case 'tags':
            $where = $this->buildWhere($where, array('field' => "tags", 'condition' => AmazonDynamoDB::CONDITION_CONTAINS, 'value' => $value));
            break;
        }
      }
    }

    #if(!empty($offset))
    #{
    #  $iterator = max(1, intval($offset - 1));
    #  $nextToken = null;
    #  $params = array('ConsistentRead' => 'true');
    #  if(!isset($page))
    #    $page = 1;
    #  $currentPage = 1;
    #  $thisLimit = min($iterator, $offset);
    #  do
    #  {
    #    $res = $this->db->select("SELECT * FROM `{$this->domainPhoto}` {$where} {$sortBy} LIMIT {$iterator}", $params);
    #    if(!$res->body->SelectResult->NextToken)
    #      break;
#
#        $nextToken = $res->body->SelectResult->NextToken;
#        $params['NextToken'] = $nextToken;
#        $currentPage++;
#      }while($currentPage < $page);
#    }

    $params = array('ConsistentRead' => 'true');
    if(isset($nextToken) && !empty($nextToken))
      $params['NextToken'] = $nextToken;

    return array('params' => $params, 'where' => $where, 'sortBy' => $sortBy, 'limit' => $limit);
  }

  /**
    * Utility function to help build the WHERE clause for SELECT statements.
    * TODO possibly put duplicate code in a utility class
    *
    * @param string $existing Existing where clause.
    * @param string $add Clause to add.
    * @return string
    */
  private function buildWhere($existing, $add)
  {
    getLogger()->info(print_r($add,1));
    getLogger()->info(print_r($existing,1));

    if(empty($existing))
    {
      $existing = array();
      $condition = $add['condition'];
      $value = $add['value'];

      getLogger()->info($condition);

      if (empty($add['value']))
      {
    	getLogger()->info("Value Empty");
      	$existing[$add['field']] = array('ComparisonOperator' => $condition, );
      }
      else
      {
      	$existing[$add['field']] = array('ComparisonOperator' => $condition, 'AttributeValueList' => array(array(AmazonDynamoDB::TYPE_STRING => $value )));
      }
    }
    else
    {
      $condition = $add['condition'];
      $value = $add['value'];

      if (empty($add['value']))
      {
    	getLogger()->info("Value Empty");
      	$existing[$add['field']] = array('ComparisonOperator' => $condition, );
      }
      else
      	$existing[$add['field']] = array('ComparisonOperator' => $condition, 'AttributeValueList' => array(array(AmazonDynamoDB::TYPE_STRING => $value )));
    }

      return $existing;
  }

  private function logErrors($res)
  {
    if($res instanceof CFArray)
    {
      foreach($res as $r)
        $this->logErrors($r);
    }
    else
    {
      if(!$res->isOK())
      {
        foreach($res->body->Errors as $error)
        {
          $message = $this->errors[] = sprintf('Amazon Web Services error (code %s): %s', $error->Error->Code, $error->Error->Message);
          getLogger()->crit($message);
        }
      }
    }
  }

  /**
    * Gets a CFBatchRequest object for the AWS library
    *
    * @return object
   */
  public function getBatchRequest()
  {
    return new CFBatchRequest();
  }

  /**
    * Normalizes data from simpleDb into schema definition
    *
    * @param SimpleXMLObject $raw An action from SimpleDb in SimpleXML.
    * @return array
    */
  private function normalizeAction($raw)
  {
    getLogger()->info("Starting normalizeAction");
    getLogger()->info(print_r($raw,1));

    $action['id'] = strval($raw->Name->S);
    $action['appId'] = $this->config->application->appId;

    foreach($raw->{0} as $key => $value)
    {
	if ($raw->$key->S)
        {
    		$name = (string)$key;
    		$value = (string)$raw->$key->S;
	}
	else
	{
    		$name = (string)$key;
    		$value = (string)$raw->$key;
	}

	if($name == 'permissions')
		$action[$name] = (array)explode(',', $value);
	else
		$action[$name] = $value;
		
	$action[$name] = $value;
    }

    getLogger()->info(print_r($action,1));

    return $action;

  }

  /**
    * Normalizes data from simpleDb into schema definition
    *
    * @param SimpleXMLObject $raw An action from SimpleDb in SimpleXML.
    * @return array
    */
  private function normalizeCredential($raw)
  {
    getLogger()->info("Starting normalizeCredential");

    $credential = array();

    $credential['id'] = strval($raw->Name->S);
    $credential['appId'] = $this->config->application->appId;

    foreach($raw->{0} as $key => $value)
    {
	if ($raw->$key->S)
        {
    		$name = (string)$key;
    		$value = (string)$raw->$key->S;
	}
	else
	{
    		$name = (string)$key;
    		$value = (string)$raw->$key;
	}

	if($name == 'permissions')
		$credential[$name] = (array)explode(',', $value);
	else
		$credential[$name] = $value;
		
	$credential[$name] = $value;
    }

    getLogger()->info(print_r($credential,1));

    getLogger()->info("Ending normalizeCredential");
    return $credential;
  }

  /**
    * Normalizes data from simpleDb into schema definition
    *
    * @param SimpleXMLObject $raw An action from SimpleDb in SimpleXML.
    * @return array
    */
  private function normalizeGroup($raw)
  {

    getLogger()->info("Starting normalizeGroup");
    getLogger()->info(print_r($raw,1));

    $group['id'] = strval($raw->Name->S);
    $group['appId'] = $this->config->application->appId;

    foreach($raw->{0} as $key => $value)
    {
	if ($raw->$key->S)
        {
    		$name = (string)$key;
    		$value = (string)$raw->$key->S;
	}
	else
	{
    		$name = (string)$key;
    		$value = (string)$raw->$key;
	}

	if($name == 'permissions' || $name == 'members')
		$group[$name] = (array)explode(",", $value);
	else
		$group[$name] = $value;
		
    }

    getLogger()->info("Ending normalizeGroup");
    getLogger()->info(print_r($group,1));
    return $group;

  }

  /**
    * Normalizes data from simpleDb into schema definition
    *
    * @param SimpleXMLObject $raw A photo from SimpleDb in SimpleXML.
    * @return array
    */
  private function normalizePhoto($raw)
  {
    $photo['id'] = strval($raw->Name->S);
    $photo['appId'] = $this->config->application->appId;
    $photo = array('tags' => array());

    foreach($raw->{0} as $key => $value)
    {
	if ($raw->$key->S)
        {
    		$name = (string)$key;
    		$value = (string)$raw->$key->S;
	}
	else
	{
    		$name = (string)$key;
    		$value = (string)$raw->$key;
	}
		

	if($name == 'tags' || $name == 'groups')
	{
		if($value != '')
			$photo[$name] = explode(",", $raw->$key->S);
		continue;
	}
	$photo[$name] = $value;
    }

    // we have to do this because natcasesort preserves array keys and turns it into an object literal (not an array literal)
    if(isset($photo['tags']))
    {
      $tags = $photo['tags'];
      natcasesort($tags);
      $photo['tags'] = array();
      foreach($tags as $tag)
        $photo['tags'][] = $tag;
    }

    return $photo;
  }

  /**
    * Normalizes data from simpleDb into schema definition
    *
    * @param SimpleXMLObject $raw A tag from SimpleDb in SimpleXML.
    * @return array
    */
  private function normalizeTag($raw)
  {
    $tag = array();
    $tag['id'] = strval($raw->id->S);
    $tag['countPublic'] = strval($raw->countPublic->S);
    $tag['countPrivate'] = strval($raw->countPrivate->S);
    $tag['count'] = strval($raw->count->S);

    return $tag;
  }

  /**
    * Normalizes data from simpleDb into schema definition
    *
    * @param SimpleXMLObject $raw A user from SimpleDb in SimpleXML.
    * @return array
    */
  private function normalizeUser($raw)
  {
    $user = array();
    $user['id'] = strval($raw->id->S);

    $jsonParsed = json_decode($raw->extra->S, 1);
    if(!empty($jsonParsed))
    {
      foreach($jsonParsed as $key => $value)
        $user[$key] = $value;
    }

    return $user;
  }

  /**
    * Normalizes data from simpleDb into schema definition
    *
    * @param SimpleXMLObject $raw A webhook from SimpleDb in SimpleXML.
    * @return array
    */
  private function normalizeWebhook($raw)
  {
    $webhook['id'] = strval($raw->Name->S);
    $webhook['appId'] = $this->config->application->appId;

    foreach($raw->{0} as $key => $value)
    {
	if ($raw->$key->S)
        {
    		$name = (string)$key;
    		$value = (string)$raw->$key->S;
	}
	else
	{
    		$name = (string)$key;
    		$value = (string)$raw->$key;
	}

	if($name == 'permissions')
		$webhook[$name] = (array)explode(',', $value);
	else
		$webhook[$name] = $value;
		
	$webhook[$name] = $value;
    }

    getLogger()->info(print_r($webhook,1));
    return $webhook;

  }

  /** Prepare tags to store in the database
   */
  private function prepareTag($params)
  {
    $bindings = array();
    if(!empty($params['id']))
    {
      $bindings[':id'] = $params['id'];
      $params['id'] = ':id';
    }
    if(!empty($bindings))
    {
      $params['::bindings'] = $bindings;
    }
    return $params;
  }

  /** Prepare user to store in the database
   */
  private function prepareUser($params)
  {
    $ret = array();
    if(isset($params) && is_array($params) && !empty($params))
    {
      foreach($params as $key => $val)
        $ret[$key] = $val;
    }
    return json_encode($ret);
  }

  /**
    * Formats a group to be updated or added to the database.
    * Primarily to properly format members as an array.
    *
    * @param string $id ID of the group.
    * @param array $params Parameters for the group to be normalized.
    * @return array
    */
  private function prepareGroup($id, $params)
  {
    getLogger()->info("Starting prepareGroup");
    getLogger()->info(print_r($params,1));

    $params['Name'] = $id;
    #if(!isset($params['members']))
    #  $params['members'] = array();
    #elseif(!is_array($params['members']))
    #  $params['members'] = (array)explode(',', $params['members']);
    return $params;
  }

  /**
    * Formats a photo to be updated or added to the database.
    * Primarily to properly format tags as an array.
    *
    * @param string $id ID of the photo.
    * @param array $params Parameters for the photo to be normalized.
    * @return array
    */
  private function preparePhoto($id, $params)
  {
    getLogger()->info("Starting preparePhoto");

    $keys = array_keys($params);
    foreach ($keys as $key) {
    	getLogger()->info($key);
    	getLogger()->info($params[$key]);
	if(!isset($params[$key]))
		$params[$key] = array( AmazonDynamoDB::TYPE_STRING => "N/A" );
	elseif(empty($params[$key]))
		$params[$key] = array( AmazonDynamoDB::TYPE_STRING => "N/A" );
	else
		$params[$key] = array( AmazonDynamoDB::TYPE_STRING => (string)$params[$key] );
    }

    $params['id'] = array( AmazonDynamoDB::TYPE_STRING => (string)$id );
    $params['Name'] = array( AmazonDynamoDB::TYPE_STRING => (string)$id );

    #foreach(array('groups','tags') as $val)
    #{
    #  if(!isset($params[$val]))
    #    $params[$val] = array();
    #  elseif(!is_array($params[$val]))
    #    $params[$val] = (array)explode(',', $params[$val]);
    #}
    getLogger()->info("Ending preparePhoto");
    return $params;
  }

}
