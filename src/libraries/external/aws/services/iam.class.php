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
 * This is the Amazon Web Services (AWS) Identity and Access Management (IAM) API Reference. This
 * guide provides descriptions of the IAM API as well as links to related content in the guide,
 * 	<a href="http://docs.amazonwebservices.com/IAM/latest/UserGuide/" target="_blank">Using
 * IAM</a>.
 *  
 * IAM is a web service that enables AWS customers to manage users and user permissions under
 * their AWS account. For more information about this product go to <a href=
 * "http://aws.amazon.com/iam/" target="_blank">AWS Identity and Access Management (IAM)</a>. For
 * specific information about setting up signatures and authorization through the API, go to
 * 	<a href="http://docs.amazonwebservices.com/IAM/latest/UserGuide/IAM_UsingQueryAPI.html" target=
 * "_blank">Making Query Requests</a> in <em>Using AWS Identity and Access Management</em>.
 *  
 * If you're new to AWS and need additional technical information about a specific AWS product,
 * you can find the product'stechnical documentation at <a href=
 * "http://aws.amazon.com/documentation/" target=
 * "_blank">http://aws.amazon.com/documentation/</a>.
 *  
 * We will refer to Amazon AWS Identity and Access Management using the abbreviated form IAM. All
 * copyrights and legal protections still apply.
 *
 * @version 2011.12.13
 * @license See the included NOTICE.md file for complete information.
 * @copyright See the included NOTICE.md file for complete information.
 * @link http://aws.amazon.com/iam/ AWS Identity and Access Management
 * @link http://aws.amazon.com/iam/documentation/ AWS Identity and Access Management documentation
 */
class AmazonIAM extends CFRuntime
{
	/*%******************************************************************************************%*/
	// CLASS CONSTANTS

	/**
	 * Specify the queue URL for the United States East (Northern Virginia) Region.
	 */
	const REGION_US_E1 = 'iam.amazonaws.com';

	/**
	 * Specify the queue URL for the United States East (Northern Virginia) Region.
	 */
	const REGION_VIRGINIA = self::REGION_US_E1;

	/**
	 * Specify the queue URL for the United States GovCloud Region.
	 */
	const REGION_US_GOV1 = 'iam.us-gov.amazonaws.com';

	/**
	 * Default service endpoint.
	 */
	const DEFAULT_URL = self::REGION_US_E1;


	/*%******************************************************************************************%*/
	// CONSTRUCTOR

	/**
	 * Constructs a new instance of <AmazonIAM>.
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
		$this->api_version = '2010-05-08';
		$this->hostname = self::DEFAULT_URL;
		$this->auth_class = 'AuthV2Query';

		return parent::__construct($options);
	}


	/*%******************************************************************************************%*/
	// SETTERS

	/**
	 * This allows you to explicitly sets the region for the service to use.
	 *
	 * @param string $region (Required) The region to explicitly set. Available options are <REGION_US_E1>, <REGION_US_GOV1>.
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
	 * Adds the specified user to the specified group.
	 *
	 * @param string $group_name (Required) Name of the group to update. [Constraints: The value must be between 1 and 128 characters, and must match the following regular expression pattern: <code>[\w+=,.@-]*</code>]
	 * @param string $user_name (Required) Name of the user to add. [Constraints: The value must be between 1 and 64 characters, and must match the following regular expression pattern: <code>[\w+=,.@-]*</code>]
	 * @param array $opt (Optional) An associative array of parameters that can have the following keys: <ul>
	 * 	<li><code>curlopts</code> - <code>array</code> - Optional - A set of values to pass directly into <code>curl_setopt()</code>, where the key is a pre-defined <code>CURLOPT_*</code> constant.</li>
	 * 	<li><code>returnCurlHandle</code> - <code>boolean</code> - Optional - A private toggle specifying that the cURL handle be returned rather than actually completing the request. This toggle is useful for manually managed batch requests.</li></ul>
	 * @return CFResponse A <CFResponse> object containing a parsed HTTP response.
	 */
	public function add_user_to_group($group_name, $user_name, $opt = null)
	{
		if (!$opt) $opt = array();
		$opt['GroupName'] = $group_name;
		$opt['UserName'] = $user_name;
		
		return $this->authenticate('AddUserToGroup', $opt);
	}

	/**
	 * Creates a new AWS Secret Access Key and corresponding AWS Access Key ID for the specified user.
	 * The default status for new keys is <code>Active</code>.
	 *  
	 * If you do not specify a user name, IAM determines the user name implicitly based on the AWS
	 * Access Key ID signing the request. Because this action works for access keys under the AWS
	 * account, you can use this API to manage root credentials even if the AWS account has no
	 * associated users.
	 *  
	 * For information about limits on the number of keys you can create, see <a href=
	 * "http://docs.amazonwebservices.com/IAM/latest/UserGuide/index.html?LimitationsOnEntities.html"
	 * target="_blank">Limitations on IAM Entities</a> in <em>Using AWS Identity and Access
	 * Management</em>.
	 * 
	 * <p class="important">
	 * To ensure the security of your AWS account, the Secret Access Key is accessible only during key
	 * and user creation. You must save the key (for example, in a text file) if you want to be able
	 * to access it again. If a secret key is lost, you can delete the access keys for the associated
	 * user and then create new keys.
	 * </p>
	 *
	 * @param array $opt (Optional) An associative array of parameters that can have the following keys: <ul>
	 * 	<li><code>UserName</code> - <code>string</code> - Optional - The user name that the new key will belong to. [Constraints: The value must be between 1 and 64 characters, and must match the following regular expression pattern: <code>[\w+=,.@-]*</code>]</li>
	 * 	<li><code>curlopts</code> - <code>array</code> - Optional - A set of values to pass directly into <code>curl_setopt()</code>, where the key is a pre-defined <code>CURLOPT_*</code> constant.</li>
	 * 	<li><code>returnCurlHandle</code> - <code>boolean</code> - Optional - A private toggle specifying that the cURL handle be returned rather than actually completing the request. This toggle is useful for manually managed batch requests.</li></ul>
	 * @return CFResponse A <CFResponse> object containing a parsed HTTP response.
	 */
	public function create_access_key($opt = null)
	{
		if (!$opt) $opt = array();
				
		return $this->authenticate('CreateAccessKey', $opt);
	}

	/**
	 * This action creates an alias for your AWS account. For information about using an AWS account
	 * alias, see <a href="http://docs.amazonwebservices.com/IAM/latest/UserGuide/AccountAlias.html"
	 * target="_blank">Using an Alias for Your AWS Account ID</a> in <em>Using AWS Identity and Access
	 * Management</em>.
	 *
	 * @param string $account_alias (Required) Name of the account alias to create. [Constraints: The value must be between 3 and 63 characters, and must match the following regular expression pattern: <code>^[a-z0-9](([a-z0-9]|-(?!-))*[a-z0-9])?$</code>]
	 * @param array $opt (Optional) An associative array of parameters that can have the following keys: <ul>
	 * 	<li><code>curlopts</code> - <code>array</code> - Optional - A set of values to pass directly into <code>curl_setopt()</code>, where the key is a pre-defined <code>CURLOPT_*</code> constant.</li>
	 * 	<li><code>returnCurlHandle</code> - <code>boolean</code> - Optional - A private toggle specifying that the cURL handle be returned rather than actually completing the request. This toggle is useful for manually managed batch requests.</li></ul>
	 * @return CFResponse A <CFResponse> object containing a parsed HTTP response.
	 */
	public function create_account_alias($account_alias, $opt = null)
	{
		if (!$opt) $opt = array();
		$opt['AccountAlias'] = $account_alias;
		
		return $this->authenticate('CreateAccountAlias', $opt);
	}

	/**
	 * Creates a new group.
	 *  
	 * For information about the number of groups you can create, see <a href=
	 * "http://docs.amazonwebservices.com/IAM/latest/UserGuide/index.html?LimitationsOnEntities.html"
	 * target="_blank">Limitations on IAM Entities</a> in <em>Using AWS Identity and Access
	 * Management</em>.
	 *
	 * @param string $group_name (Required) Name of the group to create. Do not include the path in this value. [Constraints: The value must be between 1 and 128 characters, and must match the following regular expression pattern: <code>[\w+=,.@-]*</code>]
	 * @param array $opt (Optional) An associative array of parameters that can have the following keys: <ul>
	 * 	<li><code>Path</code> - <code>string</code> - Optional - The path to the group. For more information about paths, see <a href="http://docs.amazonwebservices.com/IAM/latest/UserGuide/index.html?Using_Identifiers.html" target="_blank">Identifiers for IAM Entities</a> in <em>Using AWS Identity and Access Management</em>. This parameter is optional. If it is not included, it defaults to a slash (/). [Constraints: The value must be between 1 and 512 characters, and must match the following regular expression pattern: <code>(\u002F)|(\u002F[\u0021-\u007F]+\u002F)</code>]</li>
	 * 	<li><code>curlopts</code> - <code>array</code> - Optional - A set of values to pass directly into <code>curl_setopt()</code>, where the key is a pre-defined <code>CURLOPT_*</code> constant.</li>
	 * 	<li><code>returnCurlHandle</code> - <code>boolean</code> - Optional - A private toggle specifying that the cURL handle be returned rather than actually completing the request. This toggle is useful for manually managed batch requests.</li></ul>
	 * @return CFResponse A <CFResponse> object containing a parsed HTTP response.
	 */
	public function create_group($group_name, $opt = null)
	{
		if (!$opt) $opt = array();
		$opt['GroupName'] = $group_name;
		
		return $this->authenticate('CreateGroup', $opt);
	}

	/**
	 * Creates a login profile for the specified user, giving the user the ability to access AWS
	 * services such as the AWS Management Console. For more information about login profiles, see
	 * 	<a href=
	 * "http://docs.amazonwebservices.com/IAM/latest/UserGuide/index.html?Using_ManagingLogins.html"
	 * target="_blank">Creating or Deleting a User Login Profile</a> in <em>Using AWS Identity and
	 * Access Management</em>.
	 *
	 * @param string $user_name (Required) Name of the user to create a login profile for. [Constraints: The value must be between 1 and 64 characters, and must match the following regular expression pattern: <code>[\w+=,.@-]*</code>]
	 * @param string $password (Required) The new password for the user name. [Constraints: The value must be between 1 and 128 characters, and must match the following regular expression pattern: <code>[\u0009\u000A\u000D\u0020-\u00FF]+</code>]
	 * @param array $opt (Optional) An associative array of parameters that can have the following keys: <ul>
	 * 	<li><code>curlopts</code> - <code>array</code> - Optional - A set of values to pass directly into <code>curl_setopt()</code>, where the key is a pre-defined <code>CURLOPT_*</code> constant.</li>
	 * 	<li><code>returnCurlHandle</code> - <code>boolean</code> - Optional - A private toggle specifying that the cURL handle be returned rather than actually completing the request. This toggle is useful for manually managed batch requests.</li></ul>
	 * @return CFResponse A <CFResponse> object containing a parsed HTTP response.
	 */
	public function create_login_profile($user_name, $password, $opt = null)
	{
		if (!$opt) $opt = array();
		$opt['UserName'] = $user_name;
		$opt['Password'] = $password;
		
		return $this->authenticate('CreateLoginProfile', $opt);
	}

