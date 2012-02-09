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
 * This is the <em>Amazon Simple Notification Service (Amazon SNS) API Reference</em>. This guide
 * provides detailed information about Amazon SNS actions, data types, parameters, and errors. For
 * detailed information about Amazon SNS features and their associated API calls, go to the
 * 	<a href="http://docs.amazonwebservices.com/sns/latest/gsg/">Amazon SNS Getting Started
 * Guide</a>.
 *  
 * Amazon Simple Notification Service is a web service that enables you to build distributed
 * web-enabled applications. Applications can use Amazon SNS to easily push real-time notification
 * messages to interested subscribers over multiple delivery protocols. For more information about
 * this product go to <a href="http://aws.amazon.com/sns/">http://aws.amazon.com/sns</a>.
 *  
 * Use the following links to get started using the <em>Amazon Simple Notification Service API
 * Reference</em>:
 * 
 * <ul>
 * 	<li><a href="http://docs.amazonwebservices.com/sns/latest/api/API_Operations.html">Actions</a>:
 * 	An alphabetical list of all Amazon SNS actions.</li>
 * 	<li><a href="http://docs.amazonwebservices.com/sns/latest/api/API_Types.html">Data Types</a>:
 * 	An alphabetical list of all Amazon SNS data types.</li>
 * 	<li><a href="http://docs.amazonwebservices.com/sns/latest/api/CommonParameters.html">Common
 * 	Parameters</a>: Parameters that all Query actions can use.</li>
 * 	<li><a href="http://docs.amazonwebservices.com/sns/latest/api/CommonErrors.html">Common
 * 	Errors</a>: Client and server errors that all actions can return.</li>
 * 	<li><a href="http://docs.amazonwebservices.com/general/latest/gr/index.html?rande.html">Regions
 * 	and Endpoints</a>: Itemized regions and endpoints for all AWS products.</li>
 * 	<li><a href=
 * 	"http://sns.us-east-1.amazonaws.com/doc/2010-03-31/SimpleNotificationService.wsdl">WSDL
 * 	Location</a>:
 * 	http://sns.us-east-1.amazonaws.com/doc/2010-03-31/SimpleNotificationService.wsdl</li>
 * </ul>
 *
 * @version 2012.01.16
 * @license See the included NOTICE.md file for complete information.
 * @copyright See the included NOTICE.md file for complete information.
 * @link http://aws.amazon.com/sns/ Amazon Simple Notification Service
 * @link http://aws.amazon.com/sns/documentation/ Amazon Simple Notification Service documentation
 */
class AmazonSNS extends CFRuntime
{
	/*%******************************************************************************************%*/
	// CLASS CONSTANTS

	/**
	 * Specify the queue URL for the United States East (Northern Virginia) Region.
	 */
	const REGION_US_E1 = 'sns.us-east-1.amazonaws.com';

	/**
	 * Specify the queue URL for the United States East (Northern Virginia) Region.
	 */
	const REGION_VIRGINIA = self::REGION_US_E1;

	/**
	 * Specify the queue URL for the United States West (Northern California) Region.
	 */
	const REGION_US_W1 = 'sns.us-west-1.amazonaws.com';

	/**
	 * Specify the queue URL for the United States West (Northern California) Region.
	 */
	const REGION_CALIFORNIA = self::REGION_US_W1;

	/**
	 * Specify the queue URL for the United States West (Oregon) Region.
	 */
	const REGION_US_W2 = 'sns.us-west-2.amazonaws.com';

	/**
	 * Specify the queue URL for the United States West (Oregon) Region.
	 */
	const REGION_OREGON = self::REGION_US_W2;

	/**
	 * Specify the queue URL for the Europe West (Ireland) Region.
	 */
	const REGION_EU_W1 = 'sns.eu-west-1.amazonaws.com';

	/**
	 * Specify the queue URL for the Europe West (Ireland) Region.
	 */
	const REGION_IRELAND = self::REGION_EU_W1;

	/**
	 * Specify the queue URL for the Asia Pacific Southeast (Singapore) Region.
	 */
	const REGION_APAC_SE1 = 'sns.ap-southeast-1.amazonaws.com';

