<?php
/*
 * Copyright 2010-2011 Amazon.com, Inc. or its affiliates. All Rights Reserved.
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
 * Amazon Simple Queue Service (Amazon SQS) offers a reliable, highly scalable, hosted queue for
 * storing messages as they travel between computers. By using Amazon SQS, developers can simply
 * move data between distributed components of their applications that perform different tasks,
 * without losing messages or requiring each component to be always available. Amazon SQS makes it
 * easy to build an automated workflow, working in close conjunction with the Amazon Elastic
 * Compute Cloud (Amazon EC2) and the other AWS infrastructure web services.
 *  
 * Amazon SQS works by exposing Amazon's web-scale messaging infrastructure as a web service. Any
 * computer on the Internet can add or read messages without any installed software or special
 * firewall configurations. Components of applications using Amazon SQS can run independently, and
 * do not need to be on the same network, developed with the same technologies, or running at the
 * same time.
 *  
 * Visit <a href="http://aws.amazon.com/sqs/">http://aws.amazon.com/sqs/</a> for more information.
 *
 * @version 2011.12.13
 * @license See the included NOTICE.md file for complete information.
 * @copyright See the included NOTICE.md file for complete information.
 * @link http://aws.amazon.com/sqs/ Amazon Simple Queue Service
 * @link http://aws.amazon.com/sqs/documentation/ Amazon Simple Queue Service documentation
 */
class AmazonSQS extends CFRuntime
{
	/*%******************************************************************************************%*/
	// CLASS CONSTANTS

	/**
	 * Specify the queue URL for the United States East (Northern Virginia) Region.
	 */
	const REGION_US_E1 = 'sqs.us-east-1.amazonaws.com';

	/**
	 * Specify the queue URL for the United States East (Northern Virginia) Region.
	 */
	const REGION_VIRGINIA = self::REGION_US_E1;

	/**
	 * Specify the queue URL for the United States West (Northern California) Region.
	 */
	const REGION_US_W1 = 'sqs.us-west-1.amazonaws.com';

	/**
	 * Specify the queue URL for the United States West (Northern California) Region.
	 */
	const REGION_CALIFORNIA = self::REGION_US_W1;

	/**
	 * Specify the queue URL for the United States West (Oregon) Region.
	 */
	const REGION_US_W2 = 'sqs.us-west-2.amazonaws.com';

	/**
	 * Specify the queue URL for the United States West (Oregon) Region.
	 */
	const REGION_OREGON = self::REGION_US_W2;

	/**
	 * Specify the queue URL for the Europe West (Ireland) Region.
	 */
	const REGION_EU_W1 = 'sqs.eu-west-1.amazonaws.com';

	/**
	 * Specify the queue URL for the Europe West (Ireland) Region.
	 */
	const REGION_IRELAND = self::REGION_EU_W1;

	/**
	 * Specify the queue URL for the Asia Pacific Southeast (Singapore) Region.
	 */
	const REGION_APAC_SE1 = 'sqs.ap-southeast-1.amazonaws.com';

	/**
	 * Specify the queue URL for the Asia Pacific Southeast (Singapore) Region.
	 */
	const REGION_SINGAPORE = self::REGION_APAC_SE1;

	/**
	 * Specify the queue URL for the Asia Pacific Northeast (Tokyo) Region.
	 */
	const REGION_APAC_NE1 = 'sqs.ap-northeast-1.amazonaws.com';

	/**
	 * Specify the queue URL for the Asia Pacific Northeast (Tokyo) Region.
	 */
	const REGION_TOKYO = self::REGION_APAC_NE1;

	/**
	 * Specify the queue URL for the South America (Sao Paulo) Region.
	 */
	const REGION_SA_E1 = 'sqs.sa-east-1.amazonaws.com';

	/**
	 * Specify the queue URL for the South America (Sao Paulo) Region.
	 */
	const REGION_SAO_PAULO = self::REGION_SA_E1;

	/**
	 * Default service endpoint.
	 */
	const DEFAULT_URL = self::REGION_US_E1;


	/*%******************************************************************************************%*/
	// CONSTRUCTOR

	/**
	 * Constructs a new instance of <AmazonSQS>.
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
		$this->api_version = '2011-10-01';
		$this->hostname = self::DEFAULT_URL;
		$this->auth_class = 'AuthV2Query';

		return parent::__construct($options);
	}


	/*%******************************************************************************************%*/
	// SETTERS

