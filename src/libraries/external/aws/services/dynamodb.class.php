<?php
/*
 * Copyright 2010-2012 Amazon.com, Inc. or its affiliates. All Rights Reserved.
 *
 * Licensed under the Apache License, Version 2.0 (the "License").
 * You may not use this file except in compliance with the License.
 * A copy of the License is located at
 *
 *  http://aws.amazon.com/apache2.0
 *
 * or in the "license" file accompanying this file. This file is distributed
 * on an "AS IS" BASIS, WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either
 * express or implied. See the License for the specific language governing
 * permissions and limitations under the License.
 */

/**
 * Amazon DynamoDB is a fast, highly scalable, highly available, cost-effective non-relational
 * database service.
 *
 * Amazon DynamoDB removes traditional scalability limitations on data storage while maintaining
 * low latency and predictable performance.
 *
 * @version 2012.01.16
 * @license See the included NOTICE.md file for complete information.
 * @copyright See the included NOTICE.md file for complete information.
 * @link http://aws.amazon.com/dynamodb/ Amazon DynamoDB
 * @link http://aws.amazon.com/dynamodb/documentation/ Amazon DynamoDB documentation
 */
class AmazonDynamoDB extends CFRuntime
{
	/*%******************************************************************************************%*/
	// CLASS CONSTANTS

	/**
	 * Specify the queue URL for the United States East (Northern Virginia) Region.
	 */
	const REGION_US_E1 = 'dynamodb.us-east-1.amazonaws.com';

	/**
	 * Specify the queue URL for the United States East (Northern Virginia) Region.
	 */
	const REGION_VIRGINIA = self::REGION_US_E1;

	/**
	 * Default service endpoint.
	 */
	const DEFAULT_URL = self::REGION_US_E1;


	/*%******************************************************************************************%*/
	// ACTION CONSTANTS

	/**
	 * Action: Add
	 */
	const ACTION_ADD = 'ADD';

	/**
	 * Action: Delete
	 */
	const ACTION_DELETE = 'DELETE';

	/**
	 * Action: Put
	 */
	const ACTION_PUT = 'PUT';


	/*%******************************************************************************************%*/
	// CONDITION CONSTANTS

	/**
	 * Condition operator: Equal To
	 */
	const CONDITION_EQUAL = 'EQ';

	/**
	 * Condition operator: Not Equal To
	 */
	const CONDITION_NOT_EQUAL = 'NE';

	/**
	 * Condition operator: Less Than
	 */
	const CONDITION_LESS_THAN = 'LT';

	/**
	 * Condition operator: Less Than or Equal To
	 */
	const CONDITION_LESS_THAN_OR_EQUAL = 'LE';

	/**
	 * Condition operator: Greater Than or Equal To
	 */
	const CONDITION_GREATER_THAN = 'GT';

	/**
	 * Condition operator: Greater Than or Equal To
	 */
	const CONDITION_GREATER_THAN_OR_EQUAL = 'GE';

	/**
	 * Condition operator: Null
	 */
	const CONDITION_NULL = 'NULL';

	/**
	 * Condition operator: Not Null
	 */
	const CONDITION_NOT_NULL = 'NOT_NULL';

	/**
	 * Condition operator: Contains
	 */
	const CONDITION_CONTAINS = 'CONTAINS';

	/**
	 * Condition operator: Doesn't Contain
	 */
	const CONDITION_DOESNT_CONTAIN = 'NOT_CONTAINS';

	/**
	 * Condition operator: In
	 */
	const CONDITION_IN = 'IN';

	/**
	 * Condition operator: Between
	 */
	const CONDITION_BETWEEN = 'BETWEEN';

	/**
	 * Condition operator: Begins With
	 */
	const CONDITION_BEGINS_WITH = 'BEGINS_WITH';


	/*%******************************************************************************************%*/
	// RETURN CONSTANTS

	/**
	 * Return value type: NONE
	 */
	const RETURN_NONE = 'NONE';

	/**
	 * Return value type: ALL_OLD
	 */
	const RETURN_ALL_OLD = 'ALL_OLD';

	/**
	 * Return value type: ALL_NEW
	 */
	const RETURN_ALL_NEW = 'ALL_NEW';

	/**
	 * Return value type: UPDATED_OLD
	 */
	const RETURN_UPDATED_OLD = 'UPDATED_OLD';

	/**
	 * Return value type: UPDATED_NEW
	 */
	const RETURN_UPDATED_NEW = 'UPDATED_NEW';


	/*%******************************************************************************************%*/
	// TYPE CONSTANTS

	/**
	 * Content type: string
	 */
	const TYPE_STRING = 'S';

	/**
	 * Content type: number
	 */
	const TYPE_NUMBER = 'N';

	/**
	 * Content type: array of strings
	 */
	const TYPE_ARRAY_OF_STRINGS = 'SS';

	/**
	 * Content type: array of strings
	 */
	const TYPE_ARRAY_OF_NUMBERS = 'NS';


	/*%******************************************************************************************%*/
	// CONSTRUCTOR

	/**
	 * Constructs a new instance of <AmazonDynamoDB>.
	 *
	 * @param array $options (Optional) An associative array of parameters that can have the following keys: <ul>
	 * 	<li><code>certificate_authority</code> - <code>boolean</code> - Optional - Determines which Cerificate Authority file to use. A value of boolean <code>false</code> will use the Certificate Authority file available on the system. A value of boolean <code>true</code> will use the Certificate Authority provided by the SDK. Passing a file system path to a Certificate Authority file (chmodded to <code>0755</code>) will use that. Leave this set to <code>false</code> if you're not sure.</li>
	 * 	<li><code>credentials</code> - <code>string</code> - Optional - The name of the credential set to use for authentication.</li>
	 * 	<li><code>default_cache_config</code> - <code>string</code> - Optional - This option allows a preferred storage type to be configured for long-term caching. This can be changed later using the <set_cache_config()> method. Valid values are: <code>apc</code>, <code>xcache</code>, or a file system path such as <code>./cache</code> or <code>/tmp/cache/</code>.</li>
	 * 	<li><code>key</code> - <code>string</code> - Optional - Your AWS key, or a session key. If blank, the default credential set will be used.</li>
	 * 	<li><code>secret</code> - <code>string</code> - Optional - Your AWS secret key, or a session secret key. If blank, the default credential set will be used.</li>
	 * 	<li><code>token</code> - <code>string</code> - Optional - An AWS session token.</li></ul>
	 * @return void
	 */
	public function __construct(array $options = array())
	{
		$this->api_version = '2011-12-05';
		$this->hostname = self::DEFAULT_URL;
		$this->auth_class = 'AuthV3JSON';
		$this->operation_prefix = 'x-amz-target:DynamoDB_20111205.';

		parent::__construct($options);

		// Default caching mechanism is required
		if (!$this->credentials->default_cache_config)
		{
			// @codeCoverageIgnoreStart
			throw new DynamoDB_Exception('The DynamoDB class requires the "default_cache_config" configuration to be set in the config.inc.php file.');
			// @codeCoverageIgnoreEnd
		}

		$token = new AmazonSTS($options);
		$token->set_cache_config($this->credentials->default_cache_config);
		$response = $token->cache(3600)->get_session_token();

		$this->key = (string) $response->body->GetSessionTokenResult->Credentials->AccessKeyId;
		$this->secret_key = (string) $response->body->GetSessionTokenResult->Credentials->SecretAccessKey;
		$this->auth_token = (string) $response->body->GetSessionTokenResult->Credentials->SessionToken;
	}


	/*%******************************************************************************************%*/
	// SETTERS

	/**
	 * This allows you to explicitly sets the region for the service to use.
	 *
	 * @param string $region (Required) The region to explicitly set. Available options are <REGION_US_E1>.
	 * @return $this A reference to the current instance.
	 */
	public function set_region($region)
	{
		// @codeCoverageIgnoreStart
		$this->set_hostname($region);
		return $this;
		// @codeCoverageIgnoreEnd
	}


	/*%******************************************************************************************%*/
	// SERVICE METHODS

