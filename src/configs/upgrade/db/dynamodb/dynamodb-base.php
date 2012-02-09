<?php
#$tables = $this->db->list_tables("/^({$this->domainPhoto}|{$this->domainAction}|{$this->domainCredential}|{$this->domainGroup}|{$this->domainTag}|{$this->domainUser}|{$this->domainWebhook})?$/");
$tables = $this->db->list_tables();
#getLogger()->info("Existing tables....");
#getLogger()->info(print_r($tables->body->TableNames->to_array()->getArrayCopy(), 1));
#if(count($tables) == 7)
#  return true;

#$dynamodb = new AmazonDynamoDB();
#$response = $this->db->list_tables();

#$existingTables = array();
#foreach($response->body->TableNames->{0} as $item)
#{
#        $existingTables[] = "$item";
#}

$tablesToCreate = array($this->domainAction, $this->domainCredential, $this->domainGroup, 
  $this->domainPhoto, $this->domainTag, $this->domainUser, $this->domainWebhook);

$queue = new CFBatchRequest();
getLogger()->info(print_r($queue,1));

$this->db->batch($queue)->create_table(array(
	'TableName' => $this->domainAction,
	'KeySchema' => array(
		'HashKeyElement' => array(
			'AttributeName' => 'id',
			'AttributeType' => AmazonDynamoDB::TYPE_STRING
		),
	),
	'ProvisionedThroughput' => array(
		'ReadCapacityUnits' => 10,
		'WriteCapacityUnits' => 5
	)
));
getLogger()->info(sprintf('Queueing request to create table: %s', $this->domainAction));
$responses = $this->db->batch($queue)->send();

$this->db->batch($queue)->create_table(array(
	'TableName' => $this->domainCredential,
	'KeySchema' => array(
		'HashKeyElement' => array(
			'AttributeName' => 'id',
			'AttributeType' => AmazonDynamoDB::TYPE_STRING
		),
	),
	'ProvisionedThroughput' => array(
		'ReadCapacityUnits' => 10,
		'WriteCapacityUnits' => 5
	)
));
getLogger()->info(sprintf('Queueing request to create table: %s', $this->domainCredential));
$responses = $this->db->batch($queue)->send();

$this->db->batch($queue)->create_table(array(
	'TableName' => $this->domainGroup,
	'KeySchema' => array(
		'HashKeyElement' => array(
			'AttributeName' => 'id',
			'AttributeType' => AmazonDynamoDB::TYPE_STRING
		),
	),
	'ProvisionedThroughput' => array(
		'ReadCapacityUnits' => 10,
		'WriteCapacityUnits' => 5
	)
));
getLogger()->info(sprintf('Queueing request to create table: %s', $this->domainGroup));
$responses = $this->db->batch($queue)->send();

$this->db->batch($queue)->create_table(array(
	'TableName' => $this->domainPhoto,
	'KeySchema' => array(
		'HashKeyElement' => array(
			'AttributeName' => 'id',
			'AttributeType' => AmazonDynamoDB::TYPE_STRING
		),
	),
	'ProvisionedThroughput' => array(
		'ReadCapacityUnits' => 10,
		'WriteCapacityUnits' => 5
	)
));
getLogger()->info(sprintf('Queueing request to create table: %s', $this->domainPhoto));
$responses = $this->db->batch($queue)->send();

$this->db->batch($queue)->create_table(array(
	'TableName' => $this->domainTag,
	'KeySchema' => array(
		'HashKeyElement' => array(
			'AttributeName' => 'id',
			'AttributeType' => AmazonDynamoDB::TYPE_STRING
		),
	),
	'ProvisionedThroughput' => array(
		'ReadCapacityUnits' => 10,
		'WriteCapacityUnits' => 5
	)
));
getLogger()->info(sprintf('Queueing request to create table: %s', $this->domainTag));
$responses = $this->db->batch($queue)->send();

$this->db->batch($queue)->create_table(array(
	'TableName' => $this->domainUser,
	'KeySchema' => array(
		'HashKeyElement' => array(
			'AttributeName' => 'id',
			'AttributeType' => AmazonDynamoDB::TYPE_STRING
		),
	),
	'ProvisionedThroughput' => array(
		'ReadCapacityUnits' => 10,
		'WriteCapacityUnits' => 5
	)
));
getLogger()->info(sprintf('Queueing request to create table: %s', $this->domainUser));
$responses = $this->db->batch($queue)->send();

$this->db->batch($queue)->create_table(array(
	'TableName' => $this->domainWebhook,
	'KeySchema' => array(
		'HashKeyElement' => array(
			'AttributeName' => 'id',
			'AttributeType' => AmazonDynamoDB::TYPE_STRING
		),
	),
	'ProvisionedThroughput' => array(
		'ReadCapacityUnits' => 10,
		'WriteCapacityUnits' => 5
	)
));
getLogger()->info(sprintf('Queueing request to create table: %s', $this->domainWebhook));
$responses = $this->db->batch($queue)->send();

#$responses = $this->db->batch($queue)->send();
#getLogger()->info(print_r($responses));
#getLogger()->info(sprintf('Attempting to create %d tables.', count($responses)));

$this->logErrors($responses);
$status = $responses->areOK();
return $status;