	/**
	 * This allows you to explicitly sets the region for the service to use.
	 *
	 * @param string $region (Required) The region to explicitly set. Available options are <REGION_US_E1>, <REGION_US_W1>, <REGION_US_W2>, <REGION_EU_W1>, <REGION_APAC_SE1>, <REGION_APAC_NE1>, <REGION_SA_E1>.
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
	// CONVENIENCE METHODS

	/**
	 * Converts a queue URI into a queue ARN.
	 *
	 * @param string $queue_url (Required) The queue URL to perform the action on. Retrieved when the queue is first created.
	 * @return string An ARN representation of the queue URI.
	 */
	function get_queue_arn($queue_url)
	{
		return str_replace(
			array('http://',  'https://', '.amazonaws.com', '/', '.'),
			array('arn:aws:', 'arn:aws:', '',               ':', ':'),
			$queue_url
		);
	}

	/**
	 * Returns the approximate number of messages in the queue.
	 *
	 * @param string $queue_url (Required) The queue URL to perform the action on. Retrieved when the queue is first created.
	 * @return mixed The Approximate number of messages in the queue as an integer. If the queue doesn't exist, it returns the entire <CFResponse> object.
	 */
	public function get_queue_size($queue_url)
	{
		$response = $this->get_queue_attributes($queue_url, array(
			'AttributeName' => 'ApproximateNumberOfMessages'
		));

		if (!$response->isOK())
		{
			return $response;
		}

		return (integer) $response->body->Value(0);
	}

	/**
	 * ONLY lists the queue URLs, as an array, on the SQS account.
	 *
	 * @param string $pcre (Optional) A Perl-Compatible Regular Expression (PCRE) to filter the names against.
	 * @return array The list of matching queue names. If there are no results, the method will return an empty array.
	 * @link http://php.net/pcre Perl-Compatible Regular Expression (PCRE) Docs
	 */
	public function get_queue_list($pcre = null)
	{
		if ($this->use_batch_flow)
		{
			throw new SQS_Exception(__FUNCTION__ . '() cannot be batch requested');
		}

		// Get a list of queues.
		$list = $this->list_queues();
		if ($list = $list->body->QueueUrl())
		{
			$list = $list->map_string($pcre);
			return $list;
		}

		return array();
	}


	/*%******************************************************************************************%*/
	// OVERWRITTEN METHODS

	/**
	 * This overwrites the default authenticate method in sdk.class.php to address SQS queue URLs.
	 *
	 * @return CFResponse Object containing a parsed HTTP response.
	 */
	public function authenticate($operation, $payload)
	{
		// Save the current hostname
		$hostname = $this->hostname;

		if (isset($payload['QueueUrl']))
		{
			// Change the hostname to the queue URL
			$this->hostname = $payload['QueueUrl'];

			// Remove "QueueURL" from the payload
			unset($payload['QueueUrl']);
		}

		// Perform the request
		$response = parent::authenticate($operation, $payload);

		// Restore the hostname
		$this->hostname = $hostname;

		return $response;
	}


	/*%******************************************************************************************%*/
	// SERVICE METHODS

	/**
	 * The AddPermission action adds a permission to a queue for a specific <a href=
	 * "http://docs.amazonwebservices.com/AWSSimpleQueueService/latest/APIReference/Glossary.html#d0e3892">
	 * principal</a>. This allows for sharing access to the queue.
	 *  
	 * When you create a queue, you have full control access rights for the queue. Only you (as owner
	 * of the queue) can grant or deny permissions to the queue. For more information about these
	 * permissions, see <a href=
	 * "http://docs.amazonwebservices.com/AWSSimpleQueueService/latest/SQSDeveloperGuide/?acp-overview.html">
	 * Shared Queues</a> in the Amazon SQS Developer Guide.
	 *  
	 * <code>AddPermission</code> writes an SQS-generated policy. If you want to write your own
	 * policy, use SetQueueAttributes to upload your policy. For more information about writing your
	 * own policy, see <a href=
	 * "http://docs.amazonwebservices.com/AWSSimpleQueueService/latest/SQSDeveloperGuide/?AccessPolicyLanguage.html">
	 * Appendix: The Access Policy Language</a> in the Amazon SQS Developer Guide.
	 *
	 * @param string $queue_url (Required) The URL of the SQS queue to take action on.
	 * @param string $label (Required) The unique identification of the permission you're setting (e.g., <code>AliceSendMessage</code>). Constraints: Maximum 80 characters; alphanumeric characters, hyphens (-), and underscores (_) are allowed.
	 * @param string|array $aws_account_id (Required) The AWS account number of the <a href="http://docs.amazonwebservices.com/AWSSimpleQueueService/latest/APIReference/Glossary.html">principal</a> who will be given permission. The principal must have an AWS account, but does not need to be signed up for Amazon SQS. Pass a string for a single value, or an indexed array for multiple values.
	 * @param string|array $action_name (Required) The action the client wants to allow for the specified principal. Pass a string for a single value, or an indexed array for multiple values.
	 * @param array $opt (Optional) An associative array of parameters that can have the following keys: <ul>
	 * 	<li><code>curlopts</code> - <code>array</code> - Optional - A set of values to pass directly into <code>curl_setopt()</code>, where the key is a pre-defined <code>CURLOPT_*</code> constant.</li>
	 * 	<li><code>returnCurlHandle</code> - <code>boolean</code> - Optional - A private toggle specifying that the cURL handle be returned rather than actually completing the request. This toggle is useful for manually managed batch requests.</li></ul>
	 * @return CFResponse A <CFResponse> object containing a parsed HTTP response.
	 */
	public function add_permission($queue_url, $label, $aws_account_id, $action_name, $opt = null)
	{
		if (!$opt) $opt = array();
		$opt['QueueUrl'] = $queue_url;
		$opt['Label'] = $label;
		
		// Required list (non-map)
		$opt = array_merge($opt, CFComplexType::map(array(
			'AWSAccountId' => (is_array($aws_account_id) ? $aws_account_id : array($aws_account_id))
		)));
		
		// Required list (non-map)
		$opt = array_merge($opt, CFComplexType::map(array(
			'ActionName' => (is_array($action_name) ? $action_name : array($action_name))
		)));

		return $this->authenticate('AddPermission', $opt);
	}