	/**
	 * Retrieves the attributes for multiple items from multiple tables using their primary keys.
	 *
	 * The maximum number of item attributes that can be retrieved for a single operation is 100.
	 * Also, the number of items retrieved is constrained by a 1 MB the size limit. If the response
	 * size limit is exceeded or a partial result is returned due to an internal processing failure,
	 * Amazon DynamoDB returns an <code>UnprocessedKeys</code> value so you can retry the operation
	 * starting with the next item to get.
	 *
	 * Amazon DynamoDB automatically adjusts the number of items returned per page to enforce this
	 * limit. For example, even if you ask to retrieve 100 items, but each individual item is 50k in
	 * size, the system returns 20 items and an appropriate <code>UnprocessedKeys</code> value so you
	 * can get the next page of results. If necessary, your application needs its own logic to
	 * assemble the pages of results into one set.
	 *
	 * @param array $opt (Optional) An associative array of parameters that can have the following keys: <ul>
	 * 	<li><code>RequestItems</code> - <code>array</code> - Required - A map of the table name and corresponding items to get by primary key. While requesting items, each table name can be invoked only once per operation. <ul>
	 * 		<li><code>[table_name]</code> - <code>array</code> - Optional - The name of the table to use for the lookup. <ul>
	 * 			<li><code>Keys</code> - <code>array</code> - Required - <p>The primary key that uniquely identifies each item in a table. A primary key can be a one attribute (hash) primary key or a two attribute (hash-and-range) primary key.</p> <ul>
	 * 				<li><code>x</code> - <code>array</code> - Optional - This represents a simple array index. <ul>
	 * 					<li><code>HashKeyElement</code> - <code>array</code> - Required - A hash key element is treated as the primary key, and can be a string or a number. Single attribute primary keys have one index value. The value can be <code>String</code>, <code>Number</code>, <code>StringSet</code>, <code>NumberSet</code>. <ul>
	 * 						<li><code>x</code> - <code>array</code> - Optional - This represents a simple array index. <ul>
	 * 							<li><code>S</code> - <code>string</code> - Optional - Strings are Unicode with UTF-8 binary encoding. The maximum size is limited by the size of the primary key (1024 bytes as a range part of a key or 2048 bytes as a single part hash key) or the item size (64k).</li>
	 * 							<li><code>N</code> - <code>string</code> - Optional - Numbers are positive or negative exact-value decimals and integers. A number can have up to 38 digits precision and can be between 10^-128 to 10^+126.</li>
	 * 							<li><code>SS</code> - <code>string|array</code> - Optional - A set of strings. Pass a string for a single value, or an indexed array for multiple values.</li>
	 * 							<li><code>NS</code> - <code>string|array</code> - Optional - A set of numbers. Pass a string for a single value, or an indexed array for multiple values.</li>
	 * 						</ul></li>
	 * 					</ul></li>
	 * 					<li><code>RangeKeyElement</code> - <code>array</code> - Optional - A range key element is treated as a secondary key (used in conjunction with the primary key), and can be a string or a number, and is only used for hash-and-range primary keys. The value can be <code>String</code>, <code>Number</code>, <code>StringSet</code>, <code>NumberSet</code>. <ul>
	 * 						<li><code>x</code> - <code>array</code> - Optional - This represents a simple array index. <ul>
	 * 							<li><code>S</code> - <code>string</code> - Optional - Strings are Unicode with UTF-8 binary encoding. The maximum size is limited by the size of the primary key (1024 bytes as a range part of a key or 2048 bytes as a single part hash key) or the item size (64k).</li>
	 * 							<li><code>N</code> - <code>string</code> - Optional - Numbers are positive or negative exact-value decimals and integers. A number can have up to 38 digits precision and can be between 10^-128 to 10^+126.</li>
	 * 							<li><code>SS</code> - <code>string|array</code> - Optional - A set of strings. Pass a string for a single value, or an indexed array for multiple values.</li>
	 * 							<li><code>NS</code> - <code>string|array</code> - Optional - A set of numbers. Pass a string for a single value, or an indexed array for multiple values.</li>
	 * 						</ul></li>
	 * 					</ul></li>
	 * 				</ul></li>
	 * 			</ul></li>
	 * 			<li><code>AttributesToGet</code> - <code>string|array</code> - Optional - List of <code>Attribute</code> names. If attribute names are not specified then all attributes will be returned. If some attributes are not found, they will not appear in the result. Pass a string for a single value, or an indexed array for multiple values.</li>
	 * 		</ul></li>
	 * 	</ul></li>
	 * 	<li><code>curlopts</code> - <code>array</code> - Optional - A set of values to pass directly into <code>curl_setopt()</code>, where the key is a pre-defined <code>CURLOPT_*</code> constant.</li>
	 * 	<li><code>returnCurlHandle</code> - <code>boolean</code> - Optional - A private toggle specifying that the cURL handle be returned rather than actually completing the request. This toggle is useful for manually managed batch requests.</li></ul>
	 * @return CFResponse A <CFResponse> object containing a parsed HTTP response.
	 */
	public function batch_get_item($opt = null)
	{
		if (!$opt) $opt = array();

		$opt = json_encode($opt);
		return $this->authenticate('BatchGetItem', $opt);
	}

	/**
	 * Adds a new table to your account.
	 *
	 * The table name must be unique among those associated with the AWS Account issuing the request,
	 * and the AWS Region that receives the request (e.g. <code>us-east-1</code>).
	 *
	 * The <code>CreateTable</code> operation triggers an asynchronous workflow to begin creating the
	 * table. Amazon DynamoDB immediately returns the state of the table (<code>CREATING</code>) until
	 * the table is in the <code>ACTIVE</code> state. Once the table is in the <code>ACTIVE</code>
	 * state, you can perform data plane operations.
	 *
	 * @param array $opt (Optional) An associative array of parameters that can have the following keys: <ul>
	 * 	<li><code>TableName</code> - <code>string</code> - Required - The name of the table you want to create. Allowed characters are <code>a-z</code>, <code>A-Z</code>, <code>0-9</code>, <code>_</code> (underscore), <code>-</code> (hyphen) and <code>.</code> (period). [Constraints: The value must be between 3 and 255 characters, and must match the following regular expression pattern: <code>[a-zA-Z0-9_.-]+</code>]</li>
	 * 	<li><code>KeySchema</code> - <code>array</code> - Required - The KeySchema identifies the primary key as a one attribute primary key (hash) or a composite two attribute (hash-and-range) primary key. Single attribute primary keys have one index value: a <code>HashKeyElement</code>. A composite hash-and-range primary key contains two attribute values: a <code>HashKeyElement</code> and a <code>RangeKeyElement</code>. <ul>
	 * 		<li><code>x</code> - <code>array</code> - Optional - This represents a simple array index. <ul>
	 * 			<li><code>HashKeyElement</code> - <code>array</code> - Required - A hash key element is treated as the primary key, and can be a string or a number. Single attribute primary keys have one index value. The value can be <code>String</code>, <code>Number</code>, <code>StringSet</code>, <code>NumberSet</code>. <ul>
	 * 				<li><code>x</code> - <code>array</code> - Optional - This represents a simple array index. <ul>
	 * 					<li><code>AttributeName</code> - <code>string</code> - Required - The <code>AttributeName</code> of the <code>KeySchemaElement</code>.</li>
	 * 					<li><code>AttributeType</code> - <code>string</code> - Required - The <code>AttributeType</code> of the <code>KeySchemaElement</code> which can be a <code>String</code> or a <code>Number</code>. [Allowed values: <code>S</code>, <code>N</code>]</li>
	 * 				</ul></li>
	 * 			</ul></li>
	 * 			<li><code>RangeKeyElement</code> - <code>array</code> - Optional - A range key element is treated as a secondary key (used in conjunction with the primary key), and can be a string or a number, and is only used for hash-and-range primary keys. The value can be <code>String</code>, <code>Number</code>, <code>StringSet</code>, <code>NumberSet</code>. <ul>
	 * 				<li><code>x</code> - <code>array</code> - Optional - This represents a simple array index. <ul>
	 * 					<li><code>AttributeName</code> - <code>string</code> - Required - The <code>AttributeName</code> of the <code>KeySchemaElement</code>.</li>
	 * 					<li><code>AttributeType</code> - <code>string</code> - Required - The <code>AttributeType</code> of the <code>KeySchemaElement</code> which can be a <code>String</code> or a <code>Number</code>. [Allowed values: <code>S</code>, <code>N</code>]</li>
	 * 				</ul></li>
	 * 			</ul></li>
	 * 		</ul></li>
	 * 	</ul></li>
	 * 	<li><code>ProvisionedThroughput</code> - <code>array</code> - Required - Provisioned throughput reserves the required read and write resources for your table in terms of <code>ReadCapacityUnits</code> and <code>WriteCapacityUnits</code>. Values for provisioned throughput depend upon your expected read/write rates, item size, and consistency. Provide the expected number of read and write operations, assuming an item size of 1k and strictly consistent reads. For 2k item size, double the value. For 3k, triple the value, etc. Eventually-consistent reads consume half the resources of strictly consistent reads. <ul>
	 * 		<li><code>x</code> - <code>array</code> - Optional - This represents a simple array index. <ul>
	 * 			<li><code>ReadCapacityUnits</code> - <code>long</code> - Required - <code>ReadCapacityUnits</code> are in terms of strictly consistent reads, assuming items of 1k. 2k items require twice the <code>ReadCapacityUnits</code>. Eventually-consistent reads only require half the <code>ReadCapacityUnits</code> of stirctly consistent reads.</li>
	 * 			<li><code>WriteCapacityUnits</code> - <code>long</code> - Required - <code>WriteCapacityUnits</code> are in terms of strictly consistent reads, assuming items of 1k. 2k items require twice the <code>WriteCapacityUnits</code>.</li>
	 * 		</ul></li>
	 * 	</ul></li>
	 * 	<li><code>curlopts</code> - <code>array</code> - Optional - A set of values to pass directly into <code>curl_setopt()</code>, where the key is a pre-defined <code>CURLOPT_*</code> constant.</li>
	 * 	<li><code>returnCurlHandle</code> - <code>boolean</code> - Optional - A private toggle specifying that the cURL handle be returned rather than actually completing the request. This toggle is useful for manually managed batch requests.</li></ul>
	 * @return CFResponse A <CFResponse> object containing a parsed HTTP response.
	 */
	public function create_table($opt = null)
	{
		if (!$opt) $opt = array();

		$opt = json_encode($opt);
		return $this->authenticate('CreateTable', $opt);
	}