	/**
	 * Specify the queue URL for the Asia Pacific Southeast (Singapore) Region.
	 */
	const REGION_SINGAPORE = self::REGION_APAC_SE1;

	/**
	 * Specify the queue URL for the Asia Pacific Northeast (Tokyo) Region.
	 */
	const REGION_APAC_NE1 = 'sns.ap-northeast-1.amazonaws.com';

	/**
	 * Specify the queue URL for the Asia Pacific Northeast (Tokyo) Region.
	 */
	const REGION_TOKYO = self::REGION_APAC_NE1;

	/**
	 * Specify the queue URL for the South America (Sao Paulo) Region.
	 */
	const REGION_SA_E1 = 'sns.sa-east-1.amazonaws.com';

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
	 * Constructs a new instance of <AmazonSNS>.
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
		$this->api_version = '2010-03-31';
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
	 * Gets a simple list of Topic ARNs.
	 *
	 * @param string $pcre (Optional) A Perl-Compatible Regular Expression (PCRE) to filter the names against.
	 * @return array A list of Topic ARNs.
	 * @link http://php.net/pcre Perl-Compatible Regular Expression (PCRE) Docs
	 */
	public function get_topic_list($pcre = null)
	{
		if ($this->use_batch_flow)
		{
			throw new SNS_Exception(__FUNCTION__ . '() cannot be batch requested');
		}

		// Get a list of topics.
		$list = $this->list_topics();
		if ($list = $list->body->TopicArn())
		{
			$list = $list->map_string($pcre);
			return $list;
		}

		return array();
	}


	/*%******************************************************************************************%*/
	// SERVICE METHODS

	/**
	 * The AddPermission action adds a statement to a topic's access control policy, granting access
	 * for the specified AWS accounts to the specified actions.
	 *
	 * @param string $topic_arn (Required) The ARN of the topic whose access control policy you wish to modify.
	 * @param string $label (Required) A unique identifier for the new policy statement.
	 * @param string|array $aws_account_id (Required) The AWS account IDs of the users (principals) who will be given access to the specified actions. The users must have AWS accounts, but do not need to be signed up for this service. Pass a string for a single value, or an indexed array for multiple values.
	 * @param string|array $action_name (Required) The action you want to allow for the specified principal(s). Valid values: any Amazon SNS action name. Pass a string for a single value, or an indexed array for multiple values.
	 * @param array $opt (Optional) An associative array of parameters that can have the following keys: <ul>
	 * 	<li><code>curlopts</code> - <code>array</code> - Optional - A set of values to pass directly into <code>curl_setopt()</code>, where the key is a pre-defined <code>CURLOPT_*</code> constant.</li>
	 * 	<li><code>returnCurlHandle</code> - <code>boolean</code> - Optional - A private toggle specifying that the cURL handle be returned rather than actually completing the request. This toggle is useful for manually managed batch requests.</li></ul>
	 * @return CFResponse A <CFResponse> object containing a parsed HTTP response.
	 */
	public function add_permission($topic_arn, $label, $aws_account_id, $action_name, $opt = null)
	{
		if (!$opt) $opt = array();
		$opt['TopicArn'] = $topic_arn;
		$opt['Label'] = $label;
		
		// Required list (non-map)
		$opt = array_merge($opt, CFComplexType::map(array(
			'AWSAccountId' => (is_array($aws_account_id) ? $aws_account_id : array($aws_account_id))
		), 'member'));
		
		// Required list (non-map)
		$opt = array_merge($opt, CFComplexType::map(array(
			'ActionName' => (is_array($action_name) ? $action_name : array($action_name))
		), 'member'));

		return $this->authenticate('AddPermission', $opt);
	}