	/**
	 * Creates a new user for your AWS account.
	 *  
	 * For information about limitations on the number of users you can create, see <a href=
	 * "http://docs.amazonwebservices.com/IAM/latest/UserGuide/index.html?LimitationsOnEntities.html"
	 * target="_blank">Limitations on IAM Entities</a> in <em>Using AWS Identity and Access
	 * Management</em>.
	 *
	 * @param string $user_name (Required) Name of the user to create. [Constraints: The value must be between 1 and 64 characters, and must match the following regular expression pattern: <code>[\w+=,.@-]*</code>]
	 * @param array $opt (Optional) An associative array of parameters that can have the following keys: <ul>
	 * 	<li><code>Path</code> - <code>string</code> - Optional - The path for the user name. For more information about paths, see <a href="http://docs.amazonwebservices.com/IAM/latest/UserGuide/index.html?Using_Identifiers.html" target="_blank">Identifiers for IAM Entities</a> in <em>Using AWS Identity and Access Management</em>. This parameter is optional. If it is not included, it defaults to a slash (/). [Constraints: The value must be between 1 and 512 characters, and must match the following regular expression pattern: <code>(\u002F)|(\u002F[\u0021-\u007F]+\u002F)</code>]</li>
	 * 	<li><code>curlopts</code> - <code>array</code> - Optional - A set of values to pass directly into <code>curl_setopt()</code>, where the key is a pre-defined <code>CURLOPT_*</code> constant.</li>
	 * 	<li><code>returnCurlHandle</code> - <code>boolean</code> - Optional - A private toggle specifying that the cURL handle be returned rather than actually completing the request. This toggle is useful for manually managed batch requests.</li></ul>
	 * @return CFResponse A <CFResponse> object containing a parsed HTTP response.
	 */
	public function create_user($user_name, $opt = null)
	{
		if (!$opt) $opt = array();
		$opt['UserName'] = $user_name;
		
		return $this->authenticate('CreateUser', $opt);
	}

	/**
	 * Creates a new virtual MFA device for the AWS account. After creating the virtual MFA, use
	 * 	<a href="http://docs.amazonwebservices.com/IAM/latest/APIReference/API_EnableMFADevice.html"
	 * target="_blank">EnableMFADevice</a> to attach the MFA device to an IAM user. For more
	 * information about creating and working with virtual MFA devices, go to <a href=
	 * "http://docs.amazonwebservices.com/IAM/latest/UserGuide/index.html?Using_VirtualMFA.html"
	 * target="_blank">Using a Virtual MFA Device</a> in <em>Using AWS Identity and Access
	 * Management</em>.
	 *  
	 * For information about limits on the number of MFA devices you can create, see <a href=
	 * "http://docs.amazonwebservices.com/IAM/latest/UserGuide/index.html?LimitationsOnEntities.html"
	 * target="_blank">Limitations on Entities</a> in <em>Using AWS Identity and Access
	 * Management</em>.
	 * 
	 * <p class="important">
	 * The seed information contained in the QR code and the Base32 string should be treated like any
	 * other secret access information, such as your AWS access keys or your passwords. After you
	 * provision your virtual device, you should ensure that the information is destroyed following
	 * secure procedures.
	 * </p>
	 *
	 * @param string $virtual_mfa_device_name (Required) The name of the virtual MFA device. Use with path to uniquely identify a virtual MFA device. [Constraints: The value must be more than 1 characters, and must match the following regular expression pattern: <code>[\w+=,.@-]*</code>]
	 * @param array $opt (Optional) An associative array of parameters that can have the following keys: <ul>
	 * 	<li><code>Path</code> - <code>string</code> - Optional - The path for the virtual MFA device. For more information about paths, see <a href="http://docs.amazonwebservices.com/IAM/latest/UserGuide/index.html?Using_Identifiers.html" target="_blank">Identifiers for IAM Entities</a> in <em>Using AWS Identity and Access Management</em>. This parameter is optional. If it is not included, it defaults to a slash (/). [Constraints: The value must be between 1 and 512 characters, and must match the following regular expression pattern: <code>(\u002F)|(\u002F[\u0021-\u007F]+\u002F)</code>]</li>
	 * 	<li><code>curlopts</code> - <code>array</code> - Optional - A set of values to pass directly into <code>curl_setopt()</code>, where the key is a pre-defined <code>CURLOPT_*</code> constant.</li>
	 * 	<li><code>returnCurlHandle</code> - <code>boolean</code> - Optional - A private toggle specifying that the cURL handle be returned rather than actually completing the request. This toggle is useful for manually managed batch requests.</li></ul>
	 * @return CFResponse A <CFResponse> object containing a parsed HTTP response.
	 */
	public function create_virtual_mfa_device($virtual_mfa_device_name, $opt = null)
	{
		if (!$opt) $opt = array();
		$opt['VirtualMFADeviceName'] = $virtual_mfa_device_name;
		
		return $this->authenticate('CreateVirtualMFADevice', $opt);
	}

	/**
	 * Deactivates the specified MFA device and removes it from association with the user name for
	 * which it was originally enabled.
	 *
	 * @param string $user_name (Required) Name of the user whose MFA device you want to deactivate. [Constraints: The value must be between 1 and 64 characters, and must match the following regular expression pattern: <code>[\w+=,.@-]*</code>]
	 * @param string $serial_number (Required) The serial number that uniquely identifies the MFA device. For virtual MFA devices, the serial number is the device ARN. [Constraints: The value must be between 9 and 256 characters, and must match the following regular expression pattern: <code>[\w+=/:,.@-]*</code>]
	 * @param array $opt (Optional) An associative array of parameters that can have the following keys: <ul>
	 * 	<li><code>curlopts</code> - <code>array</code> - Optional - A set of values to pass directly into <code>curl_setopt()</code>, where the key is a pre-defined <code>CURLOPT_*</code> constant.</li>
	 * 	<li><code>returnCurlHandle</code> - <code>boolean</code> - Optional - A private toggle specifying that the cURL handle be returned rather than actually completing the request. This toggle is useful for manually managed batch requests.</li></ul>
	 * @return CFResponse A <CFResponse> object containing a parsed HTTP response.
	 */
	public function deactivate_mfa_device($user_name, $serial_number, $opt = null)
	{
		if (!$opt) $opt = array();
		$opt['UserName'] = $user_name;
		$opt['SerialNumber'] = $serial_number;
		
		return $this->authenticate('DeactivateMFADevice', $opt);
	}

	/**
	 * Deletes the access key associated with the specified user.
	 *  
	 * If you do not specify a user name, IAM determines the user name implicitly based on the AWS
	 * Access Key ID signing the request. Because this action works for access keys under the AWS
	 * account, you can use this API to manage root credentials even if the AWS account has no
	 * associated users.
	 *
	 * @param string $access_key_id (Required) The Access Key ID for the Access Key ID and Secret Access Key you want to delete. [Constraints: The value must be between 16 and 32 characters, and must match the following regular expression pattern: <code>[\w]*</code>]
	 * @param array $opt (Optional) An associative array of parameters that can have the following keys: <ul>
	 * 	<li><code>UserName</code> - <code>string</code> - Optional - Name of the user whose key you want to delete. [Constraints: The value must be between 1 and 64 characters, and must match the following regular expression pattern: <code>[\w+=,.@-]*</code>]</li>
	 * 	<li><code>curlopts</code> - <code>array</code> - Optional - A set of values to pass directly into <code>curl_setopt()</code>, where the key is a pre-defined <code>CURLOPT_*</code> constant.</li>
	 * 	<li><code>returnCurlHandle</code> - <code>boolean</code> - Optional - A private toggle specifying that the cURL handle be returned rather than actually completing the request. This toggle is useful for manually managed batch requests.</li></ul>
	 * @return CFResponse A <CFResponse> object containing a parsed HTTP response.
	 */
	public function delete_access_key($access_key_id, $opt = null)
	{
		if (!$opt) $opt = array();
		$opt['AccessKeyId'] = $access_key_id;
		
		return $this->authenticate('DeleteAccessKey', $opt);
	}

	/**
	 * Deletes the specified AWS account alias. For information about using an AWS account alias, see
	 * 	<a href="http://docs.amazonwebservices.com/IAM/latest/UserGuide/AccountAlias.html" target=
	 * "_blank">Using an Alias for Your AWS Account ID</a> in <em>Using AWS Identity and Access
	 * Management</em>.
	 *
	 * @param string $account_alias (Required) Name of the account alias to delete. [Constraints: The value must be between 3 and 63 characters, and must match the following regular expression pattern: <code>^[a-z0-9](([a-z0-9]|-(?!-))*[a-z0-9])?$</code>]
	 * @param array $opt (Optional) An associative array of parameters that can have the following keys: <ul>
	 * 	<li><code>curlopts</code> - <code>array</code> - Optional - A set of values to pass directly into <code>curl_setopt()</code>, where the key is a pre-defined <code>CURLOPT_*</code> constant.</li>
	 * 	<li><code>returnCurlHandle</code> - <code>boolean</code> - Optional - A private toggle specifying that the cURL handle be returned rather than actually completing the request. This toggle is useful for manually managed batch requests.</li></ul>
	 * @return CFResponse A <CFResponse> object containing a parsed HTTP response.
	 */
	public function delete_account_alias($account_alias, $opt = null)
	{
		if (!$opt) $opt = array();
		$opt['AccountAlias'] = $account_alias;
		
		return $this->authenticate('DeleteAccountAlias', $opt);
	}

	/**
	 * Deletes the specified group. The group must not contain any users or have any attached
	 * policies.
	 *
	 * @param string $group_name (Required) Name of the group to delete. [Constraints: The value must be between 1 and 128 characters, and must match the following regular expression pattern: <code>[\w+=,.@-]*</code>]
	 * @param array $opt (Optional) An associative array of parameters that can have the following keys: <ul>
	 * 	<li><code>curlopts</code> - <code>array</code> - Optional - A set of values to pass directly into <code>curl_setopt()</code>, where the key is a pre-defined <code>CURLOPT_*</code> constant.</li>
	 * 	<li><code>returnCurlHandle</code> - <code>boolean</code> - Optional - A private toggle specifying that the cURL handle be returned rather than actually completing the request. This toggle is useful for manually managed batch requests.</li></ul>
	 * @return CFResponse A <CFResponse> object containing a parsed HTTP response.
	 */
	public function delete_group($group_name, $opt = null)
	{
		if (!$opt) $opt = array();
		$opt['GroupName'] = $group_name;
		
		return $this->authenticate('DeleteGroup', $opt);
	}

	/**
	 * Deletes the specified policy that is associated with the specified group.
	 *
	 * @param string $group_name (Required) Name of the group the policy is associated with. [Constraints: The value must be between 1 and 128 characters, and must match the following regular expression pattern: <code>[\w+=,.@-]*</code>]
	 * @param string $policy_name (Required) Name of the policy document to delete. [Constraints: The value must be between 1 and 128 characters, and must match the following regular expression pattern: <code>[\w+=,.@-]*</code>]
	 * @param array $opt (Optional) An associative array of parameters that can have the following keys: <ul>
	 * 	<li><code>curlopts</code> - <code>array</code> - Optional - A set of values to pass directly into <code>curl_setopt()</code>, where the key is a pre-defined <code>CURLOPT_*</code> constant.</li>
	 * 	<li><code>returnCurlHandle</code> - <code>boolean</code> - Optional - A private toggle specifying that the cURL handle be returned rather than actually completing the request. This toggle is useful for manually managed batch requests.</li></ul>
	 * @return CFResponse A <CFResponse> object containing a parsed HTTP response.
	 */
	public function delete_group_policy($group_name, $policy_name, $opt = null)
	{
		if (!$opt) $opt = array();
		$opt['GroupName'] = $group_name;
		$opt['PolicyName'] = $policy_name;
		
		return $this->authenticate('DeleteGroupPolicy', $opt);
	}