	/**
	 * Deletes a single item in a table by primary key.
	 *
	 * You can perform a conditional delete operation that deletes the item if it exists, or if it has
	 * an expected attribute value.
	 *
	 * @param array $opt (Optional) An associative array of parameters that can have the following keys: <ul>
	 * 	<li><code>TableName</code> - <code>string</code> - Required - The name of the table in which you want to delete an item. Allowed characters are <code>a-z</code>, <code>A-Z</code>, <code>0-9</code>, <code>_</code> (underscore), <code>-</code> (hyphen) and <code>.</code> (period). [Constraints: The value must be between 3 and 255 characters, and must match the following regular expression pattern: <code>[a-zA-Z0-9_.-]+</code>]</li>
	 * 	<li><code>Key</code> - <code>array</code> - Required - The primary key that uniquely identifies each item in a table. A primary key can be a one attribute (hash) primary key or a two attribute (hash-and-range) primary key. <ul>
	 * 		<li><code>x</code> - <code>array</code> - Optional - This represents a simple array index. <ul>
	 * 			<li><code>HashKeyElement</code> - <code>array</code> - Required - A hash key element is treated as the primary key, and can be a string or a number. Single attribute primary keys have one index value. The value can be <code>String</code>, <code>Number</code>, <code>StringSet</code>, <code>NumberSet</code>. <ul>
	 * 				<li><code>x</code> - <code>array</code> - Optional - This represents a simple array index. <ul>
	 * 					<li><code>S</code> - <code>string</code> - Optional - Strings are Unicode with UTF-8 binary encoding. The maximum size is limited by the size of the primary key (1024 bytes as a range part of a key or 2048 bytes as a single part hash key) or the item size (64k).</li>
	 * 					<li><code>N</code> - <code>string</code> - Optional - Numbers are positive or negative exact-value decimals and integers. A number can have up to 38 digits precision and can be between 10^-128 to 10^+126.</li>
	 * 					<li><code>SS</code> - <code>string|array</code> - Optional - A set of strings. Pass a string for a single value, or an indexed array for multiple values.</li>
	 * 					<li><code>NS</code> - <code>string|array</code> - Optional - A set of numbers. Pass a string for a single value, or an indexed array for multiple values.</li>
	 * 				</ul></li>
	 * 			</ul></li>
	 * 			<li><code>RangeKeyElement</code> - <code>array</code> - Optional - A range key element is treated as a secondary key (used in conjunction with the primary key), and can be a string or a number, and is only used for hash-and-range primary keys. The value can be <code>String</code>, <code>Number</code>, <code>StringSet</code>, <code>NumberSet</code>. <ul>
	 * 				<li><code>x</code> - <code>array</code> - Optional - This represents a simple array index. <ul>
	 * 					<li><code>S</code> - <code>string</code> - Optional - Strings are Unicode with UTF-8 binary encoding. The maximum size is limited by the size of the primary key (1024 bytes as a range part of a key or 2048 bytes as a single part hash key) or the item size (64k).</li>
	 * 					<li><code>N</code> - <code>string</code> - Optional - Numbers are positive or negative exact-value decimals and integers. A number can have up to 38 digits precision and can be between 10^-128 to 10^+126.</li>
	 * 					<li><code>SS</code> - <code>string|array</code> - Optional - A set of strings. Pass a string for a single value, or an indexed array for multiple values.</li>
	 * 					<li><code>NS</code> - <code>string|array</code> - Optional - A set of numbers. Pass a string for a single value, or an indexed array for multiple values.</li>
	 * 				</ul></li>
	 * 			</ul></li>
	 * 		</ul></li>
	 * 	</ul></li>
	 * 	<li><code>Expected</code> - <code>array</code> - Optional - Designates an attribute for a conditional modification. The <code>Expected</code> parameter allows you to provide an attribute name, and whether or not Amazon DynamoDB should check to see if the attribute has a particular value before modifying it. <ul>
	 * 		<li><code>x</code> - <code>array</code> - Optional - This represents a simple array index. <ul>
	 * 			<li><code>key</code> - <code>string</code> - Optional - </li>
	 * 			<li><code>value</code> - <code>array</code> - Optional - Allows you to provide an attribute name, and whether or not Amazon DynamoDB should check to see if the attribute value already exists; or if the attribute value exists and has a particular value before changing it. <ul>
	 * 				<li><code>x</code> - <code>array</code> - Optional - This represents a simple array index. <ul>
	 * 					<li><code>Value</code> - <code>array</code> - Optional - Specify whether or not a value already exists and has a specific content for the attribute name-value pair. <ul>
	 * 						<li><code>x</code> - <code>array</code> - Optional - This represents a simple array index. <ul>
	 * 							<li><code>S</code> - <code>string</code> - Optional - Strings are Unicode with UTF-8 binary encoding. The maximum size is limited by the size of the primary key (1024 bytes as a range part of a key or 2048 bytes as a single part hash key) or the item size (64k).</li>
	 * 							<li><code>N</code> - <code>string</code> - Optional - Numbers are positive or negative exact-value decimals and integers. A number can have up to 38 digits precision and can be between 10^-128 to 10^+126.</li>
	 * 							<li><code>SS</code> - <code>string|array</code> - Optional - A set of strings. Pass a string for a single value, or an indexed array for multiple values.</li>
	 * 							<li><code>NS</code> - <code>string|array</code> - Optional - A set of numbers. Pass a string for a single value, or an indexed array for multiple values.</li>
	 * 						</ul></li>
	 * 					</ul></li>
	 * 					<li><code>Exists</code> - <code>boolean</code> - Optional - Specify whether or not a value already exists for the attribute name-value pair.</li>
	 * 				</ul></li>
	 * 			</ul></li>
	 * 		</ul></li>
	 * 	</ul></li>
	 * 	<li><code>ReturnValues</code> - <code>string</code> - Optional - Use this parameter if you want to get the attribute name-value pairs before or after they are modified. For <code>PUT</code> operations, the possible parameter values are <code>NONE</code> (default) or <code>ALL_OLD</code>. For update operations, the possible parameter values are <code>NONE</code> (default) or <code>ALL_OLD</code>, <code>UPDATED_OLD</code>, <code>ALL_NEW</code> or <code>UPDATED_NEW</code>.<ul><li> <code>NONE</code>: Nothing is returned.</li><li> <code>ALL_OLD</code>: Returns the attributes of the item as they were before the operation.</li><li> <code>UPDATED_OLD</code>: Returns the values of the updated attributes, only, as they were before the operation.</li><li> <code>ALL_NEW</code>: Returns all the attributes and their new values after the operation.</li><li> <code>UPDATED_NEW</code>: Returns the values of the updated attributes, only, as they are after the operation.</li></ul> [Allowed values: <code>NONE</code>, <code>ALL_OLD</code>, <code>UPDATED_OLD</code>, <code>ALL_NEW</code>, <code>UPDATED_NEW</code>]</li>
	 * 	<li><code>curlopts</code> - <code>array</code> - Optional - A set of values to pass directly into <code>curl_setopt()</code>, where the key is a pre-defined <code>CURLOPT_*</code> constant.</li>
	 * 	<li><code>returnCurlHandle</code> - <code>boolean</code> - Optional - A private toggle specifying that the cURL handle be returned rather than actually completing the request. This toggle is useful for manually managed batch requests.</li></ul>
	 * @return CFResponse A <CFResponse> object containing a parsed HTTP response.
	 */
	public function delete_item($opt = null)
	{
		if (!$opt) $opt = array();

		$opt = json_encode($opt);
		return $this->authenticate('DeleteItem', $opt);
	}

	/**
	 * Deletes a table and all of its items.
	 *
	 * If the table is in the <code>ACTIVE</code> state, you can delete it. If a table is in
	 * <code>CREATING</code> or <code>UPDATING</code> states then <code>DeleteTable</code> returns a
	 * <code>ResourceInUseException</code>. If the specified table does not exist, Amazon DynamoDB
	 * returns a <code>ResourceNotFoundException</code>.
	 *
	 * @param array $opt (Optional) An associative array of parameters that can have the following keys: <ul>
	 * 	<li><code>TableName</code> - <code>string</code> - Required - The name of the table you want to delete. Allowed characters are <code>a-z</code>, <code>A-Z</code>, <code>0-9</code>, <code>_</code> (underscore), <code>-</code> (hyphen) and <code>.</code> (period). [Constraints: The value must be between 3 and 255 characters, and must match the following regular expression pattern: <code>[a-zA-Z0-9_.-]+</code>]</li>
	 * 	<li><code>curlopts</code> - <code>array</code> - Optional - A set of values to pass directly into <code>curl_setopt()</code>, where the key is a pre-defined <code>CURLOPT_*</code> constant.</li>
	 * 	<li><code>returnCurlHandle</code> - <code>boolean</code> - Optional - A private toggle specifying that the cURL handle be returned rather than actually completing the request. This toggle is useful for manually managed batch requests.</li></ul>
	 * @return CFResponse A <CFResponse> object containing a parsed HTTP response.
	 */
	public function delete_table($opt = null)
	{
		if (!$opt) $opt = array();

		$opt = json_encode($opt);
		return $this->authenticate('DeleteTable', $opt);
	}