	/**
	 * The ConfirmSubscription action verifies an endpoint owner's intent to receive messages by
	 * validating the token sent to the endpoint by an earlier Subscribe action. If the token is
	 * valid, the action creates a new subscription and returns its Amazon Resource Name (ARN). This
	 * call requires an AWS signature only when the AuthenticateOnUnsubscribe flag is set to "true".
	 *
	 * @param string $topic_arn (Required) The ARN of the topic for which you wish to confirm a subscription.
	 * @param string $token (Required) Short-lived token sent to an endpoint during the Subscribe action.
	 * @param array $opt (Optional) An associative array of parameters that can have the following keys: <ul>
	 * 	<li><code>AuthenticateOnUnsubscribe</code> - <code>string</code> - Optional - Disallows unauthenticated unsubscribes of the subscription. If the value of this parameter is <code>true</code> and the request has an AWS signature, then only the topic owner and the subscription owner can unsubscribe the endpoint. The unsubscribe action will require AWS authentication.</li>
	 * 	<li><code>curlopts</code> - <code>array</code> - Optional - A set of values to pass directly into <code>curl_setopt()</code>, where the key is a pre-defined <code>CURLOPT_*</code> constant.</li>
	 * 	<li><code>returnCurlHandle</code> - <code>boolean</code> - Optional - A private toggle specifying that the cURL handle be returned rather than actually completing the request. This toggle is useful for manually managed batch requests.</li></ul>
	 * @return CFResponse A <CFResponse> object containing a parsed HTTP response.
	 */
	public function confirm_subscription($topic_arn, $token, $opt = null)
	{
		if (!$opt) $opt = array();
		$opt['TopicArn'] = $topic_arn;
		$opt['Token'] = $token;
		
		return $this->authenticate('ConfirmSubscription', $opt);
	}

	/**
	 * The CreateTopic action creates a topic to which notifications can be published. Users can
	 * create at most 25 topics. This action is idempotent, so if the requester already owns a topic
	 * with the specified name, that topic's ARN will be returned without creating a new topic.
	 *
	 * @param string $name (Required) The name of the topic you want to create. Constraints: Topic names must be made up of only uppercase and lowercase ASCII letters, numbers, and hyphens, and must be between 1 and 256 characters long.
	 * @param array $opt (Optional) An associative array of parameters that can have the following keys: <ul>
	 * 	<li><code>curlopts</code> - <code>array</code> - Optional - A set of values to pass directly into <code>curl_setopt()</code>, where the key is a pre-defined <code>CURLOPT_*</code> constant.</li>
	 * 	<li><code>returnCurlHandle</code> - <code>boolean</code> - Optional - A private toggle specifying that the cURL handle be returned rather than actually completing the request. This toggle is useful for manually managed batch requests.</li></ul>
	 * @return CFResponse A <CFResponse> object containing a parsed HTTP response.
	 */
	public function create_topic($name, $opt = null)
	{
		if (!$opt) $opt = array();
		$opt['Name'] = $name;
		
		return $this->authenticate('CreateTopic', $opt);
	}

	/**
	 * The DeleteTopic action deletes a topic and all its subscriptions. Deleting a topic might
	 * prevent some messages previously sent to the topic from being delivered to subscribers. This
	 * action is idempotent, so deleting a topic that does not exist will not result in an error.
	 *
	 * @param string $topic_arn (Required) The ARN of the topic you want to delete.
	 * @param array $opt (Optional) An associative array of parameters that can have the following keys: <ul>
	 * 	<li><code>curlopts</code> - <code>array</code> - Optional - A set of values to pass directly into <code>curl_setopt()</code>, where the key is a pre-defined <code>CURLOPT_*</code> constant.</li>
	 * 	<li><code>returnCurlHandle</code> - <code>boolean</code> - Optional - A private toggle specifying that the cURL handle be returned rather than actually completing the request. This toggle is useful for manually managed batch requests.</li></ul>
	 * @return CFResponse A <CFResponse> object containing a parsed HTTP response.
	 */
	public function delete_topic($topic_arn, $opt = null)
	{
		if (!$opt) $opt = array();
		$opt['TopicArn'] = $topic_arn;
		
		return $this->authenticate('DeleteTopic', $opt);
	}