	/**
	 * Deletes the login profile for the specified user, which terminates the user's ability to access
	 * AWS services through the IAM login page.
	 * 
	 * <p class="important">
	 * Deleting a user's login profile does not prevent a user from accessing IAM through the command
	 * line interface or the API. To prevent all user access you must also either make the access key
	 * inactive or delete it. For more information about making keys inactive or deleting them, see
	 * <code>UpdateAccessKey</code> and <code>DeleteAccessKey</code>.
	 * </p>
	 *
	 * @param string $user_name (Required) Name of the user whose login profile you want to delete. [Constraints: The value must be between 1 and 64 characters, and must match the following regular expression pattern: <code>[\w+=,.@-]*</code>]
	 * @param array $opt (Optional) An associative array of parameters that can have the following keys: <ul>
	 * 	<li><code>curlopts</code> - <code>array</code> - Optional - A set of values to pass directly into <code>curl_setopt()</code>, where the key is a pre-defined <code>CURLOPT_*</code> constant.</li>
	 * 	<li><code>returnCurlHandle</code> - <code>boolean</code> - Optional - A private toggle specifying that the cURL handle be returned rather than actually completing the request. This toggle is useful for manually managed batch requests.</li></ul>
	 * @return CFResponse A <CFResponse> object containing a parsed HTTP response.
	 */
	public function delete_login_profile($user_name, $opt = null)
	{
		if (!$opt) $opt = array();
		$opt['UserName'] = $user_name;
		
		return $this->authenticate('DeleteLoginProfile', $opt);
	}

	/**
	 * Deletes the specified server certificate.
	 * 
	 * <p class="important">
	 * If you are using a server certificate with Elastic Load Balancing, deleting the certificate
	 * could have implications for your application. If Elastic Load Balancing doesn't detect the
	 * deletion of bound certificates, it may continue to use the certificates. This could cause
	 * Elastic Load Balancing to stop accepting traffic. We recommend that you remove the reference to
	 * the certificate from Elastic Load Balancing before using this command to delete the
	 * certificate. For more information, go to <a href=
	 * "http://docs.amazonwebservices.com/ElasticLoadBalancing/latest/APIReference/API_DeleteLoadBalancerListeners.html"
	 * target="blank">DeleteLoadBalancerListeners</a> in the <em>Elastic Load Balancing API
	 * Reference</em>.
	 * </p>
	 *
	 * @param string $server_certificate_name (Required) The name of the server certificate you want to delete. [Constraints: The value must be between 1 and 128 characters, and must match the following regular expression pattern: <code>[\w+=,.@-]*</code>]
	 * @param array $opt (Optional) An associative array of parameters that can have the following keys: <ul>
	 * 	<li><code>curlopts</code> - <code>array</code> - Optional - A set of values to pass directly into <code>curl_setopt()</code>, where the key is a pre-defined <code>CURLOPT_*</code> constant.</li>
	 * 	<li><code>returnCurlHandle</code> - <code>boolean</code> - Optional - A private toggle specifying that the cURL handle be returned rather than actually completing the request. This toggle is useful for manually managed batch requests.</li></ul>
	 * @return CFResponse A <CFResponse> object containing a parsed HTTP response.
	 */
	public function delete_server_certificate($server_certificate_name, $opt = null)
	{
		if (!$opt) $opt = array();
		$opt['ServerCertificateName'] = $server_certificate_name;
		
		return $this->authenticate('DeleteServerCertificate', $opt);
	}

	/**
	 * Deletes the specified signing certificate associated with the specified user.
	 *  
	 * If you do not specify a user name, IAM determines the user name implicitly based on the AWS
	 * Access Key ID signing the request. Because this action works for access keys under the AWS
	 * account, you can use this API to manage root credentials even if the AWS account has no
	 * associated users.
	 *
	 * @param string $certificate_id (Required) ID of the signing certificate to delete. [Constraints: The value must be between 24 and 128 characters, and must match the following regular expression pattern: <code>[\w]*</code>]
	 * @param array $opt (Optional) An associative array of parameters that can have the following keys: <ul>
	 * 	<li><code>UserName</code> - <code>string</code> - Optional - Name of the user the signing certificate belongs to. [Constraints: The value must be between 1 and 64 characters, and must match the following regular expression pattern: <code>[\w+=,.@-]*</code>]</li>
	 * 	<li><code>curlopts</code> - <code>array</code> - Optional - A set of values to pass directly into <code>curl_setopt()</code>, where the key is a pre-defined <code>CURLOPT_*</code> constant.</li>
	 * 	<li><code>returnCurlHandle</code> - <code>boolean</code> - Optional - A private toggle specifying that the cURL handle be returned rather than actually completing the request. This toggle is useful for manually managed batch requests.</li></ul>
	 * @return CFResponse A <CFResponse> object containing a parsed HTTP response.
	 */
	public function delete_signing_certificate($certificate_id, $opt = null)
	{
		if (!$opt) $opt = array();
		$opt['CertificateId'] = $certificate_id;
		
		return $this->authenticate('DeleteSigningCertificate', $opt);
	}

	/**
	 * Deletes the specified user. The user must not belong to any groups, have any keys or signing
	 * certificates, or have any attached policies.
	 *
	 * @param string $user_name (Required) Name of the user to delete. [Constraints: The value must be between 1 and 64 characters, and must match the following regular expression pattern: <code>[\w+=,.@-]*</code>]
	 * @param array $opt (Optional) An associative array of parameters that can have the following keys: <ul>
	 * 	<li><code>curlopts</code> - <code>array</code> - Optional - A set of values to pass directly into <code>curl_setopt()</code>, where the key is a pre-defined <code>CURLOPT_*</code> constant.</li>
	 * 	<li><code>returnCurlHandle</code> - <code>boolean</code> - Optional - A private toggle specifying that the cURL handle be returned rather than actually completing the request. This toggle is useful for manually managed batch requests.</li></ul>
	 * @return CFResponse A <CFResponse> object containing a parsed HTTP response.
	 */
	public function delete_user($user_name, $opt = null)
	{
		if (!$opt) $opt = array();
		$opt['UserName'] = $user_name;
		
		return $this->authenticate('DeleteUser', $opt);
	}

	/**
	 * Deletes the specified policy associated with the specified user.
	 *
	 * @param string $user_name (Required) Name of the user the policy is associated with. [Constraints: The value must be between 1 and 64 characters, and must match the following regular expression pattern: <code>[\w+=,.@-]*</code>]
	 * @param string $policy_name (Required) Name of the policy document to delete. [Constraints: The value must be between 1 and 128 characters, and must match the following regular expression pattern: <code>[\w+=,.@-]*</code>]
	 * @param array $opt (Optional) An associative array of parameters that can have the following keys: <ul>
	 * 	<li><code>curlopts</code> - <code>array</code> - Optional - A set of values to pass directly into <code>curl_setopt()</code>, where the key is a pre-defined <code>CURLOPT_*</code> constant.</li>
	 * 	<li><code>returnCurlHandle</code> - <code>boolean</code> - Optional - A private toggle specifying that the cURL handle be returned rather than actually completing the request. This toggle is useful for manually managed batch requests.</li></ul>
	 * @return CFResponse A <CFResponse> object containing a parsed HTTP response.
	 */
	public function delete_user_policy($user_name, $policy_name, $opt = null)
	{
		if (!$opt) $opt = array();
		$opt['UserName'] = $user_name;
		$opt['PolicyName'] = $policy_name;
		
		return $this->authenticate('DeleteUserPolicy', $opt);
	}

	/**
	 * Deletes a virtual MFA device.
	 * 
	 * <p class="note">
	 * You must deactivate a user's virtual MFA device before you can delete it. For information about
	 * deactivating MFA devices, see <a href=
	 * "http://docs.amazonwebservices.com/IAM/latest/APIReference/API_DeactivateMFADevice.html">DeactivateMFADevice</a>.
	 * </p>
	 *
	 * @param string $serial_number (Required) The serial number that uniquely identifies the MFA device. For virtual MFA devices, the serial number is the same as the ARN. [Constraints: The value must be between 9 and 256 characters, and must match the following regular expression pattern: <code>[\w+=/:,.@-]*</code>]
	 * @param array $opt (Optional) An associative array of parameters that can have the following keys: <ul>
	 * 	<li><code>curlopts</code> - <code>array</code> - Optional - A set of values to pass directly into <code>curl_setopt()</code>, where the key is a pre-defined <code>CURLOPT_*</code> constant.</li>
	 * 	<li><code>returnCurlHandle</code> - <code>boolean</code> - Optional - A private toggle specifying that the cURL handle be returned rather than actually completing the request. This toggle is useful for manually managed batch requests.</li></ul>
	 * @return CFResponse A <CFResponse> object containing a parsed HTTP response.
	 */
	public function delete_virtual_mfa_device($serial_number, $opt = null)
	{
		if (!$opt) $opt = array();
		$opt['SerialNumber'] = $serial_number;
		
		return $this->authenticate('DeleteVirtualMFADevice', $opt);
	}

	/**
	 * Enables the specified MFA device and associates it with the specified user name. When enabled,
	 * the MFA device is required for every subsequent login by the user name associated with the
	 * device.
	 *
	 * @param string $user_name (Required) Name of the user for whom you want to enable the MFA device. [Constraints: The value must be between 1 and 64 characters, and must match the following regular expression pattern: <code>[\w+=,.@-]*</code>]
	 * @param string $serial_number (Required) The serial number that uniquely identifies the MFA device. For virtual MFA devices, the serial number is the device ARN. [Constraints: The value must be between 9 and 256 characters, and must match the following regular expression pattern: <code>[\w+=/:,.@-]*</code>]
	 * @param string $authentication_code1 (Required) An authentication code emitted by the device. [Constraints: The value must be between 6 and 6 characters, and must match the following regular expression pattern: <code>[\d]*</code>]
	 * @param string $authentication_code2 (Required) A subsequent authentication code emitted by the device. [Constraints: The value must be between 6 and 6 characters, and must match the following regular expression pattern: <code>[\d]*</code>]
	 * @param array $opt (Optional) An associative array of parameters that can have the following keys: <ul>
	 * 	<li><code>curlopts</code> - <code>array</code> - Optional - A set of values to pass directly into <code>curl_setopt()</code>, where the key is a pre-defined <code>CURLOPT_*</code> constant.</li>
	 * 	<li><code>returnCurlHandle</code> - <code>boolean</code> - Optional - A private toggle specifying that the cURL handle be returned rather than actually completing the request. This toggle is useful for manually managed batch requests.</li></ul>
	 * @return CFResponse A <CFResponse> object containing a parsed HTTP response.
	 */
	public function enable_mfa_device($user_name, $serial_number, $authentication_code1, $authentication_code2, $opt = null)
	{
		if (!$opt) $opt = array();
		$opt['UserName'] = $user_name;
		$opt['SerialNumber'] = $serial_number;
		$opt['AuthenticationCode1'] = $authentication_code1;
		$opt['AuthenticationCode2'] = $authentication_code2;
		
		return $this->authenticate('EnableMFADevice', $opt);
	}

	/**
	 * Retrieves account level information about account entity usage and IAM quotas.
	 *  
	 * For information about limitations on IAM entities, see <a href=
	 * "http://docs.amazonwebservices.com/IAM/latest/UserGuide/index.html?LimitationsOnEntities.html"
	 * target="_blank">Limitations on IAM Entities</a> in <em>Using AWS Identity and Access
	 * Management</em>.
	 *
	 * @param array $opt (Optional) An associative array of parameters that can have the following keys: <ul>
	 * 	<li><code>curlopts</code> - <code>array</code> - Optional - A set of values to pass directly into <code>curl_setopt()</code>, where the key is a pre-defined <code>CURLOPT_*</code> constant.</li>
	 * 	<li><code>returnCurlHandle</code> - <code>boolean</code> - Optional - A private toggle specifying that the cURL handle be returned rather than actually completing the request. This toggle is useful for manually managed batch requests.</li></ul>
	 * @return CFResponse A <CFResponse> object containing a parsed HTTP response.
	 */
	public function get_account_summary($opt = null)
	{
		if (!$opt) $opt = array();
				
		return $this->authenticate('GetAccountSummary', $opt);
	}