	/**
	 * Retrieves information about the table, including the current status of the table, the primary
	 * key schema and when the table was created.
	 *
	 * If the table does not exist, the server returns a <code>ResourceNotFoundException</code>.
	 *
	 * @param array $opt (Optional) An associative array of parameters that can have the following keys: <ul>
	 * 	<li><code>TableName</code> - <code>string</code> - Required - The name of the table you want to describe. Allowed characters are <code>a-z</code>, <code>A-Z</code>, <code>0-9</code>, <code>_</code> (underscore), <code>-</code> (hyphen) and <code>.</code> (period). [Constraints: The value must be between 3 and 255 characters, and must match the following regular expression pattern: <code>[a-zA-Z0-9_.-]+</code>]</li>
	 * 	<li><code>curlopts</code> - <code>array</code> - Optional - A set of values to pass directly into <code>curl_setopt()</code>, where the key is a pre-defined <code>CURLOPT_*</code> constant.</li>
	 * 	<li><code>returnCurlHandle</code> - <code>boolean</code> - Optional - A private toggle specifying that the cURL handle be returned rather than actually completing the request. This toggle is useful for manually managed batch requests.</li></ul>
	 * @return CFResponse A <CFResponse> object containing a parsed HTTP response.
	 */
	public function describe_table($opt = null)
	{
		if (!$opt) $opt = array();

		$opt = json_encode($opt);
		return $this->authenticate('DescribeTable', $opt);
	}

	/**
	 * Retrieves a set of Attributes for an item that matches the primary key.
	 *
	 * The <code>GetItem</code> operation provides an eventually-consistent read by default. If
	 * eventually-consistent reads are not acceptable for your application, use
	 * <code>ConsistentRead</code>. Although this operation might take longer than a standard read, it
	 * always returns the last updated value.
	 *
	 * @param array $opt (Optional) An associative array of parameters that can have the following keys: <ul>
	 * 	<li><code>TableName</code> - <code>string</code> - Required - The name of the table in which you want to get an item. Allowed characters are <code>a-z</code>, <code>A-Z</code>, <code>0-9</code>, <code>_</code> (underscore), <code>-</code> (hyphen) and <code>.</code> (period). [Constraints: The value must be between 3 and 255 characters, and must match the following regular expression pattern: <code>[a-zA-Z0-9_.-]+</code>]</li>
	 * 	<li><code>Key</code> - <code>array</code> - Required - The primary key that uniquely identifies each item in a table. A primary key can be a one attribute (hash) primary key or a two attribute (hash-and-range) primary key. <ul>
	 * 		<li><code>x</code> - <code>array</code> - Optional - This represents a simple array index. <ul>
	 * 			<li><code>HashKeyElement</code> - <code>array</code> - Required - A hash key element is treated as the primary key, and can be a string or a number. Single attribute primary keys have one index value. The value can be <code>String</code>, <code>Number</code>, <code>StringSet</code>, <code>NumberSet</code>. <ul>
	 * 				<li><code>x</code> - <code>array</code> - Optional - This represents a simple array index. <ul>
	 * 					<li><code>S</code> - <code>string</code> - Optional - Strings are Unicode with UTF-8 binary encoding. The maximum size is limited by the size of the primary key (1024 bytes as a range part of a key or 2048 bytes as a single part hash key) or the item size (64k).</li>
	 * 					<li><code>N</code> - <code>string</code> - Optional - Numbers are positive or negative exact-value decimals and integers. A number can have up to 38 digits precision and can be between 10^-128 to 10^+126.</li>
	 * 					<li><code>SS</code> - <code>string|array</code> - Optional - A set of strings. Pass a string for a single value, or an indexed array for multiple values.</li>
	 * 					<li><code>NS</code> - <code>string|array</code> - Optional - A set of numbers. Pass a string for a single value, or an indexed array for multiple values.</li>
	 * 				</ul></li>
	 * 			</ul></li>
	 * 			<li><code>RangeKeyElement</code> - <code>array</code> - Optional - A range key element is treated as a secondary key (used in conjunction with the primary key), and can be a string or a number, and is only used for hash-and-range primary keys. The value can be <code>String</code>, <code>Number</code>, <code>StringSet</code>, <code>NumberSet</code>. <ul>
	 * 				<li><code>x</code> - <code>array</code> - Optional - This represents a simple array index. <ul>
	 * 					<li><code>S</code> - <code>string</code> - Optional - Strings are Unicode with UTF-8 binary encoding. The maximum size is limited by the size of the primary key (1024 bytes as a range part of a key or 2048 bytes as a single part hash key) or the item size (64k).</li>
	 * 					<li><code>N</code> - <code>string</code> - Optional - Numbers are positive or negative exact-value decimals and integers. A number can have up to 38 digits precision and can be between 10^-128 to 10^+126.</li>
	 * 					<li><code>SS</code> - <code>string|array</code> - Optional - A set of strings. Pass a string for a single value, or an indexed array for multiple values.</li>
	 * 					<li><code>NS</code> - <code>string|array</code> - Optional - A set of numbers. Pass a string for a single value, or an indexed array for multiple values.</li>
	 * 				</ul></li>
	 * 			</ul></li>
	 * 		</ul></li>
	 * 	</ul></li>
	 * 	<li><code>AttributesToGet</code> - <code>string|array</code> - Optional - List of <code>Attribute</code> names. If attribute names are not specified then all attributes will be returned. If some attributes are not found, they will not appear in the result. Pass a string for a single value, or an indexed array for multiple values.</li>
	 * 	<li><code>ConsistentRead</code> - <code>boolean</code> - Optional - If set to <code>true</code>, then a consistent read is issued. Otherwise eventually-consistent is used.</li>
	 * 	<li><code>curlopts</code> - <code>array</code> - Optional - A set of values to pass directly into <code>curl_setopt()</code>, where the key is a pre-defined <code>CURLOPT_*</code> constant.</li>
	 * 	<li><code>returnCurlHandle</code> - <code>boolean</code> - Optional - A private toggle specifying that the cURL handle be returned rather than actually completing the request. This toggle is useful for manually managed batch requests.</li></ul>
	 * @return CFResponse A <CFResponse> object containing a parsed HTTP response.
	 */
	public function get_item($opt = null)
	{
		if (!$opt) $opt = array();

		// List (non-map)
		if (isset($opt['AttributesToGet']))
		{
			$opt['AttributesToGet'] = (is_array($opt['AttributesToGet']) ? $opt['AttributesToGet'] : array($opt['AttributesToGet']));
		}

		$opt = json_encode($opt);
		return $this->authenticate('GetItem', $opt);
	}

	/**
	 * Retrieves a paginated list of table names created by the AWS Account of the caller in the AWS
	 * Region (e.g. <code>us-east-1</code>).
	 *
	 * @param array $opt (Optional) An associative array of parameters that can have the following keys: <ul>
	 * 	<li><code>ExclusiveStartTableName</code> - <code>string</code> - Optional - The name of the table that starts the list. If you already ran a <code>ListTables</code> operation and received a <code>LastEvaluatedTableName</code> value in the response, use that value here to continue the list. [Constraints: The value must be between 3 and 255 characters, and must match the following regular expression pattern: <code>[a-zA-Z0-9_.-]+</code>]</li>
	 * 	<li><code>Limit</code> - <code>integer</code> - Optional - A number of maximum table names to return.</li>
	 * 	<li><code>curlopts</code> - <code>array</code> - Optional - A set of values to pass directly into <code>curl_setopt()</code>, where the key is a pre-defined <code>CURLOPT_*</code> constant.</li>
	 * 	<li><code>returnCurlHandle</code> - <code>boolean</code> - Optional - A private toggle specifying that the cURL handle be returned rather than actually completing the request. This toggle is useful for manually managed batch requests.</li></ul>
	 * @return CFResponse A <CFResponse> object containing a parsed HTTP response.
	 */
	public function list_tables($opt = null)
	{
		if (!$opt) $opt = array();

		$opt = json_encode($opt);
		return $this->authenticate('ListTables', $opt);
	}