	/**
	 * The GetSubscriptionAttribtues action returns all of the properties of a subscription.
	 *
	 * @param string $subscription_arn (Required) The ARN of the subscription whose properties you want to get.
	 * @param array $opt (Optional) An associative array of parameters that can have the following keys: <ul>
	 * 	<li><code>curlopts</code> - <code>array</code> - Optional - A set of values to pass directly into <code>curl_setopt()</code>, where the key is a pre-defined <code>CURLOPT_*</code> constant.</li>
	 * 	<li><code>returnCurlHandle</code> - <code>boolean</code> - Optional - A private toggle specifying that the cURL handle be returned rather than actually completing the request. This toggle is useful for manually managed batch requests.</li></ul>
	 * @return CFResponse A <CFResponse> object containing a parsed HTTP response.
	 */
	public function get_subscription_attributes($subscription_arn, $opt = null)
	{
		if (!$opt) $opt = array();
		$opt['SubscriptionArn'] = $subscription_arn;
		
		return $this->authenticate('GetSubscriptionAttributes', $opt);
	}

	/**
	 * The GetTopicAttribtues action returns all of the properties of a topic customers have created.
	 * Topic properties returned might differ based on the authorization of the user.
	 *
	 * @param string $topic_arn (Required) The ARN of the topic whose properties you want to get.
	 * @param array $opt (Optional) An associative array of parameters that can have the following keys: <ul>
	 * 	<li><code>curlopts</code> - <code>array</code> - Optional - A set of values to pass directly into <code>curl_setopt()</code>, where the key is a pre-defined <code>CURLOPT_*</code> constant.</li>
	 * 	<li><code>returnCurlHandle</code> - <code>boolean</code> - Optional - A private toggle specifying that the cURL handle be returned rather than actually completing the request. This toggle is useful for manually managed batch requests.</li></ul>
	 * @return CFResponse A <CFResponse> object containing a parsed HTTP response.
	 */
	public function get_topic_attributes($topic_arn, $opt = null)
	{
		if (!$opt) $opt = array();
		$opt['TopicArn'] = $topic_arn;
		
		return $this->authenticate('GetTopicAttributes', $opt);
	}

	/**
	 * The ListSubscriptions action returns a list of the requester's subscriptions. Each call returns
	 * a limited list of subscriptions, up to 100. If there are more subscriptions, a NextToken is
	 * also returned. Use the NextToken parameter in a new ListSubscriptions call to get further
	 * results.
	 *
	 * @param array $opt (Optional) An associative array of parameters that can have the following keys: <ul>
	 * 	<li><code>NextToken</code> - <code>string</code> - Optional - Token returned by the previous ListSubscriptions request.</li>
	 * 	<li><code>curlopts</code> - <code>array</code> - Optional - A set of values to pass directly into <code>curl_setopt()</code>, where the key is a pre-defined <code>CURLOPT_*</code> constant.</li>
	 * 	<li><code>returnCurlHandle</code> - <code>boolean</code> - Optional - A private toggle specifying that the cURL handle be returned rather than actually completing the request. This toggle is useful for manually managed batch requests.</li></ul>
	 * @return CFResponse A <CFResponse> object containing a parsed HTTP response.
	 */
	public function list_subscriptions($opt = null)
	{
		if (!$opt) $opt = array();
				
		return $this->authenticate('ListSubscriptions', $opt);
	}

	/**
	 * The ListSubscriptionsByTopic action returns a list of the subscriptions to a specific topic.
	 * Each call returns a limited list of subscriptions, up to 100. If there are more subscriptions,
	 * a NextToken is also returned. Use the NextToken parameter in a new ListSubscriptionsByTopic
	 * call to get further results.
	 *
	 * @param string $topic_arn (Required) The ARN of the topic for which you wish to find subscriptions.
	 * @param array $opt (Optional) An associative array of parameters that can have the following keys: <ul>
	 * 	<li><code>NextToken</code> - <code>string</code> - Optional - Token returned by the previous ListSubscriptionsByTopic request.</li>
	 * 	<li><code>curlopts</code> - <code>array</code> - Optional - A set of values to pass directly into <code>curl_setopt()</code>, where the key is a pre-defined <code>CURLOPT_*</code> constant.</li>
	 * 	<li><code>returnCurlHandle</code> - <code>boolean</code> - Optional - A private toggle specifying that the cURL handle be returned rather than actually completing the request. This toggle is useful for manually managed batch requests.</li></ul>
	 * @return CFResponse A <CFResponse> object containing a parsed HTTP response.
	 */
	public function list_subscriptions_by_topic($topic_arn, $opt = null)
	{
		if (!$opt) $opt = array();
		$opt['TopicArn'] = $topic_arn;
		
		return $this->authenticate('ListSubscriptionsByTopic', $opt);
	}