	/**
	 * Returns a list of users that are in the specified group. You can paginate the results using the
	 * <code>MaxItems</code> and <code>Marker</code> parameters.
	 *
	 * @param string $group_name (Required) Name of the group. [Constraints: The value must be between 1 and 128 characters, and must match the following regular expression pattern: <code>[\w+=,.@-]*</code>]
	 * @param array $opt (Optional) An associative array of parameters that can have the following keys: <ul>
	 * 	<li><code>Marker</code> - <code>string</code> - Optional - Use this only when paginating results, and only in a subsequent request after you've received a response where the results are truncated. Set it to the value of the <code>Marker</code> element in the response you just received. [Constraints: The value must be between 1 and 320 characters, and must match the following regular expression pattern: <code>[\u0020-\u00FF]*</code>]</li>
	 * 	<li><code>MaxItems</code> - <code>integer</code> - Optional - Use this only when paginating results to indicate the maximum number of user names you want in the response. If there are additional user names beyond the maximum you specify, the <code>IsTruncated</code> response element is <code>true</code>.</li>
	 * 	<li><code>curlopts</code> - <code>array</code> - Optional - A set of values to pass directly into <code>curl_setopt()</code>, where the key is a pre-defined <code>CURLOPT_*</code> constant.</li>
	 * 	<li><code>returnCurlHandle</code> - <code>boolean</code> - Optional - A private toggle specifying that the cURL handle be returned rather than actually completing the request. This toggle is useful for manually managed batch requests.</li></ul>
	 * @return CFResponse A <CFResponse> object containing a parsed HTTP response.
	 */
	public function get_group($group_name, $opt = null)
	{
		if (!$opt) $opt = array();
		$opt['GroupName'] = $group_name;
		
		return $this->authenticate('GetGroup', $opt);
	}

	/**
	 * Retrieves the specified policy document for the specified group. The returned policy is
	 * URL-encoded according to RFC 3986. For more information about RFC 3986, go to <a href=
	 * "http://www.faqs.org/rfcs/rfc3986.html">http://www.faqs.org/rfcs/rfc3986.html</a>.
	 *
	 * @param string $group_name (Required) Name of the group the policy is associated with. [Constraints: The value must be between 1 and 128 characters, and must match the following regular expression pattern: <code>[\w+=,.@-]*</code>]
	 * @param string $policy_name (Required) Name of the policy document to get. [Constraints: The value must be between 1 and 128 characters, and must match the following regular expression pattern: <code>[\w+=,.@-]*</code>]
	 * @param array $opt (Optional) An associative array of parameters that can have the following keys: <ul>
	 * 	<li><code>curlopts</code> - <code>array</code> - Optional - A set of values to pass directly into <code>curl_setopt()</code>, where the key is a pre-defined <code>CURLOPT_*</code> constant.</li>
	 * 	<li><code>returnCurlHandle</code> - <code>boolean</code> - Optional - A private toggle specifying that the cURL handle be returned rather than actually completing the request. This toggle is useful for manually managed batch requests.</li></ul>
	 * @return CFResponse A <CFResponse> object containing a parsed HTTP response.
	 */
	public function get_group_policy($group_name, $policy_name, $opt = null)
	{
		if (!$opt) $opt = array();
		$opt['GroupName'] = $group_name;
		$opt['PolicyName'] = $policy_name;
		
		return $this->authenticate('GetGroupPolicy', $opt);
	}

	/**
	 * Retrieves the login profile for the specified user.
	 *
	 * @param string $user_name (Required) Name of the user whose login profile you want to retrieve. [Constraints: The value must be between 1 and 64 characters, and must match the following regular expression pattern: <code>[\w+=,.@-]*</code>]
	 * @param array $opt (Optional) An associative array of parameters that can have the following keys: <ul>
	 * 	<li><code>curlopts</code> - <code>array</code> - Optional - A set of values to pass directly into <code>curl_setopt()</code>, where the key is a pre-defined <code>CURLOPT_*</code> constant.</li>
	 * 	<li><code>returnCurlHandle</code> - <code>boolean</code> - Optional - A private toggle specifying that the cURL handle be returned rather than actually completing the request. This toggle is useful for manually managed batch requests.</li></ul>
	 * @return CFResponse A <CFResponse> object containing a parsed HTTP response.
	 */
	public function get_login_profile($user_name, $opt = null)
	{
		if (!$opt) $opt = array();
		$opt['UserName'] = $user_name;
		
		return $this->authenticate('GetLoginProfile', $opt);
	}

	/**
	 * Retrieves information about the specified server certificate.
	 *
	 * @param string $server_certificate_name (Required) The name of the server certificate you want to retrieve information about. [Constraints: The value must be between 1 and 128 characters, and must match the following regular expression pattern: <code>[\w+=,.@-]*</code>]
	 * @param array $opt (Optional) An associative array of parameters that can have the following keys: <ul>
	 * 	<li><code>curlopts</code> - <code>array</code> - Optional - A set of values to pass directly into <code>curl_setopt()</code>, where the key is a pre-defined <code>CURLOPT_*</code> constant.</li>
	 * 	<li><code>returnCurlHandle</code> - <code>boolean</code> - Optional - A private toggle specifying that the cURL handle be returned rather than actually completing the request. This toggle is useful for manually managed batch requests.</li></ul>
	 * @return CFResponse A <CFResponse> object containing a parsed HTTP response.
	 */
	public function get_server_certificate($server_certificate_name, $opt = null)
	{
		if (!$opt) $opt = array();
		$opt['ServerCertificateName'] = $server_certificate_name;
		
		return $this->authenticate('GetServerCertificate', $opt);
	}

	/**
	 * Retrieves information about the specified user, including the user's path, GUID, and ARN.
	 *  
	 * If you do not specify a user name, IAM determines the user name implicitly based on the AWS
	 * Access Key ID signing the request.
	 *
	 * @param array $opt (Optional) An associative array of parameters that can have the following keys: <ul>
	 * 	<li><code>UserName</code> - <code>string</code> - Optional - Name of the user to get information about. This parameter is optional. If it is not included, it defaults to the user making the request. [Constraints: The value must be between 1 and 64 characters, and must match the following regular expression pattern: <code>[\w+=,.@-]*</code>]</li>
	 * 	<li><code>curlopts</code> - <code>array</code> - Optional - A set of values to pass directly into <code>curl_setopt()</code>, where the key is a pre-defined <code>CURLOPT_*</code> constant.</li>
	 * 	<li><code>returnCurlHandle</code> - <code>boolean</code> - Optional - A private toggle specifying that the cURL handle be returned rather than actually completing the request. This toggle is useful for manually managed batch requests.</li></ul>
	 * @return CFResponse A <CFResponse> object containing a parsed HTTP response.
	 */
	public function get_user($opt = null)
	{
		if (!$opt) $opt = array();
				
		return $this->authenticate('GetUser', $opt);
	}

	/**
	 * Retrieves the specified policy document for the specified user. The returned policy is
	 * URL-encoded according to RFC 3986. For more information about RFC 3986, go to <a href=
	 * "http://www.faqs.org/rfcs/rfc3986.html">http://www.faqs.org/rfcs/rfc3986.html</a>.
	 *
	 * @param string $user_name (Required) Name of the user who the policy is associated with. [Constraints: The value must be between 1 and 64 characters, and must match the following regular expression pattern: <code>[\w+=,.@-]*</code>]
	 * @param string $policy_name (Required) Name of the policy document to get. [Constraints: The value must be between 1 and 128 characters, and must match the following regular expression pattern: <code>[\w+=,.@-]*</code>]
	 * @param array $opt (Optional) An associative array of parameters that can have the following keys: <ul>
	 * 	<li><code>curlopts</code> - <code>array</code> - Optional - A set of values to pass directly into <code>curl_setopt()</code>, where the key is a pre-defined <code>CURLOPT_*</code> constant.</li>
	 * 	<li><code>returnCurlHandle</code> - <code>boolean</code> - Optional - A private toggle specifying that the cURL handle be returned rather than actually completing the request. This toggle is useful for manually managed batch requests.</li></ul>
	 * @return CFResponse A <CFResponse> object containing a parsed HTTP response.
	 */
	public function get_user_policy($user_name, $policy_name, $opt = null)
	{
		if (!$opt) $opt = array();
		$opt['UserName'] = $user_name;
		$opt['PolicyName'] = $policy_name;
		
		return $this->authenticate('GetUserPolicy', $opt);
	}

	/**
	 * Returns information about the Access Key IDs associated with the specified user. If there are
	 * none, the action returns an empty list.
	 *  
	 * Although each user is limited to a small number of keys, you can still paginate the results
	 * using the <code>MaxItems</code> and <code>Marker</code> parameters.
	 *  
	 * If the <code>UserName</code> field is not specified, the UserName is determined implicitly
	 * based on the AWS Access Key ID used to sign the request. Because this action works for access
	 * keys under the AWS account, this API can be used to manage root credentials even if the AWS
	 * account has no associated users.
	 * 
	 * <p class="note">
	 * To ensure the security of your AWS account, the secret access key is accessible only during key
	 * and user creation.
	 * </p>
	 *
	 * @param array $opt (Optional) An associative array of parameters that can have the following keys: <ul>
	 * 	<li><code>UserName</code> - <code>string</code> - Optional - Name of the user. [Constraints: The value must be between 1 and 64 characters, and must match the following regular expression pattern: <code>[\w+=,.@-]*</code>]</li>
	 * 	<li><code>Marker</code> - <code>string</code> - Optional - Use this parameter only when paginating results, and only in a subsequent request after you've received a response where the results are truncated. Set it to the value of the <code>Marker</code> element in the response you just received. [Constraints: The value must be between 1 and 320 characters, and must match the following regular expression pattern: <code>[\u0020-\u00FF]*</code>]</li>
	 * 	<li><code>MaxItems</code> - <code>integer</code> - Optional - Use this parameter only when paginating results to indicate the maximum number of keys you want in the response. If there are additional keys beyond the maximum you specify, the <code>IsTruncated</code> response element is <code>true</code>.</li>
	 * 	<li><code>curlopts</code> - <code>array</code> - Optional - A set of values to pass directly into <code>curl_setopt()</code>, where the key is a pre-defined <code>CURLOPT_*</code> constant.</li>
	 * 	<li><code>returnCurlHandle</code> - <code>boolean</code> - Optional - A private toggle specifying that the cURL handle be returned rather than actually completing the request. This toggle is useful for manually managed batch requests.</li></ul>
	 * @return CFResponse A <CFResponse> object containing a parsed HTTP response.
	 */
	public function list_access_keys($opt = null)
	{
		if (!$opt) $opt = array();
				
		return $this->authenticate('ListAccessKeys', $opt);
	}

	/**
	 * Lists the account aliases associated with the account. For information about using an AWS
	 * account alias, see <a href=
	 * "http://docs.amazonwebservices.com/IAM/latest/UserGuide/AccountAlias.html" target=
	 * "_blank">Using an Alias for Your AWS Account ID</a> in <em>Using AWS Identity and Access
	 * Management</em>.
	 *  
	 * You can paginate the results using the <code>MaxItems</code> and <code>Marker</code>
	 * parameters.
	 *
	 * @param array $opt (Optional) An associative array of parameters that can have the following keys: <ul>
	 * 	<li><code>Marker</code> - <code>string</code> - Optional - Use this only when paginating results, and only in a subsequent request after you've received a response where the results are truncated. Set it to the value of the <code>Marker</code> element in the response you just received. [Constraints: The value must be between 1 and 320 characters, and must match the following regular expression pattern: <code>[\u0020-\u00FF]*</code>]</li>
	 * 	<li><code>MaxItems</code> - <code>integer</code> - Optional - Use this only when paginating results to indicate the maximum number of account aliases you want in the response. If there are additional account aliases beyond the maximum you specify, the <code>IsTruncated</code> response element is <code>true</code>.</li>
	 * 	<li><code>curlopts</code> - <code>array</code> - Optional - A set of values to pass directly into <code>curl_setopt()</code>, where the key is a pre-defined <code>CURLOPT_*</code> constant.</li>
	 * 	<li><code>returnCurlHandle</code> - <code>boolean</code> - Optional - A private toggle specifying that the cURL handle be returned rather than actually completing the request. This toggle is useful for manually managed batch requests.</li></ul>
	 * @return CFResponse A <CFResponse> object containing a parsed HTTP response.
	 */
	public function list_account_aliases($opt = null)
	{
		if (!$opt) $opt = array();
				
		return $this->authenticate('ListAccountAliases', $opt);
	}