	/**
	 * Creates a new item, or replaces an old item with a new item (including all the attributes).
	 *
	 * If an item already exists in the specified table with the same primary key, the new item
	 * completely replaces the existing item. You can perform a conditional put (insert a new item if
	 * one with the specified primary key doesn't exist), or replace an existing item if it has
	 * certain attribute values.
	 *
	 * @param array $opt (Optional) An associative array of parameters that can have the following keys: <ul>
	 * 	<li><code>TableName</code> - <code>string</code> - Required - The name of the table in which you want to put an item. Allowed characters are <code>a-z</code>, <code>A-Z</code>, <code>0-9</code>, <code>_</code> (underscore), <code>-</code> (hyphen) and <code>.</code> (period). [Constraints: The value must be between 3 and 255 characters, and must match the following regular expression pattern: <code>[a-zA-Z0-9_.-]+</code>]</li>
	 * 	<li><code>Item</code> - <code>array</code> - Required - A map of the attributes for the item, and must include the primary key values that define the item. Other attribute name-value pairs can be provided for the item. <ul>
	 * 		<li><code>[attribute_name]</code> - <code>array</code> - Optional - The name of the attribute to use for the lookup. <ul>
	 * 			<li><code>x</code> - <code>array</code> - Optional - This represents a simple array index. <ul>
	 * 				<li><code>S</code> - <code>string</code> - Optional - Strings are Unicode with UTF-8 binary encoding. The maximum size is limited by the size of the primary key (1024 bytes as a range part of a key or 2048 bytes as a single part hash key) or the item size (64k).</li>
	 * 				<li><code>N</code> - <code>string</code> - Optional - Numbers are positive or negative exact-value decimals and integers. A number can have up to 38 digits precision and can be between 10^-128 to 10^+126.</li>
	 * 				<li><code>SS</code> - <code>string|array</code> - Optional - A set of strings. Pass a string for a single value, or an indexed array for multiple values.</li>
	 * 				<li><code>NS</code> - <code>string|array</code> - Optional - A set of numbers. Pass a string for a single value, or an indexed array for multiple values.</li>
	 * 			</ul></li>
	 * 		</ul></li>
	 * 	</ul></li>
	 * 	<li><code>Expected</code> - <code>array</code> - Optional - Designates an attribute for a conditional modification. The <code>Expected</code> parameter allows you to provide an attribute name, and whether or not Amazon DynamoDB should check to see if the attribute has a particular value before modifying it. <ul>
	 * 		<li><code>[attribute_name]</code> - <code>array</code> - Optional - Allows you to provide an attribute name, and whether or not Amazon DynamoDB should check to see if the attribute value already exists; or if the attribute value exists and has a particular value before changing it. <ul>
	 * 			<li><code>x</code> - <code>array</code> - Optional - This represents a simple array index. <ul>
	 * 				<li><code>Value</code> - <code>array</code> - Optional - Specify whether or not a value already exists and has a specific content for the attribute name-value pair. <ul>
	 * 					<li><code>x</code> - <code>array</code> - Optional - This represents a simple array index. <ul>
	 * 						<li><code>S</code> - <code>string</code> - Optional - Strings are Unicode with UTF-8 binary encoding. The maximum size is limited by the size of the primary key (1024 bytes as a range part of a key or 2048 bytes as a single part hash key) or the item size (64k).</li>
	 * 						<li><code>N</code> - <code>string</code> - Optional - Numbers are positive or negative exact-value decimals and integers. A number can have up to 38 digits precision and can be between 10^-128 to 10^+126.</li>
	 * 						<li><code>SS</code> - <code>string|array</code> - Optional - A set of strings. Pass a string for a single value, or an indexed array for multiple values.</li>
	 * 						<li><code>NS</code> - <code>string|array</code> - Optional - A set of numbers. Pass a string for a single value, or an indexed array for multiple values.</li>
	 * 					</ul></li>
	 * 				</ul></li>
	 * 				<li><code>Exists</code> - <code>boolean</code> - Optional - Specify whether or not a value already exists for the attribute name-value pair.</li>
	 * 			</ul></li>
	 * 		</ul></li>
	 * 	</ul></li>
	 * 	<li><code>ReturnValues</code> - <code>string</code> - Optional - Use this parameter if you want to get the attribute name-value pairs before or after they are modified. For <code>PUT</code> operations, the possible parameter values are <code>NONE</code> (default) or <code>ALL_OLD</code>. For update operations, the possible parameter values are <code>NONE</code> (default) or <code>ALL_OLD</code>, <code>UPDATED_OLD</code>, <code>ALL_NEW</code> or <code>UPDATED_NEW</code>.<ul><li> <code>NONE</code>: Nothing is returned.</li><li> <code>ALL_OLD</code>: Returns the attributes of the item as they were before the operation.</li><li> <code>UPDATED_OLD</code>: Returns the values of the updated attributes, only, as they were before the operation.</li><li> <code>ALL_NEW</code>: Returns all the attributes and their new values after the operation.</li><li> <code>UPDATED_NEW</code>: Returns the values of the updated attributes, only, as they are after the operation.</li></ul> [Allowed values: <code>NONE</code>, <code>ALL_OLD</code>, <code>UPDATED_OLD</code>, <code>ALL_NEW</code>, <code>UPDATED_NEW</code>]</li>
	 * 	<li><code>curlopts</code> - <code>array</code> - Optional - A set of values to pass directly into <code>curl_setopt()</code>, where the key is a pre-defined <code>CURLOPT_*</code> constant.</li>
	 * 	<li><code>returnCurlHandle</code> - <code>boolean</code> - Optional - A private toggle specifying that the cURL handle be returned rather than actually completing the request. This toggle is useful for manually managed batch requests.</li></ul>
	 * @return CFResponse A <CFResponse> object containing a parsed HTTP response.
	 */
	public function put_item($opt = null)
	{
		if (!$opt) $opt = array();

		$opt = json_encode($opt);
		return $this->authenticate('PutItem', $opt);
	}