	/**
	 * The <code>ChangeMessageVisibility</code> action changes the visibility timeout of a specified
	 * message in a queue to a new value. The maximum allowed timeout value you can set the value to
	 * is 12 hours. This means you can't extend the timeout of a message in an existing queue to more
	 * than a total visibility timeout of 12 hours. (For more information visibility timeout, see
	 * 	<a href=
	 * "http://docs.amazonwebservices.com/AWSSimpleQueueService/latest/SQSDeveloperGuide/AboutVT.html">
	 * Visibility Timeout</a> in the Amazon SQS Developer Guide.)
	 *  
	 * For example, let's say you have a message and its default message visibility timeout is 30
	 * minutes. You could call <code>ChangeMessageVisiblity</code> with a value of two hours and the
	 * effective timeout would be two hours and 30 minutes. When that time comes near you could again
	 * extend the time out by calling ChangeMessageVisiblity, but this time the maximum allowed
	 * timeout would be 9 hours and 30 minutes.
	 * 
	 * <p class="important">
	 * If you attempt to set the <code>VisibilityTimeout</code> to an amount more than the maximum
	 * time left, Amazon SQS returns an error. It will not automatically recalculate and increase the
	 * timeout to the maximum time remaining.
	 * </p>
	 * <p class="important">
	 * Unlike with a queue, when you change the visibility timeout for a specific message, that
	 * timeout value is applied immediately but is not saved in memory for that message. If you don't
	 * delete a message after it is received, the visibility timeout for the message the next time it
	 * is received reverts to the original timeout value, not the value you set with the
	 * ChangeMessageVisibility action.
	 * </p>
	 *
	 * @param string $queue_url (Required) The URL of the SQS queue to take action on.
	 * @param string $receipt_handle (Required) The receipt handle associated with the message whose visibility timeout should be changed.
	 * @param integer $visibility_timeout (Required) The new value (in seconds) for the message's visibility timeout.
	 * @param array $opt (Optional) An associative array of parameters that can have the following keys: <ul>
	 * 	<li><code>curlopts</code> - <code>array</code> - Optional - A set of values to pass directly into <code>curl_setopt()</code>, where the key is a pre-defined <code>CURLOPT_*</code> constant.</li>
	 * 	<li><code>returnCurlHandle</code> - <code>boolean</code> - Optional - A private toggle specifying that the cURL handle be returned rather than actually completing the request. This toggle is useful for manually managed batch requests.</li></ul>
	 * @return CFResponse A <CFResponse> object containing a parsed HTTP response.
	 */
	public function change_message_visibility($queue_url, $receipt_handle, $visibility_timeout, $opt = null)
	{
		if (!$opt) $opt = array();
		$opt['QueueUrl'] = $queue_url;
		$opt['ReceiptHandle'] = $receipt_handle;
		$opt['VisibilityTimeout'] = $visibility_timeout;
		
		return $this->authenticate('ChangeMessageVisibility', $opt);
	}