	/**
	 * Lists the names of the policies associated with the specified group. If there are none, the
	 * action returns an empty list.
	 *  
	 * You can paginate the results using the <code>MaxItems</code> and <code>Marker</code>
	 * parameters.
	 *
	 * @param string $group_name (Required) The name of the group to list policies for. [Constraints: The value must be between 1 and 128 characters, and must match the following regular expression pattern: <code>[\w+=,.@-]*</code>]
	 * @param array $opt (Optional) An associative array of parameters that can have the following keys: <ul>
	 * 	<li><code>Marker</code> - <code>string</code> - Optional - Use this only when paginating results, and only in a subsequent request after you've received a response where the results are truncated. Set it to the value of the <code>Marker</code> element in the response you just received. [Constraints: The value must be between 1 and 320 characters, and must match the following regular expression pattern: <code>[\u0020-\u00FF]*</code>]</li>
	 * 	<li><code>MaxItems</code> - <code>integer</code> - Optional - Use this only when paginating results to indicate the maximum number of policy names you want in the response. If there are additional policy names beyond the maximum you specify, the <code>IsTruncated</code> response element is <code>true</code>.</li>
	 * 	<li><code>curlopts</code> - <code>array</code> - Optional - A set of values to pass directly into <code>curl_setopt()</code>, where the key is a pre-defined <code>CURLOPT_*</code> constant.</li>
	 * 	<li><code>returnCurlHandle</code> - <code>boolean</code> - Optional - A private toggle specifying that the cURL handle be returned rather than actually completing the request. This toggle is useful for manually managed batch requests.</li></ul>
	 * @return CFResponse A <CFResponse> object containing a parsed HTTP response.
	 */
	public function list_group_policies($group_name, $opt = null)
	{
		if (!$opt) $opt = array();
		$opt['GroupName'] = $group_name;
		
		return $this->authenticate('ListGroupPolicies', $opt);
	}

	/**
	 * Lists the groups that have the specified path prefix.
	 *  
	 * You can paginate the results using the <code>MaxItems</code> and <code>Marker</code>
	 * parameters.
	 *
	 * @param array $opt (Optional) An associative array of parameters that can have the following keys: <ul>
	 * 	<li><code>PathPrefix</code> - <code>string</code> - Optional - The path prefix for filtering the results. For example: <code>/division_abc/subdivision_xyz/</code>, which would get all groups whose path starts with <code>/division_abc/subdivision_xyz/</code>. This parameter is optional. If it is not included, it defaults to a slash (/), listing all groups. [Constraints: The value must be between 1 and 512 characters, and must match the following regular expression pattern: <code>\u002F[\u0021-\u007F]*</code>]</li>
	 * 	<li><code>Marker</code> - <code>string</code> - Optional - Use this only when paginating results, and only in a subsequent request after you've received a response where the results are truncated. Set it to the value of the <code>Marker</code> element in the response you just received. [Constraints: The value must be between 1 and 320 characters, and must match the following regular expression pattern: <code>[\u0020-\u00FF]*</code>]</li>
	 * 	<li><code>MaxItems</code> - <code>integer</code> - Optional - Use this only when paginating results to indicate the maximum number of groups you want in the response. If there are additional groups beyond the maximum you specify, the <code>IsTruncated</code> response element is <code>true</code>.</li>
	 * 	<li><code>curlopts</code> - <code>array</code> - Optional - A set of values to pass directly into <code>curl_setopt()</code>, where the key is a pre-defined <code>CURLOPT_*</code> constant.</li>
	 * 	<li><code>returnCurlHandle</code> - <code>boolean</code> - Optional - A private toggle specifying that the cURL handle be returned rather than actually completing the request. This toggle is useful for manually managed batch requests.</li></ul>
	 * @return CFResponse A <CFResponse> object containing a parsed HTTP response.
	 */
	public function list_groups($opt = null)
	{
		if (!$opt) $opt = array();
				
		return $this->authenticate('ListGroups', $opt);
	}

	/**
	 * Lists the groups the specified user belongs to.
	 *  
	 * You can paginate the results using the <code>MaxItems</code> and <code>Marker</code>
	 * parameters.
	 *
	 * @param string $user_name (Required) The name of the user to list groups for. [Constraints: The value must be between 1 and 64 characters, and must match the following regular expression pattern: <code>[\w+=,.@-]*</code>]
	 * @param array $opt (Optional) An associative array of parameters that can have the following keys: <ul>
	 * 	<li><code>Marker</code> - <code>string</code> - Optional - Use this only when paginating results, and only in a subsequent request after you've received a response where the results are truncated. Set it to the value of the <code>Marker</code> element in the response you just received. [Constraints: The value must be between 1 and 320 characters, and must match the following regular expression pattern: <code>[\u0020-\u00FF]*</code>]</li>
	 * 	<li><code>MaxItems</code> - <code>integer</code> - Optional - Use this only when paginating results to indicate the maximum number of groups you want in the response. If there are additional groups beyond the maximum you specify, the <code>IsTruncated</code> response element is <code>true</code>.</li>
	 * 	<li><code>curlopts</code> - <code>array</code> - Optional - A set of values to pass directly into <code>curl_setopt()</code>, where the key is a pre-defined <code>CURLOPT_*</code> constant.</li>
	 * 	<li><code>returnCurlHandle</code> - <code>boolean</code> - Optional - A private toggle specifying that the cURL handle be returned rather than actually completing the request. This toggle is useful for manually managed batch requests.</li></ul>
	 * @return CFResponse A <CFResponse> object containing a parsed HTTP response.
	 */
	public function list_groups_for_user($user_name, $opt = null)
	{
		if (!$opt) $opt = array();
		$opt['UserName'] = $user_name;
		
		return $this->authenticate('ListGroupsForUser', $opt);
	}

	/**
	 * Lists the MFA devices. If the request includes the user name, then this action lists all the
	 * MFA devices associated with the specified user name. If you do not specify a user name, IAM
	 * determines the user name implicitly based on the AWS Access Key ID signing the request.
	 *  
	 * You can paginate the results using the <code>MaxItems</code> and <code>Marker</code>
	 * parameters.
	 *
	 * @param array $opt (Optional) An associative array of parameters that can have the following keys: <ul>
	 * 	<li><code>UserName</code> - <code>string</code> - Optional - Name of the user whose MFA devices you want to list. [Constraints: The value must be between 1 and 64 characters, and must match the following regular expression pattern: <code>[\w+=,.@-]*</code>]</li>
	 * 	<li><code>Marker</code> - <code>string</code> - Optional - Use this only when paginating results, and only in a subsequent request after you've received a response where the results are truncated. Set it to the value of the <code>Marker</code> element in the response you just received. [Constraints: The value must be between 1 and 320 characters, and must match the following regular expression pattern: <code>[\u0020-\u00FF]*</code>]</li>
	 * 	<li><code>MaxItems</code> - <code>integer</code> - Optional - Use this only when paginating results to indicate the maximum number of MFA devices you want in the response. If there are additional MFA devices beyond the maximum you specify, the <code>IsTruncated</code> response element is <code>true</code>.</li>
	 * 	<li><code>curlopts</code> - <code>array</code> - Optional - A set of values to pass directly into <code>curl_setopt()</code>, where the key is a pre-defined <code>CURLOPT_*</code> constant.</li>
	 * 	<li><code>returnCurlHandle</code> - <code>boolean</code> - Optional - A private toggle specifying that the cURL handle be returned rather than actually completing the request. This toggle is useful for manually managed batch requests.</li></ul>
	 * @return CFResponse A <CFResponse> object containing a parsed HTTP response.
	 */
	public function list_mfa_devices($opt = null)
	{
		if (!$opt) $opt = array();
				
		return $this->authenticate('ListMFADevices', $opt);
	}

	/**
	 * Lists the server certificates that have the specified path prefix. If none exist, the action
	 * returns an empty list.
	 *  
	 * You can paginate the results using the <code>MaxItems</code> and <code>Marker</code>
	 * parameters.
	 *
	 * @param array $opt (Optional) An associative array of parameters that can have the following keys: <ul>
	 * 	<li><code>PathPrefix</code> - <code>string</code> - Optional - The path prefix for filtering the results. For example: <code>/company/servercerts</code> would get all server certificates for which the path starts with <code>/company/servercerts</code>. This parameter is optional. If it is not included, it defaults to a slash (/), listing all server certificates. [Constraints: The value must be between 1 and 512 characters, and must match the following regular expression pattern: <code>\u002F[\u0021-\u007F]*</code>]</li>
	 * 	<li><code>Marker</code> - <code>string</code> - Optional - Use this only when paginating results, and only in a subsequent request after you've received a response where the results are truncated. Set it to the value of the <code>Marker</code> element in the response you just received. [Constraints: The value must be between 1 and 320 characters, and must match the following regular expression pattern: <code>[\u0020-\u00FF]*</code>]</li>
	 * 	<li><code>MaxItems</code> - <code>integer</code> - Optional - Use this only when paginating results to indicate the maximum number of server certificates you want in the response. If there are additional server certificates beyond the maximum you specify, the <code>IsTruncated</code> response element will be set to <code>true</code>.</li>
	 * 	<li><code>curlopts</code> - <code>array</code> - Optional - A set of values to pass directly into <code>curl_setopt()</code>, where the key is a pre-defined <code>CURLOPT_*</code> constant.</li>
	 * 	<li><code>returnCurlHandle</code> - <code>boolean</code> - Optional - A private toggle specifying that the cURL handle be returned rather than actually completing the request. This toggle is useful for manually managed batch requests.</li></ul>
	 * @return CFResponse A <CFResponse> object containing a parsed HTTP response.
	 */
	public function list_server_certificates($opt = null)
	{
		if (!$opt) $opt = array();
				
		return $this->authenticate('ListServerCertificates', $opt);
	}

	/**
	 * Returns information about the signing certificates associated with the specified user. If there
	 * are none, the action returns an empty list.
	 *  
	 * Although each user is limited to a small number of signing certificates, you can still paginate
	 * the results using the <code>MaxItems</code> and <code>Marker</code> parameters.
	 *  
	 * If the <code>UserName</code> field is not specified, the user name is determined implicitly
	 * based on the AWS Access Key ID used to sign the request. Because this action works for access
	 * keys under the AWS account, this API can be used to manage root credentials even if the AWS
	 * account has no associated users.
	 *
	 * @param array $opt (Optional) An associative array of parameters that can have the following keys: <ul>
	 * 	<li><code>UserName</code> - <code>string</code> - Optional - The name of the user. [Constraints: The value must be between 1 and 64 characters, and must match the following regular expression pattern: <code>[\w+=,.@-]*</code>]</li>
	 * 	<li><code>Marker</code> - <code>string</code> - Optional - Use this only when paginating results, and only in a subsequent request after you've received a response where the results are truncated. Set it to the value of the <code>Marker</code> element in the response you just received. [Constraints: The value must be between 1 and 320 characters, and must match the following regular expression pattern: <code>[\u0020-\u00FF]*</code>]</li>
	 * 	<li><code>MaxItems</code> - <code>integer</code> - Optional - Use this only when paginating results to indicate the maximum number of certificate IDs you want in the response. If there are additional certificate IDs beyond the maximum you specify, the <code>IsTruncated</code> response element is <code>true</code>.</li>
	 * 	<li><code>curlopts</code> - <code>array</code> - Optional - A set of values to pass directly into <code>curl_setopt()</code>, where the key is a pre-defined <code>CURLOPT_*</code> constant.</li>
	 * 	<li><code>returnCurlHandle</code> - <code>boolean</code> - Optional - A private toggle specifying that the cURL handle be returned rather than actually completing the request. This toggle is useful for manually managed batch requests.</li></ul>
	 * @return CFResponse A <CFResponse> object containing a parsed HTTP response.
	 */
	public function list_signing_certificates($opt = null)
	{
		if (!$opt) $opt = array();
				
		return $this->authenticate('ListSigningCertificates', $opt);
	}