	/**
	 * Gets the values of one or more items and its attributes by primary key (composite primary key,
	 * only).
	 *
	 * Narrow the scope of the query using comparison operators on the <code>RangeKeyValue</code> of
	 * the composite key. Use the <code>ScanIndexForward</code> parameter to get results in forward or
	 * reverse order by range key.
	 *
	 * @param array $opt (Optional) An associative array of parameters that can have the following keys: <ul>
	 * 	<li><code>TableName</code> - <code>string</code> - Required - The name of the table in which you want to query. Allowed characters are <code>a-z</code>, <code>A-Z</code>, <code>0-9</code>, <code>_</code> (underscore), <code>-</code> (hyphen) and <code>.</code> (period). [Constraints: The value must be between 3 and 255 characters, and must match the following regular expression pattern: <code>[a-zA-Z0-9_.-]+</code>]</li>
	 * 	<li><code>AttributesToGet</code> - <code>string|array</code> - Optional - List of <code>Attribute</code> names. If attribute names are not specified then all attributes will be returned. If some attributes are not found, they will not appear in the result. Pass a string for a single value, or an indexed array for multiple values.</li>
	 * 	<li><code>Limit</code> - <code>integer</code> - Optional - The maximum number of items to return. If Amazon DynamoDB hits this limit while querying the table, it stops the query and returns the matching values up to the limit, and a <code>LastEvaluatedKey</code> to apply in a subsequent operation to continue the query. Also, if the result set size exceeds 1MB before Amazon DynamoDB hits this limit, it stops the query and returns the matching values, and a <code>LastEvaluatedKey</code> to apply in a subsequent operation to continue the query.</li>
	 * 	<li><code>ConsistentRead</code> - <code>boolean</code> - Optional - If set to <code>true</code>, then a consistent read is issued. Otherwise eventually-consistent is used.</li>
	 * 	<li><code>Count</code> - <code>boolean</code> - Optional - If set to <code>true</code>, Amazon DynamoDB returns a total number of items that match the query parameters, instead of a list of the matching items and their attributes. Do not set <code>Count</code> to true while providing a list of <code>AttributesToGet</code>, otherwise Amazon DynamoDB returns a validation error.</li>
	 * 	<li><code>HashKeyValue</code> - <code>array</code> - Required - Attribute value of the hash component of the composite primary key. <ul>
	 * 		<li><code>x</code> - <code>array</code> - Optional - This represents a simple array index. <ul>
	 * 			<li><code>S</code> - <code>string</code> - Optional - Strings are Unicode with UTF-8 binary encoding. The maximum size is limited by the size of the primary key (1024 bytes as a range part of a key or 2048 bytes as a single part hash key) or the item size (64k).</li>
	 * 			<li><code>N</code> - <code>string</code> - Optional - Numbers are positive or negative exact-value decimals and integers. A number can have up to 38 digits precision and can be between 10^-128 to 10^+126.</li>
	 * 			<li><code>SS</code> - <code>string|array</code> - Optional - A set of strings. Pass a string for a single value, or an indexed array for multiple values.</li>
	 * 			<li><code>NS</code> - <code>string|array</code> - Optional - A set of numbers. Pass a string for a single value, or an indexed array for multiple values.</li>
	 * 		</ul></li>
	 * 	</ul></li>
	 * 	<li><code>RangeKeyCondition</code> - <code>array</code> - Optional - A container for the attribute values and comparison operators to use for the query. <ul>
	 * 		<li><code>x</code> - <code>array</code> - Optional - This represents a simple array index. <ul>
	 * 			<li><code>AttributeValueList</code> - <code>array</code> - Optional - A list of attribute values to be used with a comparison operator for a scan or query operation. For comparisons that require more than one value, such as a <code>BETWEEN</code> comparison, the AttributeValueList contains two attribute values and the comparison operator. <ul>
	 * 				<li><code>x</code> - <code>array</code> - Optional - This represents a simple array index. <ul>
	 * 					<li><code>S</code> - <code>string</code> - Optional - Strings are Unicode with UTF-8 binary encoding. The maximum size is limited by the size of the primary key (1024 bytes as a range part of a key or 2048 bytes as a single part hash key) or the item size (64k).</li>
	 * 					<li><code>N</code> - <code>string</code> - Optional - Numbers are positive or negative exact-value decimals and integers. A number can have up to 38 digits precision and can be between 10^-128 to 10^+126.</li>
	 * 					<li><code>SS</code> - <code>string|array</code> - Optional - A set of strings. Pass a string for a single value, or an indexed array for multiple values.</li>
	 * 					<li><code>NS</code> - <code>string|array</code> - Optional - A set of numbers. Pass a string for a single value, or an indexed array for multiple values.</li>
	 * 				</ul></li>
	 * 			</ul></li>
	 * 			<li><code>ComparisonOperator</code> - <code>string</code> - Required - A comparison operator is an enumeration of several operations:<ul><li> <code>EQ</code> for <em>equal</em> </li><li> <code>NE</code> for <em>not equal</em> </li><li> <code>IN</code> checks for exact matches</li><li> <code>LE</code> for <em>less than or equal to</em> </li><li> <code>LT</code> for <em>less than</em> </li><li> <code>GE</code> for <em>greater than or equal to</em> </li><li> <code>GT</code> for <em>greater than</em> </li><li> <code>BETWEEN</code> for <em>between</em> </li><li> <code>NOT_NULL</code> for <em>exists</em> </li><li> <code>NULL</code> for <em>not exists</em> </li><li> <code>CONTAINS</code> for substring or value in a set</li><li> <code>NOT_CONTAINS</code> for absence of a substring or absence of a value in a set</li><li> <code>BEGINS_WITH</code> for a substring prefix.</li></ul> [Allowed values: <code>EQ</code>, <code>NE</code>, <code>IN</code>, <code>LE</code>, <code>LT</code>, <code>GE</code>, <code>GT</code>, <code>BETWEEN</code>, <code>NOT_NULL</code>, <code>NULL</code>, <code>CONTAINS</code>, <code>NOT_CONTAINS</code>, <code>BEGINS_WITH</code>]</li>
	 * 		</ul></li>
	 * 	</ul></li>
	 * 	<li><code>ScanIndexForward</code> - <code>boolean</code> - Optional - Specifies forward or backward traversal of the index. Amazon DynamoDB returns results reflecting the requested order, determined by the range key. The default value is <code>true</code> (forward).</li>
	 * 	<li><code>ExclusiveStartKey</code> - <code>array</code> - Optional - Primary key of the item from which to continue an earlier query. An earlier query might provide this value as the <code>LastEvaluatedKey</code> if that query operation was interrupted before completing the query; either because of the result set size or the <code>Limit</code> parameter. The <code>LastEvaluatedKey</code> can be passed back in a new query request to continue the operation from that point. <ul>
	 * 		<li><code>x</code> - <code>array</code> - Optional - This represents a simple array index. <ul>
	 * 			<li><code>HashKeyElement</code> - <code>array</code> - Required - A hash key element is treated as the primary key, and can be a string or a number. Single attribute primary keys have one index value. The value can be <code>String</code>, <code>Number</code>, <code>StringSet</code>, <code>NumberSet</code>. <ul>
	 * 				<li><code>x</code> - <code>array</code> - Optional - This represents a simple array index. <ul>
	 * 					<li><code>S</code> - <code>string</code> - Optional - Strings are Unicode with UTF-8 binary encoding. The maximum size is limited by the size of the primary key (1024 bytes as a range part of a key or 2048 bytes as a single part hash key) or the item size (64k).</li>
	 * 					<li><code>N</code> - <code>string</code> - Optional - Numbers are positive or negative exact-value decimals and integers. A number can have up to 38 digits precision and can be between 10^-128 to 10^+126.</li>
	 * 					<li><code>SS</code> - <code>string|array</code> - Optional - A set of strings. Pass a string for a single value, or an indexed array for multiple values.</li>
	 * 					<li><code>NS</code> - <code>string|array</code> - Optional - A set of numbers. Pass a string for a single value, or an indexed array for multiple values.</li>
	 * 				</ul></li>
	 * 			</ul></li>
	 * 			<li><code>RangeKeyElement</code> - <code>array</code> - Optional - A range key element is treated as a secondary key (used in conjunction with the primary key), and can be a string or a number, and is only used for hash-and-range primary keys. The value can be <code>String</code>, <code>Number</code>, <code>StringSet</code>, <code>NumberSet</code>. <ul>
	 * 				<li><code>x</code> - <code>array</code> - Optional - This represents a simple array index. <ul>
	 * 					<li><code>S</code> - <code>string</code> - Optional - Strings are Unicode with UTF-8 binary encoding. The maximum size is limited by the size of the primary key (1024 bytes as a range part of a key or 2048 bytes as a single part hash key) or the item size (64k).</li>
	 * 					<li><code>N</code> - <code>string</code> - Optional - Numbers are positive or negative exact-value decimals and integers. A number can have up to 38 digits precision and can be between 10^-128 to 10^+126.</li>
	 * 					<li><code>SS</code> - <code>string|array</code> - Optional - A set of strings. Pass a string for a single value, or an indexed array for multiple values.</li>
	 * 					<li><code>NS</code> - <code>string|array</code> - Optional - A set of numbers. Pass a string for a single value, or an indexed array for multiple values.</li>
	 * 				</ul></li>
	 * 			</ul></li>
	 * 		</ul></li>
	 * 	</ul></li>
	 * 	<li><code>curlopts</code> - <code>array</code> - Optional - A set of values to pass directly into <code>curl_setopt()</code>, where the key is a pre-defined <code>CURLOPT_*</code> constant.</li>
	 * 	<li><code>returnCurlHandle</code> - <code>boolean</code> - Optional - A private toggle specifying that the cURL handle be returned rather than actually completing the request. This toggle is useful for manually managed batch requests.</li></ul>
	 * @return CFResponse A <CFResponse> object containing a parsed HTTP response.
	 */
	public function query($opt = null)
	{
		if (!$opt) $opt = array();

		// List (non-map)
		if (isset($opt['AttributesToGet']))
		{
			$opt['AttributesToGet'] = (is_array($opt['AttributesToGet']) ? $opt['AttributesToGet'] : array($opt['AttributesToGet']));
		}

		$opt = json_encode($opt);
		return $this->authenticate('Query', $opt);
	}