	/**
	 * This is a batch version of <code>ChangeMessageVisibility</code>. It takes multiple receipt
	 * handles and performs the operation on each of the them. The result of the operation on each
	 * message is reported individually in the response.
	 *
	 * @param string $queue_url (Required) The URL of the SQS queue to take action on.
	 * @param array $change_message_visibility_batch_request_entry (Required) A list of receipt handles of the messages for which the visibility timeout must be changed. <ul>
	 * 	<li><code>x</code> - <code>array</code> - Optional - This represents a simple array index. <ul>
	 * 		<li><code>Id</code> - <code>string</code> - Required - An identifier for this particular receipt handle. This is used to communicate the result. Note that the <code>Id</code> s of a batch request need to be unique within the request.</li>
	 * 		<li><code>ReceiptHandle</code> - <code>string</code> - Required - A receipt handle.</li>
	 * 		<li><code>VisibilityTimeout</code> - <code>integer</code> - Optional - The new value (in seconds) for the message's visibility timeout.</li>
	 * 	</ul></li>
	 * </ul>
	 * @param array $opt (Optional) An associative array of parameters that can have the following keys: <ul>
	 * 	<li><code>curlopts</code> - <code>array</code> - Optional - A set of values to pass directly into <code>curl_setopt()</code>, where the key is a pre-defined <code>CURLOPT_*</code> constant.</li>
	 * 	<li><code>returnCurlHandle</code> - <code>boolean</code> - Optional - A private toggle specifying that the cURL handle be returned rather than actually completing the request. This toggle is useful for manually managed batch requests.</li></ul>
	 * @return CFResponse A <CFResponse> object containing a parsed HTTP response.
	 */
	public function change_message_visibility_batch($queue_url, $change_message_visibility_batch_request_entry, $opt = null)
	{
		if (!$opt) $opt = array();
		$opt['QueueUrl'] = $queue_url;
		
		// Required list + map
		$opt = array_merge($opt, CFComplexType::map(array(
			'ChangeMessageVisibilityBatchRequestEntry' => (is_array($change_message_visibility_batch_request_entry) ? $change_message_visibility_batch_request_entry : array($change_message_visibility_batch_request_entry))
		)));

		return $this->authenticate('ChangeMessageVisibilityBatch', $opt);
	}

	/**
	 * The <code>CreateQueue</code> action creates a new queue, or returns the URL of an existing one.
	 * When you request <code>CreateQueue</code>, you provide a name for the queue. To successfully
	 * create a new queue, you must provide a name that is unique within the scope of your own queues.
	 *  
	 * You may pass one or more attributes in the request. If you do not provide a value for any
	 * attribute, the queue will have the default value for that attribute. Permitted attributes are
	 * the same that can be set using <code>SetQueueAttributes</code>.
	 *  
	 * If you provide the name of an existing queue, a new queue isn't created. If the values of
	 * attributes provided with the request match up with those on the existing queue, the queue URL
	 * is returned. Otherwise, a <code>QueueNameExists</code> error is returned.
	 *
	 * @param string $queue_name (Required) The name for the queue to be created.
	 * @param array $opt (Optional) An associative array of parameters that can have the following keys: <ul>
	 * 	<li><code>Attribute</code> - <code>array</code> - Optional - A map of attributes with their corresponding values. <ul>
	 * 		<li><code>x</code> - <code>array</code> - Optional - This represents a simple array index. <ul>
	 * 			<li><code>Name</code> - <code>string</code> - Optional - The name of a queue attribute. [Allowed values: <code>Policy</code>, <code>VisibilityTimeout</code>, <code>MaximumMessageSize</code>, <code>MessageRetentionPeriod</code>, <code>ApproximateNumberOfMessages</code>, <code>ApproximateNumberOfMessagesNotVisible</code>, <code>CreatedTimestamp</code>, <code>LastModifiedTimestamp</code>, <code>QueueArn</code>, <code>ApproximateNumberOfMessagesDelayed</code>, <code>DelaySeconds</code>]</li>
	 * 			<li><code>Value</code> - <code>string</code> - Optional - The value of a queue attribute.</li>
	 * 		</ul></li>
	 * 	</ul></li>
	 * 	<li><code>curlopts</code> - <code>array</code> - Optional - A set of values to pass directly into <code>curl_setopt()</code>, where the key is a pre-defined <code>CURLOPT_*</code> constant.</li>
	 * 	<li><code>returnCurlHandle</code> - <code>boolean</code> - Optional - A private toggle specifying that the cURL handle be returned rather than actually completing the request. This toggle is useful for manually managed batch requests.</li></ul>
	 * @return CFResponse A <CFResponse> object containing a parsed HTTP response.
	 */
	public function create_queue($queue_name, $opt = null)
	{
		if (!$opt) $opt = array();
		$opt['QueueName'] = $queue_name;
		
		// Optional map (non-list)
		if (isset($opt['Attribute']))
		{
			$opt = array_merge($opt, CFComplexType::map(array(
				'Attribute' => $opt['Attribute']
			)));
			unset($opt['Attribute']);
		}

		return $this->authenticate('CreateQueue', $opt);
	}