	/**
	 * The ListTopics action returns a list of the requester's topics. Each call returns a limited
	 * list of topics, up to 100. If there are more topics, a NextToken is also returned. Use the
	 * NextToken parameter in a new ListTopics call to get further results.
	 *
	 * @param array $opt (Optional) An associative array of parameters that can have the following keys: <ul>
	 * 	<li><code>NextToken</code> - <code>string</code> - Optional - Token returned by the previous ListTopics request.</li>
	 * 	<li><code>curlopts</code> - <code>array</code> - Optional - A set of values to pass directly into <code>curl_setopt()</code>, where the key is a pre-defined <code>CURLOPT_*</code> constant.</li>
	 * 	<li><code>returnCurlHandle</code> - <code>boolean</code> - Optional - A private toggle specifying that the cURL handle be returned rather than actually completing the request. This toggle is useful for manually managed batch requests.</li></ul>
	 * @return CFResponse A <CFResponse> object containing a parsed HTTP response.
	 */
	public function list_topics($opt = null)
	{
		if (!$opt) $opt = array();
				
		return $this->authenticate('ListTopics', $opt);
	}

	/**
	 * The Publish action sends a message to all of a topic's subscribed endpoints. When a messageId
	 * is returned, the message has been saved and Amazon SNS will attempt to deliver it to the
	 * topic's subscribers shortly. The format of the outgoing message to each subscribed endpoint
	 * depends on the notification protocol selected.
	 *
	 * @param string $topic_arn (Required) The topic you want to publish to.
	 * @param string $message (Required) The message you want to send to the topic. Constraints: Messages must be UTF-8 encoded strings at most 8 KB in size (8192 bytes, not 8192 characters).
	 * @param array $opt (Optional) An associative array of parameters that can have the following keys: <ul>
	 * 	<li><code>Subject</code> - <code>string</code> - Optional - Optional parameter to be used as the "Subject" line of when the message is delivered to e-mail endpoints. This field will also be included, if present, in the standard JSON messages delivered to other endpoints. Constraints: Subjects must be ASCII text that begins with a letter, number or punctuation mark; must not include line breaks or control characters; and must be less than 100 characters long.</li>
	 * 	<li><code>MessageStructure</code> - <code>string</code> - Optional - Optional parameter. It will have one valid value: "json". If this option, Message is present and set to "json", the value of Message must: be a syntactically valid JSON object. It must contain at least a top level JSON key of "default" with a value that is a string. For any other top level key that matches one of our transport protocols (e.g. "http"), then the corresponding value (if it is a string) will be used for the message published for that protocol Constraints: Keys in the JSON object that correspond to supported transport protocols must have simple JSON string values. The values will be parsed (unescaped) before they are used in outgoing messages. Typically, outbound notifications are JSON encoded (meaning, the characters will be reescaped for sending). JSON strings are UTF-8. Values have a minimum length of 0 (the empty string, "", is allowed). Values have a maximum length bounded by the overall message size (so, including multiple protocols may limit message sizes). Non-string values will cause the key to be ignored. Keys that do not correspond to supported transport protocols will be ignored. Duplicate keys are not allowed. Failure to parse or validate any key or value in the message will cause the Publish call to return an error (no partial delivery).</li>
	 * 	<li><code>curlopts</code> - <code>array</code> - Optional - A set of values to pass directly into <code>curl_setopt()</code>, where the key is a pre-defined <code>CURLOPT_*</code> constant.</li>
	 * 	<li><code>returnCurlHandle</code> - <code>boolean</code> - Optional - A private toggle specifying that the cURL handle be returned rather than actually completing the request. This toggle is useful for manually managed batch requests.</li></ul>
	 * @return CFResponse A <CFResponse> object containing a parsed HTTP response.
	 */
	public function publish($topic_arn, $message, $opt = null)
	{
		if (!$opt) $opt = array();
		$opt['TopicArn'] = $topic_arn;
		$opt['Message'] = $message;
		
		return $this->authenticate('Publish', $opt);
	}