	/**
	 * Lists the names of the policies associated with the specified user. If there are none, the
	 * action returns an empty list.
	 *  
	 * You can paginate the results using the <code>MaxItems</code> and <code>Marker</code>
	 * parameters.
	 *
	 * @param string $user_name (Required) The name of the user to list policies for. [Constraints: The value must be between 1 and 64 characters, and must match the following regular expression pattern: <code>[\w+=,.@-]*</code>]
	 * @param array $opt (Optional) An associative array of parameters that can have the following keys: <ul>
	 * 	<li><code>Marker</code> - <code>string</code> - Optional - Use this only when paginating results, and only in a subsequent request after you've received a response where the results are truncated. Set it to the value of the <code>Marker</code> element in the response you just received. [Constraints: The value must be between 1 and 320 characters, and must match the following regular expression pattern: <code>[\u0020-\u00FF]*</code>]</li>
	 * 	<li><code>MaxItems</code> - <code>integer</code> - Optional - Use this only when paginating results to indicate the maximum number of policy names you want in the response. If there are additional policy names beyond the maximum you specify, the <code>IsTruncated</code> response element is <code>true</code>.</li>
	 * 	<li><code>curlopts</code> - <code>array</code> - Optional - A set of values to pass directly into <code>curl_setopt()</code>, where the key is a pre-defined <code>CURLOPT_*</code> constant.</li>
	 * 	<li><code>returnCurlHandle</code> - <code>boolean</code> - Optional - A private toggle specifying that the cURL handle be returned rather than actually completing the request. This toggle is useful for manually managed batch requests.</li></ul>
	 * @return CFResponse A <CFResponse> object containing a parsed HTTP response.
	 */
	public function list_user_policies($user_name, $opt = null)
	{
		if (!$opt) $opt = array();
		$opt['UserName'] = $user_name;
		
		return $this->authenticate('ListUserPolicies', $opt);
	}

	/**
	 * Lists the users that have the specified path prefix. If there are none, the action returns an
	 * empty list.
	 *  
	 * You can paginate the results using the <code>MaxItems</code> and <code>Marker</code>
	 * parameters.
	 *
	 * @param array $opt (Optional) An associative array of parameters that can have the following keys: <ul>
	 * 	<li><code>PathPrefix</code> - <code>string</code> - Optional - The path prefix for filtering the results. For example: <code>/division_abc/subdivision_xyz/</code>, which would get all user names whose path starts with <code>/division_abc/subdivision_xyz/</code>. This parameter is optional. If it is not included, it defaults to a slash (/), listing all user names. [Constraints: The value must be between 1 and 512 characters, and must match the following regular expression pattern: <code>\u002F[\u0021-\u007F]*</code>]</li>
	 * 	<li><code>Marker</code> - <code>string</code> - Optional - Use this parameter only when paginating results, and only in a subsequent request after you've received a response where the results are truncated. Set it to the value of the <code>Marker</code> element in the response you just received. [Constraints: The value must be between 1 and 320 characters, and must match the following regular expression pattern: <code>[\u0020-\u00FF]*</code>]</li>
	 * 	<li><code>MaxItems</code> - <code>integer</code> - Optional - Use this parameter only when paginating results to indicate the maximum number of user names you want in the response. If there are additional user names beyond the maximum you specify, the <code>IsTruncated</code> response element is <code>true</code>.</li>
	 * 	<li><code>curlopts</code> - <code>array</code> - Optional - A set of values to pass directly into <code>curl_setopt()</code>, where the key is a pre-defined <code>CURLOPT_*</code> constant.</li>
	 * 	<li><code>returnCurlHandle</code> - <code>boolean</code> - Optional - A private toggle specifying that the cURL handle be returned rather than actually completing the request. This toggle is useful for manually managed batch requests.</li></ul>
	 * @return CFResponse A <CFResponse> object containing a parsed HTTP response.
	 */
	public function list_users($opt = null)
	{
		if (!$opt) $opt = array();
				
		return $this->authenticate('ListUsers', $opt);
	}

	/**
	 * Lists the virtual MFA devices under the AWS account by assignment status. If you do not specify
	 * an assignment status, the action returns a list of all virtual MFA devices. Assignment status
	 * can be <code>Assigned</code>, <code>Unassigned</code>, or <code>Any</code>.
	 *  
	 * You can paginate the results using the <code>MaxItems</code> and <code>Marker</code>
	 * parameters.
	 *
	 * @param array $opt (Optional) An associative array of parameters that can have the following keys: <ul>
	 * 	<li><code>AssignmentStatus</code> - <code>string</code> - Optional - The status (unassigned or assigned) of the devices to list. If you do not specify an <code>AssignmentStatus</code>, the action defaults to <code>Any</code> which lists both assigned and unassigned virtual MFA devices. [Allowed values: <code>Assigned</code>, <code>Unassigned</code>, <code>Any</code>]</li>
	 * 	<li><code>Marker</code> - <code>string</code> - Optional - Use this parameter only when paginating results, and only in a subsequent request after you've received a response where the results are truncated. Set it to the value of the <code>Marker</code> element in the response you just received. [Constraints: The value must be between 1 and 320 characters, and must match the following regular expression pattern: <code>[\u0020-\u00FF]*</code>]</li>
	 * 	<li><code>MaxItems</code> - <code>integer</code> - Optional - Use this parameter only when paginating results to indicate the maximum number of user names you want in the response. If there are additional user names beyond the maximum you specify, the <code>IsTruncated</code> response element is <code>true</code>.</li>
	 * 	<li><code>curlopts</code> - <code>array</code> - Optional - A set of values to pass directly into <code>curl_setopt()</code>, where the key is a pre-defined <code>CURLOPT_*</code> constant.</li>
	 * 	<li><code>returnCurlHandle</code> - <code>boolean</code> - Optional - A private toggle specifying that the cURL handle be returned rather than actually completing the request. This toggle is useful for manually managed batch requests.</li></ul>
	 * @return CFResponse A <CFResponse> object containing a parsed HTTP response.
	 */
	public function list_virtual_mfa_devices($opt = null)
	{
		if (!$opt) $opt = array();
				
		return $this->authenticate('ListVirtualMFADevices', $opt);
	}

	/**
	 * Adds (or updates) a policy document associated with the specified group. For information about
	 * policies, refer to <a href=
	 * "http://docs.amazonwebservices.com/IAM/latest/UserGuide/index.html?PoliciesOverview.html"
	 * target="_blank">Overview of Policies</a> in <em>Using AWS Identity and Access Management</em>.
	 *  
	 * For information about limits on the number of policies you can associate with a group, see
	 * 	<a href=
	 * "http://docs.amazonwebservices.com/IAM/latest/UserGuide/index.html?LimitationsOnEntities.html"
	 * target="_blank">Limitations on IAM Entities</a> in <em>Using AWS Identity and Access
	 * Management</em>.
	 * 
	 * <p class="note">
	 * Because policy documents can be large, you should use POST rather than GET when calling
	 * <code>PutGroupPolicy</code>. For more information, see <a href=
	 * "http://docs.amazonwebservices.com/IAM/latest/UserGuide/index.html?IAM_UsingQueryAPI.html"
	 * target="_blank">Making Query Requests</a> in <em>Using AWS Identity and Access Management</em>.
	 * </p>
	 *
	 * @param string $group_name (Required) Name of the group to associate the policy with. [Constraints: The value must be between 1 and 128 characters, and must match the following regular expression pattern: <code>[\w+=,.@-]*</code>]
	 * @param string $policy_name (Required) Name of the policy document. [Constraints: The value must be between 1 and 128 characters, and must match the following regular expression pattern: <code>[\w+=,.@-]*</code>]
	 * @param string $policy_document (Required) The policy document. [Constraints: The value must be between 1 and 131072 characters, and must match the following regular expression pattern: <code>[\u0009\u000A\u000D\u0020-\u00FF]+</code>]
	 * @param array $opt (Optional) An associative array of parameters that can have the following keys: <ul>
	 * 	<li><code>curlopts</code> - <code>array</code> - Optional - A set of values to pass directly into <code>curl_setopt()</code>, where the key is a pre-defined <code>CURLOPT_*</code> constant.</li>
	 * 	<li><code>returnCurlHandle</code> - <code>boolean</code> - Optional - A private toggle specifying that the cURL handle be returned rather than actually completing the request. This toggle is useful for manually managed batch requests.</li></ul>
	 * @return CFResponse A <CFResponse> object containing a parsed HTTP response.
	 */
	public function put_group_policy($group_name, $policy_name, $policy_document, $opt = null)
	{
		if (!$opt) $opt = array();
		$opt['GroupName'] = $group_name;
		$opt['PolicyName'] = $policy_name;
		$opt['PolicyDocument'] = $policy_document;
		
		return $this->authenticate('PutGroupPolicy', $opt);
	}

	/**
	 * Adds (or updates) a policy document associated with the specified user. For information about
	 * policies, refer to <a href=
	 * "http://docs.amazonwebservices.com/IAM/latest/UserGuide/index.html?PoliciesOverview.html"
	 * target="_blank">Overview of Policies</a> in <em>Using AWS Identity and Access Management</em>.
	 *  
	 * For information about limits on the number of policies you can associate with a user, see
	 * 	<a href=
	 * "http://docs.amazonwebservices.com/IAM/latest/UserGuide/index.html?LimitationsOnEntities.html"
	 * target="_blank">Limitations on IAM Entities</a> in <em>Using AWS Identity and Access
	 * Management</em>.
	 * 
	 * <p class="note">
	 * Because policy documents can be large, you should use POST rather than GET when calling
	 * <code>PutUserPolicy</code>. For more information, see <a href=
	 * "http://docs.amazonwebservices.com/IAM/latest/UserGuide/index.html?IAM_UsingQueryAPI.html"
	 * target="_blank">Making Query Requests</a> in <em>Using AWS Identity and Access Management</em>.
	 * </p>
	 *
	 * @param string $user_name (Required) Name of the user to associate the policy with. [Constraints: The value must be between 1 and 64 characters, and must match the following regular expression pattern: <code>[\w+=,.@-]*</code>]
	 * @param string $policy_name (Required) Name of the policy document. [Constraints: The value must be between 1 and 128 characters, and must match the following regular expression pattern: <code>[\w+=,.@-]*</code>]
	 * @param string $policy_document (Required) The policy document. [Constraints: The value must be between 1 and 131072 characters, and must match the following regular expression pattern: <code>[\u0009\u000A\u000D\u0020-\u00FF]+</code>]
	 * @param array $opt (Optional) An associative array of parameters that can have the following keys: <ul>
	 * 	<li><code>curlopts</code> - <code>array</code> - Optional - A set of values to pass directly into <code>curl_setopt()</code>, where the key is a pre-defined <code>CURLOPT_*</code> constant.</li>
	 * 	<li><code>returnCurlHandle</code> - <code>boolean</code> - Optional - A private toggle specifying that the cURL handle be returned rather than actually completing the request. This toggle is useful for manually managed batch requests.</li></ul>
	 * @return CFResponse A <CFResponse> object containing a parsed HTTP response.
	 */
	public function put_user_policy($user_name, $policy_name, $policy_document, $opt = null)
	{
		if (!$opt) $opt = array();
		$opt['UserName'] = $user_name;
		$opt['PolicyName'] = $policy_name;
		$opt['PolicyDocument'] = $policy_document;
		
		return $this->authenticate('PutUserPolicy', $opt);
	}