	/**
	 * The <code>DeleteMessage</code> action unconditionally removes the specified message from the
	 * specified queue. Even if the message is locked by another reader due to the visibility timeout
	 * setting, it is still deleted from the queue.
	 *
	 * @param string $queue_url (Required) The URL of the SQS queue to take action on.
	 * @param string $receipt_handle (Required) The receipt handle associated with the message to delete.
	 * @param array $opt (Optional) An associative array of parameters that can have the following keys: <ul>
	 * 	<li><code>curlopts</code> - <code>array</code> - Optional - A set of values to pass directly into <code>curl_setopt()</code>, where the key is a pre-defined <code>CURLOPT_*</code> constant.</li>
	 * 	<li><code>returnCurlHandle</code> - <code>boolean</code> - Optional - A private toggle specifying that the cURL handle be returned rather than actually completing the request. This toggle is useful for manually managed batch requests.</li></ul>
	 * @return CFResponse A <CFResponse> object containing a parsed HTTP response.
	 */
	public function delete_message($queue_url, $receipt_handle, $opt = null)
	{
		if (!$opt) $opt = array();
		$opt['QueueUrl'] = $queue_url;
		$opt['ReceiptHandle'] = $receipt_handle;
		
		return $this->authenticate('DeleteMessage', $opt);
	}

	/**
	 * This is a batch version of <code>DeleteMessage</code>. It takes multiple receipt handles and
	 * deletes each one of the messages. The result of the delete operation on each message is
	 * reported individually in the response.
	 *
	 * @param string $queue_url (Required) The URL of the SQS queue to take action on.
	 * @param array $delete_message_batch_request_entry (Required) A list of receipt handles for the messages to be deleted. <ul>
	 * 	<li><code>x</code> - <code>array</code> - Optional - This represents a simple array index. <ul>
	 * 		<li><code>Id</code> - <code>string</code> - Required - An identifier for this particular receipt handle. This is used to communicate the result. Note that the <code>Id</code> s of a batch request need to be unique within the request.</li>
	 * 		<li><code>ReceiptHandle</code> - <code>string</code> - Required - A receipt handle.</li>
	 * 	</ul></li>
	 * </ul>
	 * @param array $opt (Optional) An associative array of parameters that can have the following keys: <ul>
	 * 	<li><code>curlopts</code> - <code>array</code> - Optional - A set of values to pass directly into <code>curl_setopt()</code>, where the key is a pre-defined <code>CURLOPT_*</code> constant.</li>
	 * 	<li><code>returnCurlHandle</code> - <code>boolean</code> - Optional - A private toggle specifying that the cURL handle be returned rather than actually completing the request. This toggle is useful for manually managed batch requests.</li></ul>
	 * @return CFResponse A <CFResponse> object containing a parsed HTTP response.
	 */
	public function delete_message_batch($queue_url, $delete_message_batch_request_entry, $opt = null)
	{
		if (!$opt) $opt = array();
		$opt['QueueUrl'] = $queue_url;
		
		// Required list + map
		$opt = array_merge($opt, CFComplexType::map(array(
			'DeleteMessageBatchRequestEntry' => (is_array($delete_message_batch_request_entry) ? $delete_message_batch_request_entry : array($delete_message_batch_request_entry))
		)));

		return $this->authenticate('DeleteMessageBatch', $opt);
	}

	/**
	 * This action unconditionally deletes the queue specified by the queue URL. Use this operation
	 * WITH CARE! The queue is deleted even if it is NOT empty.
	 *  
	 * Once a queue has been deleted, the queue name is unavailable for use with new queues for 60
	 * seconds.
	 *
	 * @param string $queue_url (Required) The URL of the SQS queue to take action on.
	 * @param array $opt (Optional) An associative array of parameters that can have the following keys: <ul>
	 * 	<li><code>curlopts</code> - <code>array</code> - Optional - A set of values to pass directly into <code>curl_setopt()</code>, where the key is a pre-defined <code>CURLOPT_*</code> constant.</li>
	 * 	<li><code>returnCurlHandle</code> - <code>boolean</code> - Optional - A private toggle specifying that the cURL handle be returned rather than actually completing the request. This toggle is useful for manually managed batch requests.</li></ul>
	 * @return CFResponse A <CFResponse> object containing a parsed HTTP response.
	 */
	public function delete_queue($queue_url, $opt = null)
	{
		if (!$opt) $opt = array();
		$opt['QueueUrl'] = $queue_url;
		
		return $this->authenticate('DeleteQueue', $opt);
	}