	/**
	 * The RemovePermission action removes a statement from a topic's access control policy.
	 *
	 * @param string $topic_arn (Required) The ARN of the topic whose access control policy you wish to modify.
	 * @param string $label (Required) The unique label of the statement you want to remove.
	 * @param array $opt (Optional) An associative array of parameters that can have the following keys: <ul>
	 * 	<li><code>curlopts</code> - <code>array</code> - Optional - A set of values to pass directly into <code>curl_setopt()</code>, where the key is a pre-defined <code>CURLOPT_*</code> constant.</li>
	 * 	<li><code>returnCurlHandle</code> - <code>boolean</code> - Optional - A private toggle specifying that the cURL handle be returned rather than actually completing the request. This toggle is useful for manually managed batch requests.</li></ul>
	 * @return CFResponse A <CFResponse> object containing a parsed HTTP response.
	 */
	public function remove_permission($topic_arn, $label, $opt = null)
	{
		if (!$opt) $opt = array();
		$opt['TopicArn'] = $topic_arn;
		$opt['Label'] = $label;
		
		return $this->authenticate('RemovePermission', $opt);
	}

	/**
	 * The SetSubscriptionAttributes action allows a subscription owner to set an attribute of the
	 * topic to a new value.
	 *
	 * @param string $subscription_arn (Required) The ARN of the subscription to modify.
	 * @param string $attribute_name (Required) The name of the attribute you want to set. Only a subset of the subscriptions attributes are mutable. Valid values: DeliveryPolicy
	 * @param string $attribute_value (Required) The new value for the attribute.
	 * @param array $opt (Optional) An associative array of parameters that can have the following keys: <ul>
	 * 	<li><code>curlopts</code> - <code>array</code> - Optional - A set of values to pass directly into <code>curl_setopt()</code>, where the key is a pre-defined <code>CURLOPT_*</code> constant.</li>
	 * 	<li><code>returnCurlHandle</code> - <code>boolean</code> - Optional - A private toggle specifying that the cURL handle be returned rather than actually completing the request. This toggle is useful for manually managed batch requests.</li></ul>
	 * @return CFResponse A <CFResponse> object containing a parsed HTTP response.
	 */
	public function set_subscription_attributes($subscription_arn, $attribute_name, $attribute_value, $opt = null)
	{
		if (!$opt) $opt = array();
		$opt['SubscriptionArn'] = $subscription_arn;
		$opt['AttributeName'] = $attribute_name;
		$opt['AttributeValue'] = $attribute_value;
		
		return $this->authenticate('SetSubscriptionAttributes', $opt);
	}

	/**
	 * The SetTopicAttributes action allows a topic owner to set an attribute of the topic to a new
	 * value.
	 *
	 * @param string $topic_arn (Required) The ARN of the topic to modify.
	 * @param string $attribute_name (Required) The name of the attribute you want to set. Only a subset of the topic's attributes are mutable. Valid values: Policy | DisplayName
	 * @param string $attribute_value (Required) The new value for the attribute.
	 * @param array $opt (Optional) An associative array of parameters that can have the following keys: <ul>
	 * 	<li><code>curlopts</code> - <code>array</code> - Optional - A set of values to pass directly into <code>curl_setopt()</code>, where the key is a pre-defined <code>CURLOPT_*</code> constant.</li>
	 * 	<li><code>returnCurlHandle</code> - <code>boolean</code> - Optional - A private toggle specifying that the cURL handle be returned rather than actually completing the request. This toggle is useful for manually managed batch requests.</li></ul>
	 * @return CFResponse A <CFResponse> object containing a parsed HTTP response.
	 */
	public function set_topic_attributes($topic_arn, $attribute_name, $attribute_value, $opt = null)
	{
		if (!$opt) $opt = array();
		$opt['TopicArn'] = $topic_arn;
		$opt['AttributeName'] = $attribute_name;
		$opt['AttributeValue'] = $attribute_value;
		
		return $this->authenticate('SetTopicAttributes', $opt);
	}

