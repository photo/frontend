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
 * This is the <em>AWS Security Token Service API Reference</em>. The AWS Security Token Service
 * is a web service that enables you to request temporary, limited-privilege credentials for AWS
 * Identity and Access Management (IAM) users or for users that you authenticate (federated
 * users). This guide provides descriptions of the AWS Security Token Service API as well as links
 * to related content in <a href="http://docs.amazonwebservices.com/IAM/latest/UserGuide/" target=
 * "_blank">Using IAM</a>.
 *  
 * For more detailed information about using this service, go to <a href=
 * "http://docs.amazonwebservices.com/IAM/latest/UserGuide/TokenBasedAuth.html" target=
 * "_blank">Granting Temporary Access to Your AWS Resources</a> in <em>Using IAM</em>.
 *  
 * For specific information about setting up signatures and authorization through the API, go to
 * 	<a href="http://docs.amazonwebservices.com/IAM/latest/UserGuide/IAM_UsingQueryAPI.html" target=
 * "_blank">Making Query Requests</a> in <em>Using IAM</em>.
 *  
 * If you're new to AWS and need additional technical information about a specific AWS product,
 * you can find the product'stechnical documentation at <a href=
 * "http://aws.amazon.com/documentation/" target=
 * "_blank">http://aws.amazon.com/documentation/</a>.
 *  
 * We will refer to Amazon Identity and Access Management using the abbreviated form IAM. All
 * copyrights and legal protections still apply.
 *
 * @version 2012.01.16
 * @license See the included NOTICE.md file for complete information.
 * @copyright See the included NOTICE.md file for complete information.
 * @link http://aws.amazon.com/sts/ Amazon Secure Token Service
 * @link http://aws.amazon.com/sts/documentation/ Amazon Secure Token Service documentation
 */
class AmazonSTS extends CFRuntime
{
	/*%******************************************************************************************%*/
	// CLASS CONSTANTS

	/**
	 * Specify the queue URL for the United States East (Northern Virginia) Region.
	 */
	const REGION_US_E1 = 'sts.amazonaws.com';

	/**
	 * Specify the queue URL for the United States East (Northern Virginia) Region.
	 */
	const REGION_VIRGINIA = self::REGION_US_E1;

	/**
	 * Default service endpoint.
	 */
	const DEFAULT_URL = self::REGION_US_E1;


	/*%******************************************************************************************%*/
	// CONSTRUCTOR