	/**
	 * Gets attributes for the specified queue. The following attributes are supported:
	 * 
	 * <ul>
	 * 	<li><code>All</code> - returns all values.</li>
	 * 	<li><code>ApproximateNumberOfMessages</code> - returns the approximate number of visible
	 * 	messages in a queue. For more information, see Resources Required to Process Messages in
	 * 	the Amazon SQS Developer Guide.</li>
	 * 	<li><code>ApproximateNumberOfMessagesNotVisible</code> - returns the approximate number of
	 * 	messages that are not timed-out and not deleted. For more information, see Resources
	 * 	Required to Process Messages in the Amazon SQS Developer Guide.</li>
	 * 	<li><code>VisibilityTimeout</code> - returns the visibility timeout for the queue. For more
	 * 	information about visibility timeout, see Visibility Timeout in the Amazon SQS Developer
	 * 	Guide.</li>
	 * 	<li><code>CreatedTimestamp</code> - returns the time when the queue was created (epoch time in
	 * 	seconds).</li>
	 * 	<li><code>LastModifiedTimestamp</code> - returns the time when the queue was last changed
	 * 	(epoch time in seconds).</li>
	 * 	<li><code>Policy</code> - returns the queue's policy.</li>
	 * 	<li><code>MaximumMessageSize</code> - returns the limit of how many bytes a message can contain
	 * 	before Amazon SQS rejects it.</li>
	 * 	<li><code>MessageRetentionPeriod</code> - returns the number of seconds Amazon SQS retains a
	 * 	message.</li>
	 * 	<li><code>QueueArn</code> - returns the queue's Amazon resource name (ARN).</li>
	 * 	<li><code>ApproximateNumberOfMessagesDelayed</code> - returns the approximate number of
	 * 	messages that are pending to be added to the queue.</li>
	 * 	<li><code>DelaySeconds</code> - returns the default delay on the queue in seconds.</li>
	 * </ul>
	 *
	 * @param string $queue_url (Required) The URL of the SQS queue to take action on.
	 * @param array $opt (Optional) An associative array of parameters that can have the following keys: <ul>
	 * 	<li><code>AttributeName</code> - <code>string|array</code> - Optional - A list of attributes to retrieve information for. Pass a string for a single value, or an indexed array for multiple values.</li>
	 * 	<li><code>curlopts</code> - <code>array</code> - Optional - A set of values to pass directly into <code>curl_setopt()</code>, where the key is a pre-defined <code>CURLOPT_*</code> constant.</li>
	 * 	<li><code>returnCurlHandle</code> - <code>boolean</code> - Optional - A private toggle specifying that the cURL handle be returned rather than actually completing the request. This toggle is useful for manually managed batch requests.</li></ul>
	 * @return CFResponse A <CFResponse> object containing a parsed HTTP response.
	 */
	public function get_queue_attributes($queue_url, $opt = null)
	{
		if (!$opt) $opt = array();
		$opt['QueueUrl'] = $queue_url;
		
		// Optional list (non-map)
		if (isset($opt['AttributeName']))
		{
			$opt = array_merge($opt, CFComplexType::map(array(
				'AttributeName' => (is_array($opt['AttributeName']) ? $opt['AttributeName'] : array($opt['AttributeName']))
			)));
			unset($opt['AttributeName']);
		}

		return $this->authenticate('GetQueueAttributes', $opt);
	}

	/**
	 * The <code>GetQueueUrl</code> action returns the URL of an existing queue.
	 *
	 * @param string $queue_name (Required) The name of the queue whose URL must be fetched.
	 * @param array $opt (Optional) An associative array of parameters that can have the following keys: <ul>
	 * 	<li><code>QueueOwnerAWSAccountId</code> - <code>string</code> - Optional - The AWS account number of the queue's owner.</li>
	 * 	<li><code>curlopts</code> - <code>array</code> - Optional - A set of values to pass directly into <code>curl_setopt()</code>, where the key is a pre-defined <code>CURLOPT_*</code> constant.</li>
	 * 	<li><code>returnCurlHandle</code> - <code>boolean</code> - Optional - A private toggle specifying that the cURL handle be returned rather than actually completing the request. This toggle is useful for manually managed batch requests.</li></ul>
	 * @return CFResponse A <CFResponse> object containing a parsed HTTP response.
	 */
	public function get_queue_url($queue_name, $opt = null)
	{
		if (!$opt) $opt = array();
		$opt['QueueName'] = $queue_name;
		
		return $this->authenticate('GetQueueUrl', $opt);
	}

	/**
	 * Returns a list of your queues.
	 *
	 * @param array $opt (Optional) An associative array of parameters that can have the following keys: <ul>
	 * 	<li><code>QueueNamePrefix</code> - <code>string</code> - Optional - A string to use for filtering the list results. Only those queues whose name begins with the specified string are returned.</li>
	 * 	<li><code>curlopts</code> - <code>array</code> - Optional - A set of values to pass directly into <code>curl_setopt()</code>, where the key is a pre-defined <code>CURLOPT_*</code> constant.</li>
	 * 	<li><code>returnCurlHandle</code> - <code>boolean</code> - Optional - A private toggle specifying that the cURL handle be returned rather than actually completing the request. This toggle is useful for manually managed batch requests.</li></ul>
	 * @return CFResponse A <CFResponse> object containing a parsed HTTP response.
	 */
	public function list_queues($opt = null)
	{
		if (!$opt) $opt = array();
				
		return $this->authenticate('ListQueues', $opt);
	}