	/**
	 * The Subscribe action prepares to subscribe an endpoint by sending the endpoint a confirmation
	 * message. To actually create a subscription, the endpoint owner must call the
	 * ConfirmSubscription action with the token from the confirmation message. Confirmation tokens
	 * are valid for three days.
	 *
	 * @param string $topic_arn (Required) The ARN of topic you want to subscribe to.
	 * @param string $protocol (Required) The protocol you want to use. Supported protocols include:<ul><li>http -- delivery of JSON-encoded message via HTTP POST</li><li>https -- delivery of JSON-encoded message via HTTPS POST</li><li>email -- delivery of message via SMTP</li><li>email-json -- delivery of JSON-encoded message via SMTP</li><li>sms -- delivery of message via SMS</li><li>sqs -- delivery of JSON-encoded message to an Amazon SQS queue</li></ul>
	 * @param string $endpoint (Required) The endpoint that you want to receive notifications. Endpoints vary by protocol:<ul><li>For the http protocol, the endpoint is an URL beginning with "http://"</li><li>For the https protocol, the endpoint is a URL beginning with "https://"</li><li>For the email protocol, the endpoint is an e-mail address</li><li>For the email-json protocol, the endpoint is an e-mail address</li><li>For the sms protocol, the endpoint is a phone number of an SMS-enabled device</li><li>For the sqs protocol, the endpoint is the ARN of an Amazon SQS queue</li></ul>
	 * @param array $opt (Optional) An associative array of parameters that can have the following keys: <ul>
	 * 	<li><code>curlopts</code> - <code>array</code> - Optional - A set of values to pass directly into <code>curl_setopt()</code>, where the key is a pre-defined <code>CURLOPT_*</code> constant.</li>
	 * 	<li><code>returnCurlHandle</code> - <code>boolean</code> - Optional - A private toggle specifying that the cURL handle be returned rather than actually completing the request. This toggle is useful for manually managed batch requests.</li></ul>
	 * @return CFResponse A <CFResponse> object containing a parsed HTTP response.
	 */
	public function subscribe($topic_arn, $protocol, $endpoint, $opt = null)
	{
		if (!$opt) $opt = array();
		$opt['TopicArn'] = $topic_arn;
		$opt['Protocol'] = $protocol;
		$opt['Endpoint'] = $endpoint;
		
		return $this->authenticate('Subscribe', $opt);
	}

	/**
	 * The Unsubscribe action deletes a subscription. If the subscription requires authentication for
	 * deletion, only the owner of the subscription or the its topic's owner can unsubscribe, and an
	 * AWS signature is required. If the Unsubscribe call does not require authentication and the
	 * requester is not the subscription owner, a final cancellation message is delivered to the
	 * endpoint, so that the endpoint owner can easily resubscribe to the topic if the Unsubscribe
	 * request was unintended.
	 *
	 * @param string $subscription_arn (Required) The ARN of the subscription to be deleted.
	 * @param array $opt (Optional) An associative array of parameters that can have the following keys: <ul>
	 * 	<li><code>curlopts</code> - <code>array</code> - Optional - A set of values to pass directly into <code>curl_setopt()</code>, where the key is a pre-defined <code>CURLOPT_*</code> constant.</li>
	 * 	<li><code>returnCurlHandle</code> - <code>boolean</code> - Optional - A private toggle specifying that the cURL handle be returned rather than actually completing the request. This toggle is useful for manually managed batch requests.</li></ul>
	 * @return CFResponse A <CFResponse> object containing a parsed HTTP response.
	 */
	public function unsubscribe($subscription_arn, $opt = null)
	{
		if (!$opt) $opt = array();
		$opt['SubscriptionArn'] = $subscription_arn;
		
		return $this->authenticate('Unsubscribe', $opt);
	}
}


/*%******************************************************************************************%*/
// EXCEPTIONS

class SNS_Exception extends Exception {}