	/**
	 * Retrieves one or more items and its attributes by performing a full scan of a table.
	 *
	 * Limit the returned results by specifying a filter.
	 *
	 * @param array $opt (Optional) An associative array of parameters that can have the following keys: <ul>
	 * 	<li><code>TableName</code> - <code>string</code> - Required - The name of the table in which you want to scan. Allowed characters are <code>a-z</code>, <code>A-Z</code>, <code>0-9</code>, <code>_</code> (underscore), <code>-</code> (hyphen) and <code>.</code> (period). [Constraints: The value must be between 3 and 255 characters, and must match the following regular expression pattern: <code>[a-zA-Z0-9_.-]+</code>]</li>
	 * 	<li><code>AttributesToGet</code> - <code>string|array</code> - Optional - List of <code>Attribute</code> names. If attribute names are not specified then all attributes will be returned. If some attributes are not found, they will not appear in the result. Pass a string for a single value, or an indexed array for multiple values.</li>
	 * 	<li><code>Limit</code> - <code>integer</code> - Optional - The maximum number of items to return. If Amazon DynamoDB hits this limit while scanning the table, it stops the scan and returns the matching values up to the limit, and a <code>LastEvaluatedKey</code> to apply in a subsequent operation to continue the scan. Also, if the scanned data set size exceeds 1 MB before Amazon DynamoDB hits this limit, it stops the scan and returns the matching values up to the limit, and a <code>LastEvaluatedKey</code> to apply in a subsequent operation to continue the scan.</li>
	 * 	<li><code>Count</code> - <code>boolean</code> - Optional - If set to true, Amazon DynamoDB returns a total number of items for the <code>Scan</code> operation, even if the operation has no matching items for the assigned filter. Do not set <code>Count</code> to true while providing a list of <code>AttributesToGet</code>, otherwise Amazon DynamoDB returns a validation error.</li>
	 * 	<li><code>ScanFilter</code> - <code>array</code> - Optional - Evaluates the scan results and returns only the desired values. <ul>
	 * 		<li><code>[attribute_name]</code> - <code>array</code> - Optional - The name of the attribute to use for the lookup. <ul>
	 * 			<li><code>AttributeValueList</code> - <code>array</code> - Optional - A list of attribute values to be used with a comparison operator for a scan or query operation. For comparisons that require more than one value, such as a <code>BETWEEN</code> comparison, the AttributeValueList contains two attribute values and the comparison operator. <ul>
	 * 				<li><code>x</code> - <code>array</code> - Optional - This represents a simple array index. <ul>
	 * 					<li><code>S</code> - <code>string</code> - Optional - Strings are Unicode with UTF-8 binary encoding. The maximum size is limited by the size of the primary key (1024 bytes as a range part of a key or 2048 bytes as a single part hash key) or the item size (64k).</li>
	 * 					<li><code>N</code> - <code>string</code> - Optional - Numbers are positive or negative exact-value decimals and integers. A number can have up to 38 digits precision and can be between 10^-128 to 10^+126.</li>
	 * 					<li><code>SS</code> - <code>string|array</code> - Optional - A set of strings. Pass a string for a single value, or an indexed array for multiple values.</li>
	 * 					<li><code>NS</code> - <code>string|array</code> - Optional - A set of numbers. Pass a string for a single value, or an indexed array for multiple values.</li>
	 * 				</ul></li>
	 * 			</ul></li>
	 * 			<li><code>ComparisonOperator</code> - <code>string</code> - Required - A comparison operator is an enumeration of several operations:<ul><li> <code>EQ</code> for <em>equal</em> </li><li> <code>NE</code> for <em>not equal</em> </li><li> <code>IN</code> checks for exact matches</li><li> <code>LE</code> for <em>less than or equal to</em> </li><li> <code>LT</code> for <em>less than</em> </li><li> <code>GE</code> for <em>greater than or equal to</em> </li><li> <code>GT</code> for <em>greater than</em> </li><li> <code>BETWEEN</code> for <em>between</em> </li><li> <code>NOT_NULL</code> for <em>exists</em> </li><li> <code>NULL</code> for <em>not exists</em> </li><li> <code>CONTAINS</code> for substring or value in a set</li><li> <code>NOT_CONTAINS</code> for absence of a substring or absence of a value in a set</li><li> <code>BEGINS_WITH</code> for a substring prefix.</li></ul> [Allowed values: <code>EQ</code>, <code>NE</code>, <code>IN</code>, <code>LE</code>, <code>LT</code>, <code>GE</code>, <code>GT</code>, <code>BETWEEN</code>, <code>NOT_NULL</code>, <code>NULL</code>, <code>CONTAINS</code>, <code>NOT_CONTAINS</code>, <code>BEGINS_WITH</code>]</li>
	 * 		</ul></li>
	 * 	</ul></li>
	 * 	<li><code>ExclusiveStartKey</code> - <code>array</code> - Optional - Primary key of the item from which to continue an earlier scan. An earlier scan might provide this value if that scan operation was interrupted before scanning the entire table; either because of the result set size or the <code>Limit</code> parameter. The <code>LastEvaluatedKey</code> can be passed back in a new scan request to continue the operation from that point. <ul>
	 * 		<li><code>x</code> - <code>array</code> - Optional - This represents a simple array index. <ul>
	 * 			<li><code>HashKeyElement</code> - <code>array</code> - Required - A hash key element is treated as the primary key, and can be a string or a number. Single attribute primary keys have one index value. The value can be <code>String</code>, <code>Number</code>, <code>StringSet</code>, <code>NumberSet</code>. <ul>
	 * 				<li><code>x</code> - <code>array</code> - Optional - This represents a simple array index. <ul>
	 * 					<li><code>S</code> - <code>string</code> - Optional - Strings are Unicode with UTF-8 binary encoding. The maximum size is limited by the size of the primary key (1024 bytes as a range part of a key or 2048 bytes as a single part hash key) or the item size (64k).</li>
	 * 					<li><code>N</code> - <code>string</code> - Optional - Numbers are positive or negative exact-value decimals and integers. A number can have up to 38 digits precision and can be between 10^-128 to 10^+126.</li>
	 * 					<li><code>SS</code> - <code>string|array</code> - Optional - A set of strings. Pass a string for a single value, or an indexed array for multiple values.</li>
	 * 					<li><code>NS</code> - <code>string|array</code> - Optional - A set of numbers. Pass a string for a single value, or an indexed array for multiple values.</li>
	 * 				</ul></li>
	 * 			</ul></li>
	 * 			<li><code>RangeKeyElement</code> - <code>array</code> - Optional - A range key element is treated as a secondary key (used in conjunction with the primary key), and can be a string or a number, and is only used for hash-and-range primary keys. The value can be <code>String</code>, <code>Number</code>, <code>StringSet</code>, <code>NumberSet</code>. <ul>
	 * 				<li><code>x</code> - <code>array</code> - Optional - This represents a simple array index. <ul>
	 * 					<li><code>S</code> - <code>string</code> - Optional - Strings are Unicode with UTF-8 binary encoding. The maximum size is limited by the size of the primary key (1024 bytes as a range part of a key or 2048 bytes as a single part hash key) or the item size (64k).</li>
	 * 					<li><code>N</code> - <code>string</code> - Optional - Numbers are positive or negative exact-value decimals and integers. A number can have up to 38 digits precision and can be between 10^-128 to 10^+126.</li>
	 * 					<li><code>SS</code> - <code>string|array</code> - Optional - A set of strings. Pass a string for a single value, or an indexed array for multiple values.</li>
	 * 					<li><code>NS</code> - <code>string|array</code> - Optional - A set of numbers. Pass a string for a single value, or an indexed array for multiple values.</li>
	 * 				</ul></li>
	 * 			</ul></li>
	 * 		</ul></li>
	 * 	</ul></li>
	 * 	<li><code>curlopts</code> - <code>array</code> - Optional - A set of values to pass directly into <code>curl_setopt()</code>, where the key is a pre-defined <code>CURLOPT_*</code> constant.</li>
	 * 	<li><code>returnCurlHandle</code> - <code>boolean</code> - Optional - A private toggle specifying that the cURL handle be returned rather than actually completing the request. This toggle is useful for manually managed batch requests.</li></ul>
	 * @return CFResponse A <CFResponse> object containing a parsed HTTP response.
	 */
	public function scan($opt = null)
	{
		if (!$opt) $opt = array();

		// List (non-map)
		if (isset($opt['AttributesToGet']))
		{
			$opt['AttributesToGet'] = (is_array($opt['AttributesToGet']) ? $opt['AttributesToGet'] : array($opt['AttributesToGet']));
		}

		$opt = json_encode($opt);
		return $this->authenticate('Scan', $opt);
	}