	/**
	 * Removes the specified user from the specified group.
	 *
	 * @param string $group_name (Required) Name of the group to update. [Constraints: The value must be between 1 and 128 characters, and must match the following regular expression pattern: <code>[\w+=,.@-]*</code>]
	 * @param string $user_name (Required) Name of the user to remove. [Constraints: The value must be between 1 and 64 characters, and must match the following regular expression pattern: <code>[\w+=,.@-]*</code>]
	 * @param array $opt (Optional) An associative array of parameters that can have the following keys: <ul>
	 * 	<li><code>curlopts</code> - <code>array</code> - Optional - A set of values to pass directly into <code>curl_setopt()</code>, where the key is a pre-defined <code>CURLOPT_*</code> constant.</li>
	 * 	<li><code>returnCurlHandle</code> - <code>boolean</code> - Optional - A private toggle specifying that the cURL handle be returned rather than actually completing the request. This toggle is useful for manually managed batch requests.</li></ul>
	 * @return CFResponse A <CFResponse> object containing a parsed HTTP response.
	 */
	public function remove_user_from_group($group_name, $user_name, $opt = null)
	{
		if (!$opt) $opt = array();
		$opt['GroupName'] = $group_name;
		$opt['UserName'] = $user_name;
		
		return $this->authenticate('RemoveUserFromGroup', $opt);
	}

	/**
	 * Synchronizes the specified MFA device with AWS servers.
	 *
	 * @param string $user_name (Required) Name of the user whose MFA device you want to resynchronize. [Constraints: The value must be between 1 and 64 characters, and must match the following regular expression pattern: <code>[\w+=,.@-]*</code>]
	 * @param string $serial_number (Required) Serial number that uniquely identifies the MFA device. [Constraints: The value must be between 9 and 256 characters, and must match the following regular expression pattern: <code>[\w+=/:,.@-]*</code>]
	 * @param string $authentication_code1 (Required) An authentication code emitted by the device. [Constraints: The value must be between 6 and 6 characters, and must match the following regular expression pattern: <code>[\d]*</code>]
	 * @param string $authentication_code2 (Required) A subsequent authentication code emitted by the device. [Constraints: The value must be between 6 and 6 characters, and must match the following regular expression pattern: <code>[\d]*</code>]
	 * @param array $opt (Optional) An associative array of parameters that can have the following keys: <ul>
	 * 	<li><code>curlopts</code> - <code>array</code> - Optional - A set of values to pass directly into <code>curl_setopt()</code>, where the key is a pre-defined <code>CURLOPT_*</code> constant.</li>
	 * 	<li><code>returnCurlHandle</code> - <code>boolean</code> - Optional - A private toggle specifying that the cURL handle be returned rather than actually completing the request. This toggle is useful for manually managed batch requests.</li></ul>
	 * @return CFResponse A <CFResponse> object containing a parsed HTTP response.
	 */
	public function resync_mfa_device($user_name, $serial_number, $authentication_code1, $authentication_code2, $opt = null)
	{
		if (!$opt) $opt = array();
		$opt['UserName'] = $user_name;
		$opt['SerialNumber'] = $serial_number;
		$opt['AuthenticationCode1'] = $authentication_code1;
		$opt['AuthenticationCode2'] = $authentication_code2;
		
		return $this->authenticate('ResyncMFADevice', $opt);
	}

	/**
	 * Changes the status of the specified access key from Active to Inactive, or vice versa. This
	 * action can be used to disable a user's key as part of a key rotation work flow.
	 *  
	 * If the <code>UserName</code> field is not specified, the UserName is determined implicitly
	 * based on the AWS Access Key ID used to sign the request. Because this action works for access
	 * keys under the AWS account, this API can be used to manage root credentials even if the AWS
	 * account has no associated users.
	 *  
	 * For information about rotating keys, see <a href=
	 * "http://docs.amazonwebservices.com/IAM/latest/UserGuide/index.html?ManagingCredentials.html"
	 * target="_blank">Managing Keys and Certificates</a> in <em>Using AWS Identity and Access
	 * Management</em>.
	 *
	 * @param string $access_key_id (Required) The Access Key ID of the Secret Access Key you want to update. [Constraints: The value must be between 16 and 32 characters, and must match the following regular expression pattern: <code>[\w]*</code>]
	 * @param string $status (Required) The status you want to assign to the Secret Access Key. <code>Active</code> means the key can be used for API calls to AWS, while <code>Inactive</code> means the key cannot be used. [Allowed values: <code>Active</code>, <code>Inactive</code>]
	 * @param array $opt (Optional) An associative array of parameters that can have the following keys: <ul>
	 * 	<li><code>UserName</code> - <code>string</code> - Optional - Name of the user whose key you want to update. [Constraints: The value must be between 1 and 64 characters, and must match the following regular expression pattern: <code>[\w+=,.@-]*</code>]</li>
	 * 	<li><code>curlopts</code> - <code>array</code> - Optional - A set of values to pass directly into <code>curl_setopt()</code>, where the key is a pre-defined <code>CURLOPT_*</code> constant.</li>
	 * 	<li><code>returnCurlHandle</code> - <code>boolean</code> - Optional - A private toggle specifying that the cURL handle be returned rather than actually completing the request. This toggle is useful for manually managed batch requests.</li></ul>
	 * @return CFResponse A <CFResponse> object containing a parsed HTTP response.
	 */
	public function update_access_key($access_key_id, $status, $opt = null)
	{
		if (!$opt) $opt = array();
		$opt['AccessKeyId'] = $access_key_id;
		$opt['Status'] = $status;
		
		return $this->authenticate('UpdateAccessKey', $opt);
	}

	/**
	 * Updates the name and/or the path of the specified group.
	 * 
	 * <p class="important">
	 * You should understand the implications of changing a group's path or name. For more
	 * information, see <a href=
	 * "http://docs.amazonwebservices.com/IAM/latest/UserGuide/index.html?Using_Renaming.html" target=
	 * "_blank">Renaming Users and Groups</a> in <em>Using AWS Identity and Access Management</em>.
	 * </p>
	 * <p class="note">
	 * To change a group name the requester must have appropriate permissions on both the source
	 * object and the target object. For example, to change Managers to MGRs, the entity making the
	 * request must have permission on Managers and MGRs, or must have permission on all (*). For more
	 * information about permissions, see <a href=
	 * "http://docs.amazonwebservices.com/IAM/latest/UserGuide/PermissionsAndPolicies.html" target=
	 * "blank">Permissions and Policies</a>.
	 * </p>
	 *
	 * @param string $group_name (Required) Name of the group to update. If you're changing the name of the group, this is the original name. [Constraints: The value must be between 1 and 128 characters, and must match the following regular expression pattern: <code>[\w+=,.@-]*</code>]
	 * @param array $opt (Optional) An associative array of parameters that can have the following keys: <ul>
	 * 	<li><code>NewPath</code> - <code>string</code> - Optional - New path for the group. Only include this if changing the group's path. [Constraints: The value must be between 1 and 512 characters, and must match the following regular expression pattern: <code>(\u002F)|(\u002F[\u0021-\u007F]+\u002F)</code>]</li>
	 * 	<li><code>NewGroupName</code> - <code>string</code> - Optional - New name for the group. Only include this if changing the group's name. [Constraints: The value must be between 1 and 128 characters, and must match the following regular expression pattern: <code>[\w+=,.@-]*</code>]</li>
	 * 	<li><code>curlopts</code> - <code>array</code> - Optional - A set of values to pass directly into <code>curl_setopt()</code>, where the key is a pre-defined <code>CURLOPT_*</code> constant.</li>
	 * 	<li><code>returnCurlHandle</code> - <code>boolean</code> - Optional - A private toggle specifying that the cURL handle be returned rather than actually completing the request. This toggle is useful for manually managed batch requests.</li></ul>
	 * @return CFResponse A <CFResponse> object containing a parsed HTTP response.
	 */
	public function update_group($group_name, $opt = null)
	{
		if (!$opt) $opt = array();
		$opt['GroupName'] = $group_name;
		
		return $this->authenticate('UpdateGroup', $opt);
	}

	/**
	 * Updates the login profile for the specified user. Use this API to change the user's password.
	 *
	 * @param string $user_name (Required) Name of the user whose login profile you want to update. [Constraints: The value must be between 1 and 64 characters, and must match the following regular expression pattern: <code>[\w+=,.@-]*</code>]
	 * @param array $opt (Optional) An associative array of parameters that can have the following keys: <ul>
	 * 	<li><code>Password</code> - <code>string</code> - Optional - The new password for the user name. [Constraints: The value must be between 1 and 128 characters, and must match the following regular expression pattern: <code>[\u0009\u000A\u000D\u0020-\u00FF]+</code>]</li>
	 * 	<li><code>curlopts</code> - <code>array</code> - Optional - A set of values to pass directly into <code>curl_setopt()</code>, where the key is a pre-defined <code>CURLOPT_*</code> constant.</li>
	 * 	<li><code>returnCurlHandle</code> - <code>boolean</code> - Optional - A private toggle specifying that the cURL handle be returned rather than actually completing the request. This toggle is useful for manually managed batch requests.</li></ul>
	 * @return CFResponse A <CFResponse> object containing a parsed HTTP response.
	 */
	public function update_login_profile($user_name, $opt = null)
	{
		if (!$opt) $opt = array();
		$opt['UserName'] = $user_name;
		
		return $this->authenticate('UpdateLoginProfile', $opt);
	}

	/**
	 * Updates the name and/or the path of the specified server certificate.
	 * 
	 * <p class="important">
	 * You should understand the implications of changing a server certificate's path or name. For
	 * more information, see <a href=
	 * "http://docs.amazonwebservices.com/IAM/latest/UserGuide/ManagingServerCerts.html" target=
	 * "_blank">Managing Server Certificates</a> in <em>Using AWS Identity and Access Management</em>.
	 * </p>
	 * <p class="note">
	 * To change a server certificate name the requester must have appropriate permissions on both the
	 * source object and the target object. For example, to change the name from ProductionCert to
	 * ProdCert, the entity making the request must have permission on ProductionCert and ProdCert, or
	 * must have permission on all (*). For more information about permissions, see <a href=
	 * "http://docs.amazonwebservices.com/IAM/latest/UserGuide/PermissionsAndPolicies.html" target=
	 * "blank">Permissions and Policies</a>.
	 * </p>
	 *
	 * @param string $server_certificate_name (Required) The name of the server certificate that you want to update. [Constraints: The value must be between 1 and 128 characters, and must match the following regular expression pattern: <code>[\w+=,.@-]*</code>]
	 * @param array $opt (Optional) An associative array of parameters that can have the following keys: <ul>
	 * 	<li><code>NewPath</code> - <code>string</code> - Optional - The new path for the server certificate. Include this only if you are updating the server certificate's path. [Constraints: The value must be between 1 and 512 characters, and must match the following regular expression pattern: <code>(\u002F)|(\u002F[\u0021-\u007F]+\u002F)</code>]</li>
	 * 	<li><code>NewServerCertificateName</code> - <code>string</code> - Optional - The new name for the server certificate. Include this only if you are updating the server certificate's name. [Constraints: The value must be between 1 and 128 characters, and must match the following regular expression pattern: <code>[\w+=,.@-]*</code>]</li>
	 * 	<li><code>curlopts</code> - <code>array</code> - Optional - A set of values to pass directly into <code>curl_setopt()</code>, where the key is a pre-defined <code>CURLOPT_*</code> constant.</li>
	 * 	<li><code>returnCurlHandle</code> - <code>boolean</code> - Optional - A private toggle specifying that the cURL handle be returned rather than actually completing the request. This toggle is useful for manually managed batch requests.</li></ul>
	 * @return CFResponse A <CFResponse> object containing a parsed HTTP response.
	 */
	public function update_server_certificate($server_certificate_name, $opt = null)
	{
		if (!$opt) $opt = array();
		$opt['ServerCertificateName'] = $server_certificate_name;
		
		return $this->authenticate('UpdateServerCertificate', $opt);
	}