	/**
	 * Constructs a new instance of <AmazonSTS>.
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
		$this->api_version = '2011-06-15';
		$this->hostname = self::DEFAULT_URL;
		$this->auth_class = 'AuthV2Query';

		return parent::__construct($options);
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
	 * The GetFederationToken action returns a set of temporary credentials for a federated user with
	 * the user name and policy specified in the request. The credentials consist of an Access Key ID,
	 * a Secret Access Key, and a security token. The credentials are valid for the specified
	 * duration, between one and 36 hours.
	 *  
	 * The federated user who holds these credentials has any permissions allowed by the intersection
	 * of the specified policy and any resource or user policies that apply to the caller of the
	 * GetFederationToken API, and any resource policies that apply to the federated user's Amazon
	 * Resource Name (ARN). For more information about how token permissions work, see <a href=
	 * "http://docs.amazonwebservices.com/IAM/latest/UserGuide/TokenPermissions.html" target=
	 * "_blank">Controlling Permissions in Temporary Credentials</a> in <em>Using AWS Identity and
	 * Access Management</em>. For information about using GetFederationToken to create temporary
	 * credentials, see <a href=
	 * "http://docs.amazonwebservices.com/IAM/latest/UserGuide/CreatingFedTokens.html" target=
	 * "_blank">Creating Temporary Credentials to Enable Access for Federated Users</a> in <em>Using
	 * AWS Identity and Access Management</em>.
	 *
	 * @param string $name (Required) The name of the federated user associated with the credentials. For information about limitations on user names, go to <a href="http://docs.amazonwebservices.com/IAM/latest/UserGuide/LimitationsOnEntities.html">Limitations on IAM Entities</a> in <em>Using AWS Identity and Access Management</em>. [Constraints: The value must be between 2 and 32 characters, and must match the following regular expression pattern: <code>[\w+=,.@-]*</code>]
	 * @param array $opt (Optional) An associative array of parameters that can have the following keys: <ul>
	 * 	<li><code>Policy</code> - <code>string</code> - Optional - A policy specifying the permissions to associate with the credentials. The caller can delegate their own permissions by specifying a policy, and both policies will be checked when a service call is made. For more information about how permissions work in the context of temporary credentials, see <a href="http://docs.amazonwebservices.com/IAM/latest/UserGuide/TokenPermissions.html" target="_blank">Controlling Permissions in Temporary Credentials</a> in <em>Using AWS Identity and Access Management</em>. [Constraints: The value must be between 1 and 2048 characters, and must match the following regular expression pattern: <code>[\u0009\u000A\u000D\u0020-\u00FF]+</code>]</li>
	 * 	<li><code>DurationSeconds</code> - <code>integer</code> - Optional - The duration, in seconds, that the session should last. Acceptable durations for federation sessions range from 3600s (one hour) to 129600s (36 hours), with 43200s (12 hours) as the default.</li>
	 * 	<li><code>curlopts</code> - <code>array</code> - Optional - A set of values to pass directly into <code>curl_setopt()</code>, where the key is a pre-defined <code>CURLOPT_*</code> constant.</li>
	 * 	<li><code>returnCurlHandle</code> - <code>boolean</code> - Optional - A private toggle specifying that the cURL handle be returned rather than actually completing the request. This toggle is useful for manually managed batch requests.</li></ul>
	 * @return CFResponse A <CFResponse> object containing a parsed HTTP response.
	 */
	public function get_federation_token($name, $opt = null)
	{
		if (!$opt) $opt = array();
		$opt['Name'] = $name;
		
		return $this->authenticate('GetFederationToken', $opt);
	}

	/**
	 * The GetSessionToken action returns a set of temporary credentials for an AWS account or IAM
	 * user. The credentials consist of an Access Key ID, a Secret Access Key, and a security token.
	 * These credentials are valid for the specified duration only. The session duration for IAM users
	 * can be between one and 36 hours, with a default of 12 hours. The session duration for AWS
	 * account owners is restricted to one hour.
	 *  
	 * For more information about using GetSessionToken to create temporary credentials, go to
	 * 	<a href="http://docs.amazonwebservices.com/IAM/latest/UserGuide/CreatingSessionTokens.html"
	 * target="_blank">Creating Temporary Credentials to Enable Access for IAM Users</a> in <em>Using
	 * IAM</em>.
	 *
	 * @param array $opt (Optional) An associative array of parameters that can have the following keys: <ul>
	 * 	<li><code>DurationSeconds</code> - <code>integer</code> - Optional - The duration, in seconds, that the credentials should remain valid. Acceptable durations for IAM user sessions range from 3600s (one hour) to 129600s (36 hours), with 43200s (12 hours) as the default. Sessions for AWS account owners are restricted to a maximum of 3600s (one hour).</li>
	 * 	<li><code>curlopts</code> - <code>array</code> - Optional - A set of values to pass directly into <code>curl_setopt()</code>, where the key is a pre-defined <code>CURLOPT_*</code> constant.</li>
	 * 	<li><code>returnCurlHandle</code> - <code>boolean</code> - Optional - A private toggle specifying that the cURL handle be returned rather than actually completing the request. This toggle is useful for manually managed batch requests.</li></ul>
	 * @return CFResponse A <CFResponse> object containing a parsed HTTP response.
	 */
	public function get_session_token($opt = null)
	{
		if (!$opt) $opt = array();
				
		return $this->authenticate('GetSessionToken', $opt);
	}
}


/*%******************************************************************************************%*/
// EXCEPTIONS

class STS_Exception extends Exception {}