	/**
	 * Edits an existing item's attributes.
	 *
	 * You can perform a conditional update (insert a new attribute name-value pair if it doesn't
	 * exist, or replace an existing name-value pair if it has certain expected attribute values).
	 *
	 * @param array $opt (Optional) An associative array of parameters that can have the following keys: <ul>
	 * 	<li><code>TableName</code> - <code>string</code> - Required - The name of the table in which you want to update an item. Allowed characters are <code>a-z</code>, <code>A-Z</code>, <code>0-9</code>, <code>_</code> (underscore), <code>-</code> (hyphen) and <code>.</code> (period). [Constraints: The value must be between 3 and 255 characters, and must match the following regular expression pattern: <code>[a-zA-Z0-9_.-]+</code>]</li>
	 * 	<li><code>Key</code> - <code>array</code> - Required - The primary key that uniquely identifies each item in a table. A primary key can be a one attribute (hash) primary key or a two attribute (hash-and-range) primary key. <ul>
	 * 		<li><code>x</code> - <code>array</code> - Optional - This represents a simple array index. <ul>
	 * 			<li><code>HashKeyElement</code> - <code>array</code> - Required - A hash key element is treated as the primary key, and can be a string or a number. Single attribute primary keys have one index value. The value can be <code>String</code>, <code>Number</code>, <code>StringSet</code>, <code>NumberSet</code>. <ul>
	 * 				<li><code>x</code> - <code>array</code> - Optional - This represents a simple array index. <ul>
	 * 					<li><code>S</code> - <code>string</code> - Optional - Strings are Unicode with UTF-8 binary encoding. The maximum size is limited by the size of the primary key (1024 bytes as a range part of a key or 2048 bytes as a single part hash key) or the item size (64k).</li>
	 * 					<li><code>N</code> - <code>string</code> - Optional - Numbers are positive or negative exact-value decimals and integers. A number can have up to 38 digits precision and can be between 10^-128 to 10^+126.</li>
	 * 					<li><code>SS</code> - <code>string|array</code> - Optional - A set of strings. Pass a string for a single value, or an indexed array for multiple values.</li>
	 * 					<li><code>NS</code> - <code>string|array</code> - Optional - A set of numbers. Pass a string for a single value, or an indexed array for multiple values.</li>
	 * 				</ul></li>
	 * 			</ul></li>
	 * 			<li><code>RangeKeyElement</code> - <code>array</code> - Optional - A range key element is treated as a secondary key (used in conjunction with the primary key), and can be a string or a number, and is only used for hash-and-range primary keys. The value can be <code>String</code>, <code>Number</code>, <code>StringSet</code>, <code>NumberSet</code>. <ul>
	 * 				<li><code>x</code> - <code>array</code> - Optional - This represents a simple array index. <ul>
	 * 					<li><code>S</code> - <code>string</code> - Optional - Strings are Unicode with UTF-8 binary encoding. The maximum size is limited by the size of the primary key (1024 bytes as a range part of a key or 2048 bytes as a single part hash key) or the item size (64k).</li>
	 * 					<li><code>N</code> - <code>string</code> - Optional - Numbers are positive or negative exact-value decimals and integers. A number can have up to 38 digits precision and can be between 10^-128 to 10^+126.</li>
	 * 					<li><code>SS</code> - <code>string|array</code> - Optional - A set of strings. Pass a string for a single value, or an indexed array for multiple values.</li>
	 * 					<li><code>NS</code> - <code>string|array</code> - Optional - A set of numbers. Pass a string for a single value, or an indexed array for multiple values.</li>
	 * 				</ul></li>
	 * 			</ul></li>
	 * 		</ul></li>
	 * 	</ul></li>
	 * 	<li><code>AttributeUpdates</code> - <code>array</code> - Required - Map of attribute name to the new value and action for the update. The attribute names specify the attributes to modify, and cannot contain any primary key attributes. <ul>
	 * 		<li><code>[attribute_name]</code> - <code>array</code> - Optional - The name of the attribute to use for the lookup. <ul>
	 * 			<li><code>Action</code> - <code>string</code> - Optional - The type of action for an item update operation. Only use the add action for numbers or sets; the specified value is added to the existing value. If a set of values is specified, the values are added to the existing set. Adds the specified attribute. If the attribute exists, it is replaced by the new value. If no value is specified, this removes the attribute and its value. If a set of values is specified, then the values in the specified set are removed from the old set. [Allowed values: <code>ADD</code>, <code>PUT</code>, <code>DELETE</code>]</li>
	 * 			<li><code>Value</code> - <code>array</code> - Optional - AttributeValue can be <code>String</code>, <code>Number</code>, <code>StringSet</code>, <code>NumberSet</code>. <ul>
	 * 				<li><code>x</code> - <code>array</code> - Optional - This represents a simple array index. <ul>
	 * 					<li><code>S</code> - <code>string</code> - Optional - Strings are Unicode with UTF-8 binary encoding. The maximum size is limited by the size of the primary key (1024 bytes as a range part of a key or 2048 bytes as a single part hash key) or the item size (64k).</li>
	 * 					<li><code>N</code> - <code>string</code> - Optional - Numbers are positive or negative exact-value decimals and integers. A number can have up to 38 digits precision and can be between 10^-128 to 10^+126.</li>
	 * 					<li><code>SS</code> - <code>string|array</code> - Optional - A set of strings. Pass a string for a single value, or an indexed array for multiple values.</li>
	 * 					<li><code>NS</code> - <code>string|array</code> - Optional - A set of numbers. Pass a string for a single value, or an indexed array for multiple values.</li>
	 * 				</ul></li>
	 * 			</ul></li>
	 * 		</ul></li>
	 * 	</ul></li>
	 * 	<li><code>Expected</code> - <code>array</code> - Optional - Designates an attribute for a conditional modification. The <code>Expected</code> parameter allows you to provide an attribute name, and whether or not Amazon DynamoDB should check to see if the attribute has a particular value before modifying it. <ul>
	 * 		<li><code>[attribute_name]</code> - <code>array</code> - Optional - The name of the attribute to use for the lookup. <ul>
	 * 			<li><code>Value</code> - <code>array</code> - Optional - Specify whether or not a value already exists and has a specific content for the attribute name-value pair. <ul>
	 * 				<li><code>x</code> - <code>array</code> - Optional - This represents a simple array index. <ul>
	 * 					<li><code>S</code> - <code>string</code> - Optional - Strings are Unicode with UTF-8 binary encoding. The maximum size is limited by the size of the primary key (1024 bytes as a range part of a key or 2048 bytes as a single part hash key) or the item size (64k).</li>
	 * 					<li><code>N</code> - <code>string</code> - Optional - Numbers are positive or negative exact-value decimals and integers. A number can have up to 38 digits precision and can be between 10^-128 to 10^+126.</li>
	 * 					<li><code>SS</code> - <code>string|array</code> - Optional - A set of strings. Pass a string for a single value, or an indexed array for multiple values.</li>
	 * 					<li><code>NS</code> - <code>string|array</code> - Optional - A set of numbers. Pass a string for a single value, or an indexed array for multiple values.</li>
	 * 				</ul></li>
	 * 			</ul></li>
	 * 			<li><code>Exists</code> - <code>boolean</code> - Optional - Specify whether or not a value already exists for the attribute name-value pair.</li>
	 * 		</ul></li>
	 * 	</ul></li>
	 * 	<li><code>ReturnValues</code> - <code>string</code> - Optional - Use this parameter if you want to get the attribute name-value pairs before or after they are modified. For <code>PUT</code> operations, the possible parameter values are <code>NONE</code> (default) or <code>ALL_OLD</code>. For update operations, the possible parameter values are <code>NONE</code> (default) or <code>ALL_OLD</code>, <code>UPDATED_OLD</code>, <code>ALL_NEW</code> or <code>UPDATED_NEW</code>.<ul><li> <code>NONE</code>: Nothing is returned.</li><li> <code>ALL_OLD</code>: Returns the attributes of the item as they were before the operation.</li><li> <code>UPDATED_OLD</code>: Returns the values of the updated attributes, only, as they were before the operation.</li><li> <code>ALL_NEW</code>: Returns all the attributes and their new values after the operation.</li><li> <code>UPDATED_NEW</code>: Returns the values of the updated attributes, only, as they are after the operation.</li></ul> [Allowed values: <code>NONE</code>, <code>ALL_OLD</code>, <code>UPDATED_OLD</code>, <code>ALL_NEW</code>, <code>UPDATED_NEW</code>]</li>
	 * 	<li><code>curlopts</code> - <code>array</code> - Optional - A set of values to pass directly into <code>curl_setopt()</code>, where the key is a pre-defined <code>CURLOPT_*</code> constant.</li>
	 * 	<li><code>returnCurlHandle</code> - <code>boolean</code> - Optional - A private toggle specifying that the cURL handle be returned rather than actually completing the request. This toggle is useful for manually managed batch requests.</li></ul>
	 * @return CFResponse A <CFResponse> object containing a parsed HTTP response.
	 */
	public function update_item($opt = null)
	{
		if (!$opt) $opt = array();

		$opt = json_encode($opt);
		return $this->authenticate('UpdateItem', $opt);
	}

	/**
	 * Updates the provisioned throughput for the given table.
	 *
	 * Setting the throughput for a table helps you manage performance and is part of the Provisioned
	 * Capacity feature of Amazon DynamoDB.
	 *
	 * @param array $opt (Optional) An associative array of parameters that can have the following keys: <ul>
	 * 	<li><code>TableName</code> - <code>string</code> - Required - The name of the table you want to update. Allowed characters are <code>a-z</code>, <code>A-Z</code>, <code>0-9</code>, <code>_</code> (underscore), <code>-</code> (hyphen) and <code>.</code> (period). [Constraints: The value must be between 3 and 255 characters, and must match the following regular expression pattern: <code>[a-zA-Z0-9_.-]+</code>]</li>
	 * 	<li><code>ProvisionedThroughput</code> - <code>array</code> - Required - Provisioned throughput reserves the required read and write resources for your table in terms of <code>ReadCapacityUnits</code> and <code>WriteCapacityUnits</code>. Values for provisioned throughput depend upon your expected read/write rates, item size, and consistency. Provide the expected number of read and write operations, assuming an item size of 1k and strictly consistent reads. For 2k item size, double the value. For 3k, triple the value, etc. Eventually-consistent reads consume half the resources of strictly consistent reads. <ul>
	 * 		<li><code>x</code> - <code>array</code> - Optional - This represents a simple array index. <ul>
	 * 			<li><code>ReadCapacityUnits</code> - <code>long</code> - Required - <code>ReadCapacityUnits</code> are in terms of strictly consistent reads, assuming items of 1k. 2k items require twice the <code>ReadCapacityUnits</code>. Eventually-consistent reads only require half the <code>ReadCapacityUnits</code> of stirctly consistent reads.</li>
	 * 			<li><code>WriteCapacityUnits</code> - <code>long</code> - Required - <code>WriteCapacityUnits</code> are in terms of strictly consistent reads, assuming items of 1k. 2k items require twice the <code>WriteCapacityUnits</code>.</li>
	 * 		</ul></li>
	 * 	</ul></li>
	 * 	<li><code>curlopts</code> - <code>array</code> - Optional - A set of values to pass directly into <code>curl_setopt()</code>, where the key is a pre-defined <code>CURLOPT_*</code> constant.</li>
	 * 	<li><code>returnCurlHandle</code> - <code>boolean</code> - Optional - A private toggle specifying that the cURL handle be returned rather than actually completing the request. This toggle is useful for manually managed batch requests.</li></ul>
	 * @return CFResponse A <CFResponse> object containing a parsed HTTP response.
	 */
	public function update_table($opt = null)
	{
		if (!$opt) $opt = array();

		$opt = json_encode($opt);
		return $this->authenticate('UpdateTable', $opt);
	}
}


/*%******************************************************************************************%*/
// EXCEPTIONS

class DynamoDB_Exception extends Exception {}