	/**
	 * Changes the status of the specified signing certificate from active to disabled, or vice versa.
	 * This action can be used to disable a user's signing certificate as part of a certificate
	 * rotation work flow.
	 *  
	 * If the <code>UserName</code> field is not specified, the UserName is determined implicitly
	 * based on the AWS Access Key ID used to sign the request. Because this action works for access
	 * keys under the AWS account, this API can be used to manage root credentials even if the AWS
	 * account has no associated users.
	 *  
	 * For information about rotating certificates, see <a href=
	 * "http://docs.amazonwebservices.com/IAM/latest/UserGuide/index.html?ManagingCredentials.html"
	 * target="_blank">Managing Keys and Certificates</a> in <em>Using AWS Identity and Access
	 * Management</em>.
	 *
	 * @param string $certificate_id (Required) The ID of the signing certificate you want to update. [Constraints: The value must be between 24 and 128 characters, and must match the following regular expression pattern: <code>[\w]*</code>]
	 * @param string $status (Required) The status you want to assign to the certificate. <code>Active</code> means the certificate can be used for API calls to AWS, while <code>Inactive</code> means the certificate cannot be used. [Allowed values: <code>Active</code>, <code>Inactive</code>]
	 * @param array $opt (Optional) An associative array of parameters that can have the following keys: <ul>
	 * 	<li><code>UserName</code> - <code>string</code> - Optional - Name of the user the signing certificate belongs to. [Constraints: The value must be between 1 and 64 characters, and must match the following regular expression pattern: <code>[\w+=,.@-]*</code>]</li>
	 * 	<li><code>curlopts</code> - <code>array</code> - Optional - A set of values to pass directly into <code>curl_setopt()</code>, where the key is a pre-defined <code>CURLOPT_*</code> constant.</li>
	 * 	<li><code>returnCurlHandle</code> - <code>boolean</code> - Optional - A private toggle specifying that the cURL handle be returned rather than actually completing the request. This toggle is useful for manually managed batch requests.</li></ul>
	 * @return CFResponse A <CFResponse> object containing a parsed HTTP response.
	 */
	public function update_signing_certificate($certificate_id, $status, $opt = null)
	{
		if (!$opt) $opt = array();
		$opt['CertificateId'] = $certificate_id;
		$opt['Status'] = $status;
		
		return $this->authenticate('UpdateSigningCertificate', $opt);
	}

	/**
	 * Updates the name and/or the path of the specified user.
	 * 
	 * <p class="important">
	 * You should understand the implications of changing a user's path or name. For more information,
	 * see <a href=
	 * "http://docs.amazonwebservices.com/IAM/latest/UserGuide/index.html?Using_Renaming.html" target=
	 * "_blank">Renaming Users and Groups</a> in <em>Using AWS Identity and Access Management</em>.
	 * </p>
	 * <p class="note">
	 * To change a user name the requester must have appropriate permissions on both the source object
	 * and the target object. For example, to change Bob to Robert, the entity making the request must
	 * have permission on Bob and Robert, or must have permission on all (*). For more information
	 * about permissions, see <a href=
	 * "http://docs.amazonwebservices.com/IAM/latest/UserGuide/PermissionsAndPolicies.html" target=
	 * "blank">Permissions and Policies</a>.
	 * </p>
	 *
	 * @param string $user_name (Required) Name of the user to update. If you're changing the name of the user, this is the original user name. [Constraints: The value must be between 1 and 64 characters, and must match the following regular expression pattern: <code>[\w+=,.@-]*</code>]
	 * @param array $opt (Optional) An associative array of parameters that can have the following keys: <ul>
	 * 	<li><code>NewPath</code> - <code>string</code> - Optional - New path for the user. Include this parameter only if you're changing the user's path. [Constraints: The value must be between 1 and 512 characters, and must match the following regular expression pattern: <code>(\u002F)|(\u002F[\u0021-\u007F]+\u002F)</code>]</li>
	 * 	<li><code>NewUserName</code> - <code>string</code> - Optional - New name for the user. Include this parameter only if you're changing the user's name. [Constraints: The value must be between 1 and 64 characters, and must match the following regular expression pattern: <code>[\w+=,.@-]*</code>]</li>
	 * 	<li><code>curlopts</code> - <code>array</code> - Optional - A set of values to pass directly into <code>curl_setopt()</code>, where the key is a pre-defined <code>CURLOPT_*</code> constant.</li>
	 * 	<li><code>returnCurlHandle</code> - <code>boolean</code> - Optional - A private toggle specifying that the cURL handle be returned rather than actually completing the request. This toggle is useful for manually managed batch requests.</li></ul>
	 * @return CFResponse A <CFResponse> object containing a parsed HTTP response.
	 */
	public function update_user($user_name, $opt = null)
	{
		if (!$opt) $opt = array();
		$opt['UserName'] = $user_name;
		
		return $this->authenticate('UpdateUser', $opt);
	}

	/**
	 * Uploads a server certificate entity for the AWS account. The server certificate entity includes
	 * a public key certificate, a private key, and an optional certificate chain, which should all be
	 * PEM-encoded.
	 *  
	 * For information about the number of server certificates you can upload, see <a href=
	 * "http://docs.amazonwebservices.com/IAM/latest/UserGuide/index.html?LimitationsOnEntities.html"
	 * target="_blank">Limitations on IAM Entities</a> in <em>Using AWS Identity and Access
	 * Management</em>.
	 * 
	 * <p class="note">
	 * Because the body of the public key certificate, private key, and the certificate chain can be
	 * large, you should use POST rather than GET when calling <code>UploadServerCertificate</code>.
	 * For more information, see <a href=
	 * "http://docs.amazonwebservices.com/IAM/latest/UserGuide/IAM_UsingQueryAPI.html" target=
	 * "_blank">Making Query Requests</a> in <em>Using AWS Identity and Access Management</em>.
	 * </p>
	 *
	 * @param string $server_certificate_name (Required) The name for the server certificate. Do not include the path in this value. [Constraints: The value must be between 1 and 128 characters, and must match the following regular expression pattern: <code>[\w+=,.@-]*</code>]
	 * @param string $certificate_body (Required) The contents of the public key certificate in PEM-encoded format. [Constraints: The value must be between 1 and 16384 characters, and must match the following regular expression pattern: <code>[\u0009\u000A\u000D\u0020-\u00FF]+</code>]
	 * @param string $private_key (Required) The contents of the private key in PEM-encoded format. [Constraints: The value must be between 1 and 16384 characters, and must match the following regular expression pattern: <code>[\u0009\u000A\u000D\u0020-\u00FF]*</code>]
	 * @param array $opt (Optional) An associative array of parameters that can have the following keys: <ul>
	 * 	<li><code>Path</code> - <code>string</code> - Optional - The path for the server certificate. For more information about paths, see <a href="http://docs.amazonwebservices.com/IAM/latest/UserGuide/index.html?Using_Identifiers.html" target="_blank">Identifiers for IAM Entities</a> in <em>Using AWS Identity and Access Management</em>. This parameter is optional. If it is not included, it defaults to a slash (/). [Constraints: The value must be between 1 and 512 characters, and must match the following regular expression pattern: <code>(\u002F)|(\u002F[\u0021-\u007F]+\u002F)</code>]</li>
	 * 	<li><code>CertificateChain</code> - <code>string</code> - Optional - The contents of the certificate chain. This is typically a concatenation of the PEM-encoded public key certificates of the chain. [Constraints: The value must be between 1 and 2097152 characters, and must match the following regular expression pattern: <code>[\u0009\u000A\u000D\u0020-\u00FF]*</code>]</li>
	 * 	<li><code>curlopts</code> - <code>array</code> - Optional - A set of values to pass directly into <code>curl_setopt()</code>, where the key is a pre-defined <code>CURLOPT_*</code> constant.</li>
	 * 	<li><code>returnCurlHandle</code> - <code>boolean</code> - Optional - A private toggle specifying that the cURL handle be returned rather than actually completing the request. This toggle is useful for manually managed batch requests.</li></ul>
	 * @return CFResponse A <CFResponse> object containing a parsed HTTP response.
	 */
	public function upload_server_certificate($server_certificate_name, $certificate_body, $private_key, $opt = null)
	{
		if (!$opt) $opt = array();
		$opt['ServerCertificateName'] = $server_certificate_name;
		$opt['CertificateBody'] = $certificate_body;
		$opt['PrivateKey'] = $private_key;
		
		return $this->authenticate('UploadServerCertificate', $opt);
	}

	/**
	 * Uploads an X.509 signing certificate and associates it with the specified user. Some AWS
	 * services use X.509 signing certificates to validate requests that are signed with a
	 * corresponding private key. When you upload the certificate, its default status is
	 * <code>Active</code>.
	 *  
	 * If the <code>UserName</code> field is not specified, the user name is determined implicitly
	 * based on the AWS Access Key ID used to sign the request. Because this action works for access
	 * keys under the AWS account, this API can be used to manage root credentials even if the AWS
	 * account has no associated users.
	 * 
	 * <p class="note">
	 * Because the body of a X.509 certificate can be large, you should use POST rather than GET when
	 * calling <code>UploadSigningCertificate</code>. For more information, see <a href=
	 * "http://docs.amazonwebservices.com/IAM/latest/UserGuide/index.html?IAM_UsingQueryAPI.html"
	 * target="_blank">Making Query Requests</a> in <em>Using AWS Identity and Access Management</em>.
	 * </p>
	 *
	 * @param string $certificate_body (Required) The contents of the signing certificate. [Constraints: The value must be between 1 and 16384 characters, and must match the following regular expression pattern: <code>[\u0009\u000A\u000D\u0020-\u00FF]+</code>]
	 * @param array $opt (Optional) An associative array of parameters that can have the following keys: <ul>
	 * 	<li><code>UserName</code> - <code>string</code> - Optional - Name of the user the signing certificate is for. [Constraints: The value must be between 1 and 64 characters, and must match the following regular expression pattern: <code>[\w+=,.@-]*</code>]</li>
	 * 	<li><code>curlopts</code> - <code>array</code> - Optional - A set of values to pass directly into <code>curl_setopt()</code>, where the key is a pre-defined <code>CURLOPT_*</code> constant.</li>
	 * 	<li><code>returnCurlHandle</code> - <code>boolean</code> - Optional - A private toggle specifying that the cURL handle be returned rather than actually completing the request. This toggle is useful for manually managed batch requests.</li></ul>
	 * @return CFResponse A <CFResponse> object containing a parsed HTTP response.
	 */
	public function upload_signing_certificate($certificate_body, $opt = null)
	{
		if (!$opt) $opt = array();
		$opt['CertificateBody'] = $certificate_body;
		
		return $this->authenticate('UploadSigningCertificate', $opt);
	}
}


/*%******************************************************************************************%*/
// EXCEPTIONS

class IAM_Exception extends Exception {}