	/**
	 * Retrieves one or more messages from the specified queue, including the message body and message
	 * ID of each message. Messages returned by this action stay in the queue until you delete them.
	 * However, once a message is returned to a <code>ReceiveMessage</code> request, it is not
	 * returned on subsequent <code>ReceiveMessage</code> requests for the duration of the
	 * <code>VisibilityTimeout</code>. If you do not specify a <code>VisibilityTimeout</code> in the
	 * request, the overall visibility timeout for the queue is used for the returned messages.
	 *
	 * @param string $queue_url (Required) The URL of the SQS queue to take action on.
	 * @param array $opt (Optional) An associative array of parameters that can have the following keys: <ul>
	 * 	<li><code>AttributeName</code> - <code>string|array</code> - Optional - A list of attributes to retrieve information for. Pass a string for a single value, or an indexed array for multiple values.</li>
	 * 	<li><code>MaxNumberOfMessages</code> - <code>integer</code> - Optional - The maximum number of messages to return. Amazon SQS never returns more messages than this value but may return fewer. All of the messages are not necessarily returned.</li>
	 * 	<li><code>VisibilityTimeout</code> - <code>integer</code> - Optional - The duration (in seconds) that the received messages are hidden from subsequent retrieve requests after being retrieved by a <code>ReceiveMessage</code> request.</li>
	 * 	<li><code>curlopts</code> - <code>array</code> - Optional - A set of values to pass directly into <code>curl_setopt()</code>, where the key is a pre-defined <code>CURLOPT_*</code> constant.</li>
	 * 	<li><code>returnCurlHandle</code> - <code>boolean</code> - Optional - A private toggle specifying that the cURL handle be returned rather than actually completing the request. This toggle is useful for manually managed batch requests.</li></ul>
	 * @return CFResponse A <CFResponse> object containing a parsed HTTP response.
	 */
	public function receive_message($queue_url, $opt = null)
	{
		if (!$opt) $opt = array();
		$opt['QueueUrl'] = $queue_url;
		
		// Optional list (non-map)
		if (isset($opt['AttributeName']))
		{
			$opt = array_merge($opt, CFComplexType::map(array(
				'AttributeName' => (is_array($opt['AttributeName']) ? $opt['AttributeName'] : array($opt['AttributeName']))
			)));
			unset($opt['AttributeName']);
		}

		return $this->authenticate('ReceiveMessage', $opt);
	}

	/**
	 * The <code>RemovePermission</code> action revokes any permissions in the queue policy that
	 * matches the specified <code>Label</code> parameter. Only the owner of the queue can remove
	 * permissions.
	 *
	 * @param string $queue_url (Required) The URL of the SQS queue to take action on.
	 * @param string $label (Required) The identification of the permission to remove. This is the label added with the <code>AddPermission</code> operation.
	 * @param array $opt (Optional) An associative array of parameters that can have the following keys: <ul>
	 * 	<li><code>curlopts</code> - <code>array</code> - Optional - A set of values to pass directly into <code>curl_setopt()</code>, where the key is a pre-defined <code>CURLOPT_*</code> constant.</li>
	 * 	<li><code>returnCurlHandle</code> - <code>boolean</code> - Optional - A private toggle specifying that the cURL handle be returned rather than actually completing the request. This toggle is useful for manually managed batch requests.</li></ul>
	 * @return CFResponse A <CFResponse> object containing a parsed HTTP response.
	 */
	public function remove_permission($queue_url, $label, $opt = null)
	{
		if (!$opt) $opt = array();
		$opt['QueueUrl'] = $queue_url;
		$opt['Label'] = $label;
		
		return $this->authenticate('RemovePermission', $opt);
	}

	/**
	 * The <code>SendMessage</code> action delivers a message to the specified queue.
	 *
	 * @param string $queue_url (Required) The URL of the SQS queue to take action on.
	 * @param string $message_body (Required) The message to send.
	 * @param array $opt (Optional) An associative array of parameters that can have the following keys: <ul>
	 * 	<li><code>DelaySeconds</code> - <code>integer</code> - Optional - The number of seconds the message has to be delayed.</li>
	 * 	<li><code>curlopts</code> - <code>array</code> - Optional - A set of values to pass directly into <code>curl_setopt()</code>, where the key is a pre-defined <code>CURLOPT_*</code> constant.</li>
	 * 	<li><code>returnCurlHandle</code> - <code>boolean</code> - Optional - A private toggle specifying that the cURL handle be returned rather than actually completing the request. This toggle is useful for manually managed batch requests.</li></ul>
	 * @return CFResponse A <CFResponse> object containing a parsed HTTP response.
	 */
	public function send_message($queue_url, $message_body, $opt = null)
	{
		if (!$opt) $opt = array();
		$opt['QueueUrl'] = $queue_url;
		$opt['MessageBody'] = $message_body;
		
		return $this->authenticate('SendMessage', $opt);
	}

	/**
	 * This is a batch version of <code>SendMessage</code>. It takes multiple messages and adds each
	 * of them to the queue. The result of each add operation is reported individually in the
	 * response.
	 *
	 * @param string $queue_url (Required) The URL of the SQS queue to take action on.
	 * @param array $send_message_batch_request_entry (Required) A list of <code>SendMessageBatchRequestEntry</code> s. <ul>
	 * 	<li><code>x</code> - <code>array</code> - Optional - This represents a simple array index. <ul>
	 * 		<li><code>Id</code> - <code>string</code> - Required - An identifier for the message in this batch. This is used to communicate the result. Note that the the <code>Id</code> s of a batch request need to be unique within the request.</li>
	 * 		<li><code>MessageBody</code> - <code>string</code> - Required - Body of the message.</li>
	 * 		<li><code>DelaySeconds</code> - <code>integer</code> - Optional - The number of seconds for which the message has to be delayed.</li>
	 * 	</ul></li>
	 * </ul>
	 * @param array $opt (Optional) An associative array of parameters that can have the following keys: <ul>
	 * 	<li><code>curlopts</code> - <code>array</code> - Optional - A set of values to pass directly into <code>curl_setopt()</code>, where the key is a pre-defined <code>CURLOPT_*</code> constant.</li>
	 * 	<li><code>returnCurlHandle</code> - <code>boolean</code> - Optional - A private toggle specifying that the cURL handle be returned rather than actually completing the request. This toggle is useful for manually managed batch requests.</li></ul>
	 * @return CFResponse A <CFResponse> object containing a parsed HTTP response.
	 */
	public function send_message_batch($queue_url, $send_message_batch_request_entry, $opt = null)
	{
		if (!$opt) $opt = array();
		$opt['QueueUrl'] = $queue_url;
		
		// Required list + map
		$opt = array_merge($opt, CFComplexType::map(array(
			'SendMessageBatchRequestEntry' => (is_array($send_message_batch_request_entry) ? $send_message_batch_request_entry : array($send_message_batch_request_entry))
		)));

		return $this->authenticate('SendMessageBatch', $opt);
	}

	/**
	 * Sets an attribute of a queue. The set of attributes that can be set are - DelaySeconds,
	 * MessageRetentionPeriod, MaximumMessageSize, VisibilityTimeout and Policy.
	 *
	 * @param string $queue_url (Required) The URL of the SQS queue to take action on.
	 * @param array $attribute (Required) A map of attributes to set. <ul>
	 * 	<li><code>x</code> - <code>array</code> - Optional - This represents a simple array index. <ul>
	 * 		<li><code>Name</code> - <code>string</code> - Optional - The name of a queue attribute. [Allowed values: <code>Policy</code>, <code>VisibilityTimeout</code>, <code>MaximumMessageSize</code>, <code>MessageRetentionPeriod</code>, <code>ApproximateNumberOfMessages</code>, <code>ApproximateNumberOfMessagesNotVisible</code>, <code>CreatedTimestamp</code>, <code>LastModifiedTimestamp</code>, <code>QueueArn</code>, <code>ApproximateNumberOfMessagesDelayed</code>, <code>DelaySeconds</code>]</li>
	 * 		<li><code>Value</code> - <code>string</code> - Optional - The value of a queue attribute.</li>
	 * 	</ul></li>
	 * </ul>
	 * @param array $opt (Optional) An associative array of parameters that can have the following keys: <ul>
	 * 	<li><code>curlopts</code> - <code>array</code> - Optional - A set of values to pass directly into <code>curl_setopt()</code>, where the key is a pre-defined <code>CURLOPT_*</code> constant.</li>
	 * 	<li><code>returnCurlHandle</code> - <code>boolean</code> - Optional - A private toggle specifying that the cURL handle be returned rather than actually completing the request. This toggle is useful for manually managed batch requests.</li></ul>
	 * @return CFResponse A <CFResponse> object containing a parsed HTTP response.
	 */
	public function set_queue_attributes($queue_url, $attribute, $opt = null)
	{
		if (!$opt) $opt = array();
		$opt['QueueUrl'] = $queue_url;
		
		// Required map (non-list)
		$opt = array_merge($opt, CFComplexType::map(array(
			'Attribute' => (is_array($attribute) ? $attribute : array($attribute))
		)));

		return $this->authenticate('SetQueueAttributes', $opt);
	}
}


/*%******************************************************************************************%*/
// EXCEPTIONS

class SQS_Exception extends Exception {}
