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
 * This is the AWS Elastic Beanstalk API Reference. This guide provides detailed information about
 * AWS Elastic Beanstalk actions, data types, parameters, and errors.
 *  
 * AWS Elastic Beanstalk is a tool that makes it easy for you to create, deploy, and manage
 * scalable, fault-tolerant applications running on Amazon Web Services cloud resources.
 *  
 * For more information about this product, go to the <a href=
 * "http://aws.amazon.com/elasticbeanstalk/">AWS Elastic Beanstalk</a> details page. The location
 * of the lastest AWS Elastic Beanstalk WSDL is <a href=
 * "http://elasticbeanstalk.s3.amazonaws.com/doc/2010-12-01/AWSElasticBeanstalk.wsdl">http://elasticbeanstalk.s3.amazonaws.com/doc/2010-12-01/AWSElasticBeanstalk.wsdl</a>.
 *  
 * <strong>Endpoints</strong>
 *  
 * AWS Elastic Beanstalk supports the following region-specific endpoint:
 * 
 * <ul>
 * 	<li>https://elasticbeanstalk.us-east-1.amazonaws.com</li>
 * </ul>
 *
 * @version 2012.01.16
 * @license See the included NOTICE.md file for complete information.
 * @copyright See the included NOTICE.md file for complete information.
 * @link http://aws.amazon.com/elasticbeanstalk/ AWS ElasticBeanstalk
 * @link http://aws.amazon.com/elasticbeanstalk/documentation/ AWS ElasticBeanstalk documentation
 */
class AmazonElasticBeanstalk extends CFRuntime
{
	/*%******************************************************************************************%*/
	// CLASS CONSTANTS

	/**
	 * Specify the queue URL for the United States East (Northern Virginia) Region.
	 */
	const REGION_US_E1 = 'elasticbeanstalk.us-east-1.amazonaws.com';

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
	 * Constructs a new instance of <AmazonElasticBeanstalk>.
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
		$this->api_version = '2010-12-01';
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
	 * Checks if the specified CNAME is available.
	 *
	 * @param string $cnameprefix (Required) The prefix used when this CNAME is reserved.
	 * @param array $opt (Optional) An associative array of parameters that can have the following keys: <ul>
	 * 	<li><code>curlopts</code> - <code>array</code> - Optional - A set of values to pass directly into <code>curl_setopt()</code>, where the key is a pre-defined <code>CURLOPT_*</code> constant.</li>
	 * 	<li><code>returnCurlHandle</code> - <code>boolean</code> - Optional - A private toggle specifying that the cURL handle be returned rather than actually completing the request. This toggle is useful for manually managed batch requests.</li></ul>
	 * @return CFResponse A <CFResponse> object containing a parsed HTTP response.
	 */
	public function check_dns_availability($cnameprefix, $opt = null)
	{
		if (!$opt) $opt = array();
		$opt['CNAMEPrefix'] = $cnameprefix;
		
		return $this->authenticate('CheckDNSAvailability', $opt);
	}

	/**
	 * Creates an application that has one configuration template named <code>default</code> and no
	 * application versions.
	 * 
	 * <p class="note">
	 * The <code>default</code> configuration template is for a 32-bit version of the Amazon Linux
	 * operating system running the Tomcat 6 application container.
	 * </p>
	 *
	 * @param string $application_name (Required) The name of the application. Constraint: This name must be unique within your account. If the specified name already exists, the action returns an <code>InvalidParameterValue</code> error.
	 * @param array $opt (Optional) An associative array of parameters that can have the following keys: <ul>
	 * 	<li><code>Description</code> - <code>string</code> - Optional - Describes the application.</li>
	 * 	<li><code>curlopts</code> - <code>array</code> - Optional - A set of values to pass directly into <code>curl_setopt()</code>, where the key is a pre-defined <code>CURLOPT_*</code> constant.</li>
	 * 	<li><code>returnCurlHandle</code> - <code>boolean</code> - Optional - A private toggle specifying that the cURL handle be returned rather than actually completing the request. This toggle is useful for manually managed batch requests.</li></ul>
	 * @return CFResponse A <CFResponse> object containing a parsed HTTP response.
	 */
	public function create_application($application_name, $opt = null)
	{
		if (!$opt) $opt = array();
		$opt['ApplicationName'] = $application_name;
		
		return $this->authenticate('CreateApplication', $opt);
	}

	/**
	 * Creates an application version for the specified application.
	 * 
	 * <p class="note">
	 * Once you create an application version with a specified Amazon S3 bucket and key location, you
	 * cannot change that Amazon S3 location. If you change the Amazon S3 location, you receive an
	 * exception when you attempt to launch an environment from the application version.
	 * </p>
	 *
	 * @param string $application_name (Required) The name of the application. If no application is found with this name, and <code>AutoCreateApplication</code> is <code>false</code>, returns an <code>InvalidParameterValue</code> error.
	 * @param string $version_label (Required) A label identifying this version. Constraint: Must be unique per application. If an application version already exists with this label for the specified application, AWS Elastic Beanstalk returns an <code>InvalidParameterValue</code> error.
	 * @param array $opt (Optional) An associative array of parameters that can have the following keys: <ul>
	 * 	<li><code>Description</code> - <code>string</code> - Optional - Describes this version.</li>
	 * 	<li><code>SourceBundle</code> - <code>array</code> - Optional - The Amazon S3 bucket and key that identify the location of the source bundle for this version. If data found at the Amazon S3 location exceeds the maximum allowed source bundle size, AWS Elastic Beanstalk returns an <code>InvalidParameterValue</code> error. Default: If not specified, AWS Elastic Beanstalk uses a sample application. If only partially specified (for example, a bucket is provided but not the key) or if no data is found at the Amazon S3 location, AWS Elastic Beanstalk returns an <code>InvalidParameterCombination</code> error. <ul>
	 * 		<li><code>x</code> - <code>array</code> - Optional - This represents a simple array index. <ul>
	 * 			<li><code>S3Bucket</code> - <code>string</code> - Optional - The Amazon S3 bucket where the data is located.</li>
	 * 			<li><code>S3Key</code> - <code>string</code> - Optional - The Amazon S3 key where the data is located.</li>
	 * 		</ul></li>
	 * 	</ul></li>
	 * 	<li><code>AutoCreateApplication</code> - <code>boolean</code> - Optional - Determines how the system behaves if the specified application for this version does not already exist:<enumValues><value name="true"> <code>true</code>: Automatically creates the specified application for this version if it does not already exist.</value><value name="false"> <code>false</code>: Returns an <code>InvalidParameterValue</code> if the specified application for this version does not already exist.</value></enumValues><ul><li> <code>true</code>: Automatically creates the specified application for this release if it does not already exist.</li><li> <code>false</code>: Throws an <code>InvalidParameterValue</code> if the specified application for this release does not already exist.</li></ul>Default: <code>false</code> Valid Values: <code>true</code> | <code>false</code></li>
	 * 	<li><code>curlopts</code> - <code>array</code> - Optional - A set of values to pass directly into <code>curl_setopt()</code>, where the key is a pre-defined <code>CURLOPT_*</code> constant.</li>
	 * 	<li><code>returnCurlHandle</code> - <code>boolean</code> - Optional - A private toggle specifying that the cURL handle be returned rather than actually completing the request. This toggle is useful for manually managed batch requests.</li></ul>
	 * @return CFResponse A <CFResponse> object containing a parsed HTTP response.
	 */
	public function create_application_version($application_name, $version_label, $opt = null)
	{
		if (!$opt) $opt = array();
		$opt['ApplicationName'] = $application_name;
		$opt['VersionLabel'] = $version_label;
		
		// Optional map (non-list)
		if (isset($opt['SourceBundle']))
		{
			$opt = array_merge($opt, CFComplexType::map(array(
				'SourceBundle' => $opt['SourceBundle']
			), 'member'));
			unset($opt['SourceBundle']);
		}

		return $this->authenticate('CreateApplicationVersion', $opt);
	}

	/**
	 * Creates a configuration template. Templates are associated with a specific application and are
	 * used to deploy different versions of the application with the same configuration settings.
	 *  
	 * Related Topics
	 * 
	 * <ul>
	 * 	<li><code>DescribeConfigurationOptions</code></li>
	 * 	<li><code>DescribeConfigurationSettings</code></li>
	 * 	<li><code>ListAvailableSolutionStacks</code></li>
	 * </ul>
	 *
	 * @param string $application_name (Required) The name of the application to associate with this configuration template. If no application is found with this name, AWS Elastic Beanstalk returns an <code>InvalidParameterValue</code> error.
	 * @param string $template_name (Required) The name of the configuration template. Constraint: This name must be unique per application. Default: If a configuration template already exists with this name, AWS Elastic Beanstalk returns an <code>InvalidParameterValue</code> error.
	 * @param array $opt (Optional) An associative array of parameters that can have the following keys: <ul>
	 * 	<li><code>SolutionStackName</code> - <code>string</code> - Optional - The name of the solution stack used by this configuration. The solution stack specifies the operating system, architecture, and application server for a configuration template. It determines the set of configuration options as well as the possible and default values. Use <code>ListAvailableSolutionStacks</code> to obtain a list of available solution stacks. Default: If the <code>SolutionStackName</code> is not specified and the source configuration parameter is blank, AWS Elastic Beanstalk uses the default solution stack. If not specified and the source configuration parameter is specified, AWS Elastic Beanstalk uses the same solution stack as the source configuration template.</li>
	 * 	<li><code>SourceConfiguration</code> - <code>array</code> - Optional - If specified, AWS Elastic Beanstalk uses the configuration values from the specified configuration template to create a new configuration. Values specified in the <code>OptionSettings</code> parameter of this call overrides any values obtained from the <code>SourceConfiguration</code>. If no configuration template is found, returns an <code>InvalidParameterValue</code> error. Constraint: If both the solution stack name parameter and the source configuration parameters are specified, the solution stack of the source configuration template must match the specified solution stack name or else AWS Elastic Beanstalk returns an <code>InvalidParameterCombination</code> error. <ul>
	 * 		<li><code>x</code> - <code>array</code> - Optional - This represents a simple array index. <ul>
	 * 			<li><code>ApplicationName</code> - <code>string</code> - Optional - The name of the application associated with the configuration.</li>
	 * 			<li><code>TemplateName</code> - <code>string</code> - Optional - The name of the configuration template.</li>
	 * 		</ul></li>
	 * 	</ul></li>
	 * 	<li><code>EnvironmentId</code> - <code>string</code> - Optional - The ID of the environment used with this configuration template.</li>
	 * 	<li><code>Description</code> - <code>string</code> - Optional - Describes this configuration.</li>
	 * 	<li><code>OptionSettings</code> - <code>array</code> - Optional - If specified, AWS Elastic Beanstalk sets the specified configuration option to the requested value. The new value overrides the value obtained from the solution stack or the source configuration template. <ul>
	 * 		<li><code>x</code> - <code>array</code> - Optional - This represents a simple array index. <ul>
	 * 			<li><code>Namespace</code> - <code>string</code> - Optional - A unique namespace identifying the option's associated AWS resource.</li>
	 * 			<li><code>OptionName</code> - <code>string</code> - Optional - The name of the configuration option.</li>
	 * 			<li><code>Value</code> - <code>string</code> - Optional - The current value for the configuration option.</li>
	 * 		</ul></li>
	 * 	</ul></li>
	 * 	<li><code>curlopts</code> - <code>array</code> - Optional - A set of values to pass directly into <code>curl_setopt()</code>, where the key is a pre-defined <code>CURLOPT_*</code> constant.</li>
	 * 	<li><code>returnCurlHandle</code> - <code>boolean</code> - Optional - A private toggle specifying that the cURL handle be returned rather than actually completing the request. This toggle is useful for manually managed batch requests.</li></ul>
	 * @return CFResponse A <CFResponse> object containing a parsed HTTP response.
	 */
	public function create_configuration_template($application_name, $template_name, $opt = null)
	{
		if (!$opt) $opt = array();
		$opt['ApplicationName'] = $application_name;
		$opt['TemplateName'] = $template_name;
		
		// Optional map (non-list)
		if (isset($opt['SourceConfiguration']))
		{
			$opt = array_merge($opt, CFComplexType::map(array(
				'SourceConfiguration' => $opt['SourceConfiguration']
			), 'member'));
			unset($opt['SourceConfiguration']);
		}
		
		// Optional list + map
		if (isset($opt['OptionSettings']))
		{
			$opt = array_merge($opt, CFComplexType::map(array(
				'OptionSettings' => $opt['OptionSettings']
			), 'member'));
			unset($opt['OptionSettings']);
		}

		return $this->authenticate('CreateConfigurationTemplate', $opt);
	}

	/**
	 * Launches an environment for the specified application using the specified configuration.
	 *
	 * @param string $application_name (Required) The name of the application that contains the version to be deployed. If no application is found with this name, <code>CreateEnvironment</code> returns an <code>InvalidParameterValue</code> error.
	 * @param string $environment_name (Required) A unique name for the deployment environment. Used in the application URL. Constraint: Must be from 4 to 23 characters in length. The name can contain only letters, numbers, and hyphens. It cannot start or end with a hyphen. This name must be unique in your account. If the specified name already exists, AWS Elastic Beanstalk returns an <code>InvalidParameterValue</code> error. Default: If the CNAME parameter is not specified, the environment name becomes part of the CNAME, and therefore part of the visible URL for your application.
	 * @param array $opt (Optional) An associative array of parameters that can have the following keys: <ul>
	 * 	<li><code>VersionLabel</code> - <code>string</code> - Optional - The name of the application version to deploy. If the specified application has no associated application versions, AWS Elastic Beanstalk <code>UpdateEnvironment</code> returns an <code>InvalidParameterValue</code> error. Default: If not specified, AWS Elastic Beanstalk attempts to launch the most recently created application version.</li>
	 * 	<li><code>TemplateName</code> - <code>string</code> - Optional - The name of the configuration template to use in deployment. If no configuration template is found with this name, AWS Elastic Beanstalk returns an <code>InvalidParameterValue</code> error. Condition: You must specify either this parameter or a <code>SolutionStackName</code>, but not both. If you specify both, AWS Elastic Beanstalk returns an <code>InvalidParameterCombination</code> error. If you do not specify either, AWS Elastic Beanstalk returns a <code>MissingRequiredParameter</code> error.</li>
	 * 	<li><code>SolutionStackName</code> - <code>string</code> - Optional - This is an alternative to specifying a configuration name. If specified, AWS Elastic Beanstalk sets the configuration values to the default values associated with the specified solution stack. Condition: You must specify either this or a <code>TemplateName</code>, but not both. If you specify both, AWS Elastic Beanstalk returns an <code>InvalidParameterCombination</code> error. If you do not specify either, AWS Elastic Beanstalk returns a <code>MissingRequiredParameter</code> error.</li>
	 * 	<li><code>CNAMEPrefix</code> - <code>string</code> - Optional - If specified, the environment attempts to use this value as the prefix for the CNAME. If not specified, the environment uses the environment name.</li>
	 * 	<li><code>Description</code> - <code>string</code> - Optional - Describes this environment.</li>
	 * 	<li><code>OptionSettings</code> - <code>array</code> - Optional - If specified, AWS Elastic Beanstalk sets the specified configuration options to the requested value in the configuration set for the new environment. These override the values obtained from the solution stack or the configuration template. <ul>
	 * 		<li><code>x</code> - <code>array</code> - Optional - This represents a simple array index. <ul>
	 * 			<li><code>Namespace</code> - <code>string</code> - Optional - A unique namespace identifying the option's associated AWS resource.</li>
	 * 			<li><code>OptionName</code> - <code>string</code> - Optional - The name of the configuration option.</li>
	 * 			<li><code>Value</code> - <code>string</code> - Optional - The current value for the configuration option.</li>
	 * 		</ul></li>
	 * 	</ul></li>
	 * 	<li><code>OptionsToRemove</code> - <code>array</code> - Optional - A list of custom user-defined configuration options to remove from the configuration set for this new environment. <ul>
	 * 		<li><code>x</code> - <code>array</code> - Optional - This represents a simple array index. <ul>
	 * 			<li><code>Namespace</code> - <code>string</code> - Optional - A unique namespace identifying the option's associated AWS resource.</li>
	 * 			<li><code>OptionName</code> - <code>string</code> - Optional - The name of the configuration option.</li>
	 * 		</ul></li>
	 * 	</ul></li>
	 * 	<li><code>curlopts</code> - <code>array</code> - Optional - A set of values to pass directly into <code>curl_setopt()</code>, where the key is a pre-defined <code>CURLOPT_*</code> constant.</li>
	 * 	<li><code>returnCurlHandle</code> - <code>boolean</code> - Optional - A private toggle specifying that the cURL handle be returned rather than actually completing the request. This toggle is useful for manually managed batch requests.</li></ul>
	 * @return CFResponse A <CFResponse> object containing a parsed HTTP response.
	 */
	public function create_environment($application_name, $environment_name, $opt = null)
	{
		if (!$opt) $opt = array();
		$opt['ApplicationName'] = $application_name;
		$opt['EnvironmentName'] = $environment_name;
		
		// Optional list + map
		if (isset($opt['OptionSettings']))
		{
			$opt = array_merge($opt, CFComplexType::map(array(
				'OptionSettings' => $opt['OptionSettings']
			), 'member'));
			unset($opt['OptionSettings']);
		}
		
		// Optional list + map
		if (isset($opt['OptionsToRemove']))
		{
			$opt = array_merge($opt, CFComplexType::map(array(
				'OptionsToRemove' => $opt['OptionsToRemove']
			), 'member'));
			unset($opt['OptionsToRemove']);
		}

		return $this->authenticate('CreateEnvironment', $opt);
	}

	/**
	 * Creates the Amazon S3 storage location for the account.
	 *  
	 * This location is used to store user log files.
	 *
	 * @param array $opt (Optional) An associative array of parameters that can have the following keys: <ul>
	 * 	<li><code>curlopts</code> - <code>array</code> - Optional - A set of values to pass directly into <code>curl_setopt()</code>, where the key is a pre-defined <code>CURLOPT_*</code> constant.</li>
	 * 	<li><code>returnCurlHandle</code> - <code>boolean</code> - Optional - A private toggle specifying that the cURL handle be returned rather than actually completing the request. This toggle is useful for manually managed batch requests.</li></ul>
	 * @return CFResponse A <CFResponse> object containing a parsed HTTP response.
	 */
	public function create_storage_location($opt = null)
	{
		if (!$opt) $opt = array();
				
		return $this->authenticate('CreateStorageLocation', $opt);
	}

	/**
	 * Deletes the specified application along with all associated versions and configurations.
	 * 
	 * <p class="note">
	 * You cannot delete an application that has a running environment.
	 * </p>
	 *
	 * @param string $application_name (Required) The name of the application to delete.
	 * @param array $opt (Optional) An associative array of parameters that can have the following keys: <ul>
	 * 	<li><code>curlopts</code> - <code>array</code> - Optional - A set of values to pass directly into <code>curl_setopt()</code>, where the key is a pre-defined <code>CURLOPT_*</code> constant.</li>
	 * 	<li><code>returnCurlHandle</code> - <code>boolean</code> - Optional - A private toggle specifying that the cURL handle be returned rather than actually completing the request. This toggle is useful for manually managed batch requests.</li></ul>
	 * @return CFResponse A <CFResponse> object containing a parsed HTTP response.
	 */
	public function delete_application($application_name, $opt = null)
	{
		if (!$opt) $opt = array();
		$opt['ApplicationName'] = $application_name;
		
		return $this->authenticate('DeleteApplication', $opt);
	}

	/**
	 * Deletes the specified version from the specified application.
	 * 
	 * <p class="note">
	 * You cannot delete an application version that is associated with a running environment.
	 * </p>
	 *
	 * @param string $application_name (Required) The name of the application to delete releases from.
	 * @param string $version_label (Required) The label of the version to delete.
	 * @param array $opt (Optional) An associative array of parameters that can have the following keys: <ul>
	 * 	<li><code>DeleteSourceBundle</code> - <code>boolean</code> - Optional - Indicates whether to delete the associated source bundle from Amazon S3:<ul><li> <code>true</code>: An attempt is made to delete the associated Amazon S3 source bundle specified at time of creation.</li><li> <code>false</code>: No action is taken on the Amazon S3 source bundle specified at time of creation.</li></ul>Valid Values: <code>true</code> | <code>false</code></li>
	 * 	<li><code>curlopts</code> - <code>array</code> - Optional - A set of values to pass directly into <code>curl_setopt()</code>, where the key is a pre-defined <code>CURLOPT_*</code> constant.</li>
	 * 	<li><code>returnCurlHandle</code> - <code>boolean</code> - Optional - A private toggle specifying that the cURL handle be returned rather than actually completing the request. This toggle is useful for manually managed batch requests.</li></ul>
	 * @return CFResponse A <CFResponse> object containing a parsed HTTP response.
	 */
	public function delete_application_version($application_name, $version_label, $opt = null)
	{
		if (!$opt) $opt = array();
		$opt['ApplicationName'] = $application_name;
		$opt['VersionLabel'] = $version_label;
		
		return $this->authenticate('DeleteApplicationVersion', $opt);
	}

	/**
	 * Deletes the specified configuration template.
	 * 
	 * <p class="note">
	 * When you launch an environment using a configuration template, the environment gets a copy of
	 * the template. You can delete or modify the environment's copy of the template without affecting
	 * the running environment.
	 * </p>
	 *
	 * @param string $application_name (Required) The name of the application to delete the configuration template from.
	 * @param string $template_name (Required) The name of the configuration template to delete.
	 * @param array $opt (Optional) An associative array of parameters that can have the following keys: <ul>
	 * 	<li><code>curlopts</code> - <code>array</code> - Optional - A set of values to pass directly into <code>curl_setopt()</code>, where the key is a pre-defined <code>CURLOPT_*</code> constant.</li>
	 * 	<li><code>returnCurlHandle</code> - <code>boolean</code> - Optional - A private toggle specifying that the cURL handle be returned rather than actually completing the request. This toggle is useful for manually managed batch requests.</li></ul>
	 * @return CFResponse A <CFResponse> object containing a parsed HTTP response.
	 */
	public function delete_configuration_template($application_name, $template_name, $opt = null)
	{
		if (!$opt) $opt = array();
		$opt['ApplicationName'] = $application_name;
		$opt['TemplateName'] = $template_name;
		
		return $this->authenticate('DeleteConfigurationTemplate', $opt);
	}

	/**
	 * Deletes the draft configuration associated with the running environment.
	 *  
	 * Updating a running environment with any configuration changes creates a draft configuration
	 * set. You can get the draft configuration using <code>DescribeConfigurationSettings</code> while
	 * the update is in progress or if the update fails. The <code>DeploymentStatus</code> for the
	 * draft configuration indicates whether the deployment is in process or has failed. The draft
	 * configuration remains in existence until it is deleted with this action.
	 *
	 * @param string $application_name (Required) The name of the application the environment is associated with.
	 * @param string $environment_name (Required) The name of the environment to delete the draft configuration from.
	 * @param array $opt (Optional) An associative array of parameters that can have the following keys: <ul>
	 * 	<li><code>curlopts</code> - <code>array</code> - Optional - A set of values to pass directly into <code>curl_setopt()</code>, where the key is a pre-defined <code>CURLOPT_*</code> constant.</li>
	 * 	<li><code>returnCurlHandle</code> - <code>boolean</code> - Optional - A private toggle specifying that the cURL handle be returned rather than actually completing the request. This toggle is useful for manually managed batch requests.</li></ul>
	 * @return CFResponse A <CFResponse> object containing a parsed HTTP response.
	 */
	public function delete_environment_configuration($application_name, $environment_name, $opt = null)
	{
		if (!$opt) $opt = array();
		$opt['ApplicationName'] = $application_name;
		$opt['EnvironmentName'] = $environment_name;
		
		return $this->authenticate('DeleteEnvironmentConfiguration', $opt);
	}

	/**
	 * Returns descriptions for existing application versions.
	 *
	 * @param array $opt (Optional) An associative array of parameters that can have the following keys: <ul>
	 * 	<li><code>ApplicationName</code> - <code>string</code> - Optional - If specified, AWS Elastic Beanstalk restricts the returned descriptions to only include ones that are associated with the specified application.</li>
	 * 	<li><code>VersionLabels</code> - <code>string|array</code> - Optional - If specified, restricts the returned descriptions to only include ones that have the specified version labels. Pass a string for a single value, or an indexed array for multiple values.</li>
	 * 	<li><code>curlopts</code> - <code>array</code> - Optional - A set of values to pass directly into <code>curl_setopt()</code>, where the key is a pre-defined <code>CURLOPT_*</code> constant.</li>
	 * 	<li><code>returnCurlHandle</code> - <code>boolean</code> - Optional - A private toggle specifying that the cURL handle be returned rather than actually completing the request. This toggle is useful for manually managed batch requests.</li></ul>
	 * @return CFResponse A <CFResponse> object containing a parsed HTTP response.
	 */
	public function describe_application_versions($opt = null)
	{
		if (!$opt) $opt = array();
				
		// Optional list (non-map)
		if (isset($opt['VersionLabels']))
		{
			$opt = array_merge($opt, CFComplexType::map(array(
				'VersionLabels' => (is_array($opt['VersionLabels']) ? $opt['VersionLabels'] : array($opt['VersionLabels']))
			), 'member'));
			unset($opt['VersionLabels']);
		}

		return $this->authenticate('DescribeApplicationVersions', $opt);
	}

	/**
	 * Returns the descriptions of existing applications.
	 *
	 * @param array $opt (Optional) An associative array of parameters that can have the following keys: <ul>
	 * 	<li><code>ApplicationNames</code> - <code>string|array</code> - Optional - If specified, AWS Elastic Beanstalk restricts the returned descriptions to only include those with the specified names. Pass a string for a single value, or an indexed array for multiple values.</li>
	 * 	<li><code>curlopts</code> - <code>array</code> - Optional - A set of values to pass directly into <code>curl_setopt()</code>, where the key is a pre-defined <code>CURLOPT_*</code> constant.</li>
	 * 	<li><code>returnCurlHandle</code> - <code>boolean</code> - Optional - A private toggle specifying that the cURL handle be returned rather than actually completing the request. This toggle is useful for manually managed batch requests.</li></ul>
	 * @return CFResponse A <CFResponse> object containing a parsed HTTP response.
	 */
	public function describe_applications($opt = null)
	{
		if (!$opt) $opt = array();
				
		// Optional list (non-map)
		if (isset($opt['ApplicationNames']))
		{
			$opt = array_merge($opt, CFComplexType::map(array(
				'ApplicationNames' => (is_array($opt['ApplicationNames']) ? $opt['ApplicationNames'] : array($opt['ApplicationNames']))
			), 'member'));
			unset($opt['ApplicationNames']);
		}

		return $this->authenticate('DescribeApplications', $opt);
	}

	/**
	 * Describes the configuration options that are used in a particular configuration template or
	 * environment, or that a specified solution stack defines. The description includes the values
	 * the options, their default values, and an indication of the required action on a running
	 * environment if an option value is changed.
	 *
	 * @param array $opt (Optional) An associative array of parameters that can have the following keys: <ul>
	 * 	<li><code>ApplicationName</code> - <code>string</code> - Optional - The name of the application associated with the configuration template or environment. Only needed if you want to describe the configuration options associated with either the configuration template or environment.</li>
	 * 	<li><code>TemplateName</code> - <code>string</code> - Optional - The name of the configuration template whose configuration options you want to describe.</li>
	 * 	<li><code>EnvironmentName</code> - <code>string</code> - Optional - The name of the environment whose configuration options you want to describe.</li>
	 * 	<li><code>SolutionStackName</code> - <code>string</code> - Optional - The name of the solution stack whose configuration options you want to describe.</li>
	 * 	<li><code>Options</code> - <code>array</code> - Optional - If specified, restricts the descriptions to only the specified options. <ul>
	 * 		<li><code>x</code> - <code>array</code> - Optional - This represents a simple array index. <ul>
	 * 			<li><code>Namespace</code> - <code>string</code> - Optional - A unique namespace identifying the option's associated AWS resource.</li>
	 * 			<li><code>OptionName</code> - <code>string</code> - Optional - The name of the configuration option.</li>
	 * 		</ul></li>
	 * 	</ul></li>
	 * 	<li><code>curlopts</code> - <code>array</code> - Optional - A set of values to pass directly into <code>curl_setopt()</code>, where the key is a pre-defined <code>CURLOPT_*</code> constant.</li>
	 * 	<li><code>returnCurlHandle</code> - <code>boolean</code> - Optional - A private toggle specifying that the cURL handle be returned rather than actually completing the request. This toggle is useful for manually managed batch requests.</li></ul>
	 * @return CFResponse A <CFResponse> object containing a parsed HTTP response.
	 */
	public function describe_configuration_options($opt = null)
	{
		if (!$opt) $opt = array();
				
		// Optional list + map
		if (isset($opt['Options']))
		{
			$opt = array_merge($opt, CFComplexType::map(array(
				'Options' => $opt['Options']
			), 'member'));
			unset($opt['Options']);
		}

		return $this->authenticate('DescribeConfigurationOptions', $opt);
	}

	/**
	 * Returns a description of the settings for the specified configuration set, that is, either a
	 * configuration template or the configuration set associated with a running environment.
	 *  
	 * When describing the settings for the configuration set associated with a running environment,
	 * it is possible to receive two sets of setting descriptions. One is the deployed configuration
	 * set, and the other is a draft configuration of an environment that is either in the process of
	 * deployment or that failed to deploy.
	 *  
	 * Related Topics
	 * 
	 * <ul>
	 * 	<li><code>DeleteEnvironmentConfiguration</code></li>
	 * </ul>
	 *
	 * @param string $application_name (Required) The application for the environment or configuration template.
	 * @param array $opt (Optional) An associative array of parameters that can have the following keys: <ul>
	 * 	<li><code>TemplateName</code> - <code>string</code> - Optional - The name of the configuration template to describe. Conditional: You must specify either this parameter or an EnvironmentName, but not both. If you specify both, AWS Elastic Beanstalk returns an <code>InvalidParameterCombination</code> error. If you do not specify either, AWS Elastic Beanstalk returns a <code>MissingRequiredParameter</code> error.</li>
	 * 	<li><code>EnvironmentName</code> - <code>string</code> - Optional - The name of the environment to describe. Condition: You must specify either this or a TemplateName, but not both. If you specify both, AWS Elastic Beanstalk returns an <code>InvalidParameterCombination</code> error. If you do not specify either, AWS Elastic Beanstalk returns <code>MissingRequiredParameter</code> error.</li>
	 * 	<li><code>curlopts</code> - <code>array</code> - Optional - A set of values to pass directly into <code>curl_setopt()</code>, where the key is a pre-defined <code>CURLOPT_*</code> constant.</li>
	 * 	<li><code>returnCurlHandle</code> - <code>boolean</code> - Optional - A private toggle specifying that the cURL handle be returned rather than actually completing the request. This toggle is useful for manually managed batch requests.</li></ul>
	 * @return CFResponse A <CFResponse> object containing a parsed HTTP response.
	 */
	public function describe_configuration_settings($application_name, $opt = null)
	{
		if (!$opt) $opt = array();
		$opt['ApplicationName'] = $application_name;
		
		return $this->authenticate('DescribeConfigurationSettings', $opt);
	}

	/**
	 * Returns AWS resources for this environment.
	 *
	 * @param array $opt (Optional) An associative array of parameters that can have the following keys: <ul>
	 * 	<li><code>EnvironmentId</code> - <code>string</code> - Optional - The ID of the environment to retrieve AWS resource usage data. Condition: You must specify either this or an EnvironmentName, or both. If you do not specify either, AWS Elastic Beanstalk returns <code>MissingRequiredParameter</code> error.</li>
	 * 	<li><code>EnvironmentName</code> - <code>string</code> - Optional - The name of the environment to retrieve AWS resource usage data. Condition: You must specify either this or an EnvironmentId, or both. If you do not specify either, AWS Elastic Beanstalk returns <code>MissingRequiredParameter</code> error.</li>
	 * 	<li><code>curlopts</code> - <code>array</code> - Optional - A set of values to pass directly into <code>curl_setopt()</code>, where the key is a pre-defined <code>CURLOPT_*</code> constant.</li>
	 * 	<li><code>returnCurlHandle</code> - <code>boolean</code> - Optional - A private toggle specifying that the cURL handle be returned rather than actually completing the request. This toggle is useful for manually managed batch requests.</li></ul>
	 * @return CFResponse A <CFResponse> object containing a parsed HTTP response.
	 */
	public function describe_environment_resources($opt = null)
	{
		if (!$opt) $opt = array();
				
		return $this->authenticate('DescribeEnvironmentResources', $opt);
	}

	/**
	 * Returns descriptions for existing environments.
	 *
	 * @param array $opt (Optional) An associative array of parameters that can have the following keys: <ul>
	 * 	<li><code>ApplicationName</code> - <code>string</code> - Optional - If specified, AWS Elastic Beanstalk restricts the returned descriptions to include only those that are associated with this application.</li>
	 * 	<li><code>VersionLabel</code> - <code>string</code> - Optional - If specified, AWS Elastic Beanstalk restricts the returned descriptions to include only those that are associated with this application version.</li>
	 * 	<li><code>EnvironmentIds</code> - <code>string|array</code> - Optional - If specified, AWS Elastic Beanstalk restricts the returned descriptions to include only those that have the specified IDs. Pass a string for a single value, or an indexed array for multiple values.</li>
	 * 	<li><code>EnvironmentNames</code> - <code>string|array</code> - Optional - If specified, AWS Elastic Beanstalk restricts the returned descriptions to include only those that have the specified names. Pass a string for a single value, or an indexed array for multiple values.</li>
	 * 	<li><code>IncludeDeleted</code> - <code>boolean</code> - Optional - Indicates whether to include deleted environments: <code>true</code>: Environments that have been deleted after <code>IncludedDeletedBackTo</code> are displayed. <code>false</code>: Do not include deleted environments.</li>
	 * 	<li><code>IncludedDeletedBackTo</code> - <code>string</code> - Optional - If specified when <code>IncludeDeleted</code> is set to <code>true</code>, then environments deleted after this date are displayed. May be passed as a number of seconds since UNIX Epoch, or any string compatible with <php:strtotime()>.</li>
	 * 	<li><code>curlopts</code> - <code>array</code> - Optional - A set of values to pass directly into <code>curl_setopt()</code>, where the key is a pre-defined <code>CURLOPT_*</code> constant.</li>
	 * 	<li><code>returnCurlHandle</code> - <code>boolean</code> - Optional - A private toggle specifying that the cURL handle be returned rather than actually completing the request. This toggle is useful for manually managed batch requests.</li></ul>
	 * @return CFResponse A <CFResponse> object containing a parsed HTTP response.
	 */
	public function describe_environments($opt = null)
	{
		if (!$opt) $opt = array();
				
		// Optional DateTime
		if (isset($opt['IncludedDeletedBackTo']))
		{
			$opt['IncludedDeletedBackTo'] = $this->util->convert_date_to_iso8601($opt['IncludedDeletedBackTo']);
		}

		// Optional list (non-map)
		if (isset($opt['EnvironmentIds']))
		{
			$opt = array_merge($opt, CFComplexType::map(array(
				'EnvironmentIds' => (is_array($opt['EnvironmentIds']) ? $opt['EnvironmentIds'] : array($opt['EnvironmentIds']))
			), 'member'));
			unset($opt['EnvironmentIds']);
		}
		
		// Optional list (non-map)
		if (isset($opt['EnvironmentNames']))
		{
			$opt = array_merge($opt, CFComplexType::map(array(
				'EnvironmentNames' => (is_array($opt['EnvironmentNames']) ? $opt['EnvironmentNames'] : array($opt['EnvironmentNames']))
			), 'member'));
			unset($opt['EnvironmentNames']);
		}

		return $this->authenticate('DescribeEnvironments', $opt);
	}

	/**
	 * Returns list of event descriptions matching criteria up to the last 6 weeks.
	 * 
	 * <p class="note">
	 * This action returns the most recent 1,000 events from the specified <code>NextToken</code>.
	 * </p>
	 *
	 * @param array $opt (Optional) An associative array of parameters that can have the following keys: <ul>
	 * 	<li><code>ApplicationName</code> - <code>string</code> - Optional - If specified, AWS Elastic Beanstalk restricts the returned descriptions to include only those associated with this application.</li>
	 * 	<li><code>VersionLabel</code> - <code>string</code> - Optional - If specified, AWS Elastic Beanstalk restricts the returned descriptions to those associated with this application version.</li>
	 * 	<li><code>TemplateName</code> - <code>string</code> - Optional - If specified, AWS Elastic Beanstalk restricts the returned descriptions to those that are associated with this environment configuration.</li>
	 * 	<li><code>EnvironmentId</code> - <code>string</code> - Optional - If specified, AWS Elastic Beanstalk restricts the returned descriptions to those associated with this environment.</li>
	 * 	<li><code>EnvironmentName</code> - <code>string</code> - Optional - If specified, AWS Elastic Beanstalk restricts the returned descriptions to those associated with this environment.</li>
	 * 	<li><code>RequestId</code> - <code>string</code> - Optional - If specified, AWS Elastic Beanstalk restricts the described events to include only those associated with this request ID.</li>
	 * 	<li><code>Severity</code> - <code>string</code> - Optional - If specified, limits the events returned from this call to include only those with the specified severity or higher. [Allowed values: <code>TRACE</code>, <code>DEBUG</code>, <code>INFO</code>, <code>WARN</code>, <code>ERROR</code>, <code>FATAL</code>]</li>
	 * 	<li><code>StartTime</code> - <code>string</code> - Optional - If specified, AWS Elastic Beanstalk restricts the returned descriptions to those that occur on or after this time. May be passed as a number of seconds since UNIX Epoch, or any string compatible with <php:strtotime()>.</li>
	 * 	<li><code>EndTime</code> - <code>string</code> - Optional - If specified, AWS Elastic Beanstalk restricts the returned descriptions to those that occur up to, but not including, the <code>EndTime</code>. May be passed as a number of seconds since UNIX Epoch, or any string compatible with <php:strtotime()>.</li>
	 * 	<li><code>MaxRecords</code> - <code>integer</code> - Optional - Specifies the maximum number of events that can be returned, beginning with the most recent event.</li>
	 * 	<li><code>NextToken</code> - <code>string</code> - Optional - Pagination token. If specified, the events return the next batch of results.</li>
	 * 	<li><code>curlopts</code> - <code>array</code> - Optional - A set of values to pass directly into <code>curl_setopt()</code>, where the key is a pre-defined <code>CURLOPT_*</code> constant.</li>
	 * 	<li><code>returnCurlHandle</code> - <code>boolean</code> - Optional - A private toggle specifying that the cURL handle be returned rather than actually completing the request. This toggle is useful for manually managed batch requests.</li></ul>
	 * @return CFResponse A <CFResponse> object containing a parsed HTTP response.
	 */
	public function describe_events($opt = null)
	{
		if (!$opt) $opt = array();
				
		// Optional DateTime
		if (isset($opt['StartTime']))
		{
			$opt['StartTime'] = $this->util->convert_date_to_iso8601($opt['StartTime']);
		}
		
		// Optional DateTime
		if (isset($opt['EndTime']))
		{
			$opt['EndTime'] = $this->util->convert_date_to_iso8601($opt['EndTime']);
		}

		return $this->authenticate('DescribeEvents', $opt);
	}

	/**
	 * Returns a list of the available solution stack names.
	 *
	 * @param array $opt (Optional) An associative array of parameters that can have the following keys: <ul>
	 * 	<li><code>curlopts</code> - <code>array</code> - Optional - A set of values to pass directly into <code>curl_setopt()</code>, where the key is a pre-defined <code>CURLOPT_*</code> constant.</li>
	 * 	<li><code>returnCurlHandle</code> - <code>boolean</code> - Optional - A private toggle specifying that the cURL handle be returned rather than actually completing the request. This toggle is useful for manually managed batch requests.</li></ul>
	 * @return CFResponse A <CFResponse> object containing a parsed HTTP response.
	 */
	public function list_available_solution_stacks($opt = null)
	{
		if (!$opt) $opt = array();
				
		return $this->authenticate('ListAvailableSolutionStacks', $opt);
	}

	/**
	 * Deletes and recreates all of the AWS resources (for example: the Auto Scaling group, load
	 * balancer, etc.) for a specified environment and forces a restart.
	 *
	 * @param array $opt (Optional) An associative array of parameters that can have the following keys: <ul>
	 * 	<li><code>EnvironmentId</code> - <code>string</code> - Optional - The ID of the environment to rebuild. Condition: You must specify either this or an EnvironmentName, or both. If you do not specify either, AWS Elastic Beanstalk returns <code>MissingRequiredParameter</code> error.</li>
	 * 	<li><code>EnvironmentName</code> - <code>string</code> - Optional - The name of the environment to rebuild. Condition: You must specify either this or an EnvironmentId, or both. If you do not specify either, AWS Elastic Beanstalk returns <code>MissingRequiredParameter</code> error.</li>
	 * 	<li><code>curlopts</code> - <code>array</code> - Optional - A set of values to pass directly into <code>curl_setopt()</code>, where the key is a pre-defined <code>CURLOPT_*</code> constant.</li>
	 * 	<li><code>returnCurlHandle</code> - <code>boolean</code> - Optional - A private toggle specifying that the cURL handle be returned rather than actually completing the request. This toggle is useful for manually managed batch requests.</li></ul>
	 * @return CFResponse A <CFResponse> object containing a parsed HTTP response.
	 */
	public function rebuild_environment($opt = null)
	{
		if (!$opt) $opt = array();
				
		return $this->authenticate('RebuildEnvironment', $opt);
	}

	/**
	 * Initiates a request to compile the specified type of information of the deployed environment.
	 *  
	 * Setting the <code>InfoType</code> to <code>tail</code> compiles the last lines from the
	 * application server log files of every Amazon EC2 instance in your environment. Use
	 * <code>RetrieveEnvironmentInfo</code> to access the compiled information.
	 *  
	 * Related Topics
	 * 
	 * <ul>
	 * 	<li><code>RetrieveEnvironmentInfo</code></li>
	 * </ul>
	 *
	 * @param string $info_type (Required) The type of information to request. [Allowed values: <code>tail</code>]
	 * @param array $opt (Optional) An associative array of parameters that can have the following keys: <ul>
	 * 	<li><code>EnvironmentId</code> - <code>string</code> - Optional - The ID of the environment of the requested data. If no such environment is found, <code>RequestEnvironmentInfo</code> returns an <code>InvalidParameterValue</code> error. Condition: You must specify either this or an EnvironmentName, or both. If you do not specify either, AWS Elastic Beanstalk returns <code>MissingRequiredParameter</code> error.</li>
	 * 	<li><code>EnvironmentName</code> - <code>string</code> - Optional - The name of the environment of the requested data. If no such environment is found, <code>RequestEnvironmentInfo</code> returns an <code>InvalidParameterValue</code> error. Condition: You must specify either this or an EnvironmentId, or both. If you do not specify either, AWS Elastic Beanstalk returns <code>MissingRequiredParameter</code> error.</li>
	 * 	<li><code>curlopts</code> - <code>array</code> - Optional - A set of values to pass directly into <code>curl_setopt()</code>, where the key is a pre-defined <code>CURLOPT_*</code> constant.</li>
	 * 	<li><code>returnCurlHandle</code> - <code>boolean</code> - Optional - A private toggle specifying that the cURL handle be returned rather than actually completing the request. This toggle is useful for manually managed batch requests.</li></ul>
	 * @return CFResponse A <CFResponse> object containing a parsed HTTP response.
	 */
	public function request_environment_info($info_type, $opt = null)
	{
		if (!$opt) $opt = array();
		$opt['InfoType'] = $info_type;
		
		return $this->authenticate('RequestEnvironmentInfo', $opt);
	}

	/**
	 * Causes the environment to restart the application container server running on each Amazon EC2
	 * instance.
	 *
	 * @param array $opt (Optional) An associative array of parameters that can have the following keys: <ul>
	 * 	<li><code>EnvironmentId</code> - <code>string</code> - Optional - The ID of the environment to restart the server for. Condition: You must specify either this or an EnvironmentName, or both. If you do not specify either, AWS Elastic Beanstalk returns <code>MissingRequiredParameter</code> error.</li>
	 * 	<li><code>EnvironmentName</code> - <code>string</code> - Optional - The name of the environment to restart the server for. Condition: You must specify either this or an EnvironmentId, or both. If you do not specify either, AWS Elastic Beanstalk returns <code>MissingRequiredParameter</code> error.</li>
	 * 	<li><code>curlopts</code> - <code>array</code> - Optional - A set of values to pass directly into <code>curl_setopt()</code>, where the key is a pre-defined <code>CURLOPT_*</code> constant.</li>
	 * 	<li><code>returnCurlHandle</code> - <code>boolean</code> - Optional - A private toggle specifying that the cURL handle be returned rather than actually completing the request. This toggle is useful for manually managed batch requests.</li></ul>
	 * @return CFResponse A <CFResponse> object containing a parsed HTTP response.
	 */
	public function restart_app_server($opt = null)
	{
		if (!$opt) $opt = array();
				
		return $this->authenticate('RestartAppServer', $opt);
	}

	/**
	 * Retrieves the compiled information from a <code>RequestEnvironmentInfo</code> request.
	 *  
	 * Related Topics
	 * 
	 * <ul>
	 * 	<li><code>RequestEnvironmentInfo</code></li>
	 * </ul>
	 *
	 * @param string $info_type (Required) The type of information to retrieve. [Allowed values: <code>tail</code>]
	 * @param array $opt (Optional) An associative array of parameters that can have the following keys: <ul>
	 * 	<li><code>EnvironmentId</code> - <code>string</code> - Optional - The ID of the data's environment. If no such environment is found, returns an <code>InvalidParameterValue</code> error. Condition: You must specify either this or an EnvironmentName, or both. If you do not specify either, AWS Elastic Beanstalk returns <code>MissingRequiredParameter</code> error.</li>
	 * 	<li><code>EnvironmentName</code> - <code>string</code> - Optional - The name of the data's environment. If no such environment is found, returns an <code>InvalidParameterValue</code> error. Condition: You must specify either this or an EnvironmentId, or both. If you do not specify either, AWS Elastic Beanstalk returns <code>MissingRequiredParameter</code> error.</li>
	 * 	<li><code>curlopts</code> - <code>array</code> - Optional - A set of values to pass directly into <code>curl_setopt()</code>, where the key is a pre-defined <code>CURLOPT_*</code> constant.</li>
	 * 	<li><code>returnCurlHandle</code> - <code>boolean</code> - Optional - A private toggle specifying that the cURL handle be returned rather than actually completing the request. This toggle is useful for manually managed batch requests.</li></ul>
	 * @return CFResponse A <CFResponse> object containing a parsed HTTP response.
	 */
	public function retrieve_environment_info($info_type, $opt = null)
	{
		if (!$opt) $opt = array();
		$opt['InfoType'] = $info_type;
		
		return $this->authenticate('RetrieveEnvironmentInfo', $opt);
	}

	/**
	 * Swaps the CNAMEs of two environments.
	 *
	 * @param array $opt (Optional) An associative array of parameters that can have the following keys: <ul>
	 * 	<li><code>SourceEnvironmentId</code> - <code>string</code> - Optional - The ID of the source environment. Condition: You must specify at least the <code>SourceEnvironmentID</code> or the <code>SourceEnvironmentName</code>. You may also specify both. If you specify the <code>SourceEnvironmentId</code>, you must specify the <code>DestinationEnvironmentId</code>.</li>
	 * 	<li><code>SourceEnvironmentName</code> - <code>string</code> - Optional - The name of the source environment. Condition: You must specify at least the <code>SourceEnvironmentID</code> or the <code>SourceEnvironmentName</code>. You may also specify both. If you specify the <code>SourceEnvironmentName</code>, you must specify the <code>DestinationEnvironmentName</code>.</li>
	 * 	<li><code>DestinationEnvironmentId</code> - <code>string</code> - Optional - The ID of the destination environment. Condition: You must specify at least the <code>DestinationEnvironmentID</code> or the <code>DestinationEnvironmentName</code>. You may also specify both. You must specify the <code>SourceEnvironmentId</code> with the <code>DestinationEnvironmentId</code>.</li>
	 * 	<li><code>DestinationEnvironmentName</code> - <code>string</code> - Optional - The name of the destination environment. Condition: You must specify at least the <code>DestinationEnvironmentID</code> or the <code>DestinationEnvironmentName</code>. You may also specify both. You must specify the <code>SourceEnvironmentName</code> with the <code>DestinationEnvironmentName</code>.</li>
	 * 	<li><code>curlopts</code> - <code>array</code> - Optional - A set of values to pass directly into <code>curl_setopt()</code>, where the key is a pre-defined <code>CURLOPT_*</code> constant.</li>
	 * 	<li><code>returnCurlHandle</code> - <code>boolean</code> - Optional - A private toggle specifying that the cURL handle be returned rather than actually completing the request. This toggle is useful for manually managed batch requests.</li></ul>
	 * @return CFResponse A <CFResponse> object containing a parsed HTTP response.
	 */
	public function swap_environment_cnames($opt = null)
	{
		if (!$opt) $opt = array();
				
		return $this->authenticate('SwapEnvironmentCNAMEs', $opt);
	}

	/**
	 * Terminates the specified environment.
	 *
	 * @param array $opt (Optional) An associative array of parameters that can have the following keys: <ul>
	 * 	<li><code>EnvironmentId</code> - <code>string</code> - Optional - The ID of the environment to terminate. Condition: You must specify either this or an EnvironmentName, or both. If you do not specify either, AWS Elastic Beanstalk returns <code>MissingRequiredParameter</code> error.</li>
	 * 	<li><code>EnvironmentName</code> - <code>string</code> - Optional - The name of the environment to terminate. Condition: You must specify either this or an EnvironmentId, or both. If you do not specify either, AWS Elastic Beanstalk returns <code>MissingRequiredParameter</code> error.</li>
	 * 	<li><code>TerminateResources</code> - <code>boolean</code> - Optional - Indicates whether the associated AWS resources should shut down when the environment is terminated:<enumValues><value name="true"> <code>true</code>: (default) The user AWS resources (for example, the Auto Scaling group, LoadBalancer, etc.) are terminated along with the environment.</value><value name="false"> <code>false</code>: The environment is removed from the AWS Elastic Beanstalk but the AWS resources continue to operate.</value></enumValues><ul><li> <code>true</code>: The specified environment as well as the associated AWS resources, such as Auto Scaling group and LoadBalancer, are terminated.</li><li> <code>false</code>: AWS Elastic Beanstalk resource management is removed from the environment, but the AWS resources continue to operate.</li></ul>For more information, see the <a href="http://docs.amazonwebservices.com/elasticbeanstalk/latest/ug/">AWS Elastic Beanstalk User Guide.</a> Default: <code>true</code> Valid Values: <code>true</code> | <code>false</code></li>
	 * 	<li><code>curlopts</code> - <code>array</code> - Optional - A set of values to pass directly into <code>curl_setopt()</code>, where the key is a pre-defined <code>CURLOPT_*</code> constant.</li>
	 * 	<li><code>returnCurlHandle</code> - <code>boolean</code> - Optional - A private toggle specifying that the cURL handle be returned rather than actually completing the request. This toggle is useful for manually managed batch requests.</li></ul>
	 * @return CFResponse A <CFResponse> object containing a parsed HTTP response.
	 */
	public function terminate_environment($opt = null)
	{
		if (!$opt) $opt = array();
				
		return $this->authenticate('TerminateEnvironment', $opt);
	}

	/**
	 * Updates the specified application to have the specified properties.
	 * 
	 * <p class="note">
	 * If a property (for example, <code>description</code>) is not provided, the value remains
	 * unchanged. To clear these properties, specify an empty string.
	 * </p>
	 *
	 * @param string $application_name (Required) The name of the application to update. If no such application is found, <code>UpdateApplication</code> returns an <code>InvalidParameterValue</code> error.
	 * @param array $opt (Optional) An associative array of parameters that can have the following keys: <ul>
	 * 	<li><code>Description</code> - <code>string</code> - Optional - A new description for the application. Default: If not specified, AWS Elastic Beanstalk does not update the description.</li>
	 * 	<li><code>curlopts</code> - <code>array</code> - Optional - A set of values to pass directly into <code>curl_setopt()</code>, where the key is a pre-defined <code>CURLOPT_*</code> constant.</li>
	 * 	<li><code>returnCurlHandle</code> - <code>boolean</code> - Optional - A private toggle specifying that the cURL handle be returned rather than actually completing the request. This toggle is useful for manually managed batch requests.</li></ul>
	 * @return CFResponse A <CFResponse> object containing a parsed HTTP response.
	 */
	public function update_application($application_name, $opt = null)
	{
		if (!$opt) $opt = array();
		$opt['ApplicationName'] = $application_name;
		
		return $this->authenticate('UpdateApplication', $opt);
	}

	/**
	 * Updates the specified application version to have the specified properties.
	 * 
	 * <p class="note">
	 * If a property (for example, <code>description</code>) is not provided, the value remains
	 * unchanged. To clear properties, specify an empty string.
	 * </p>
	 *
	 * @param string $application_name (Required) The name of the application associated with this version. If no application is found with this name, <code>UpdateApplication</code> returns an <code>InvalidParameterValue</code> error.
	 * @param string $version_label (Required) The name of the version to update. If no application version is found with this label, <code>UpdateApplication</code> returns an <code>InvalidParameterValue</code> error.
	 * @param array $opt (Optional) An associative array of parameters that can have the following keys: <ul>
	 * 	<li><code>Description</code> - <code>string</code> - Optional - A new description for this release.</li>
	 * 	<li><code>curlopts</code> - <code>array</code> - Optional - A set of values to pass directly into <code>curl_setopt()</code>, where the key is a pre-defined <code>CURLOPT_*</code> constant.</li>
	 * 	<li><code>returnCurlHandle</code> - <code>boolean</code> - Optional - A private toggle specifying that the cURL handle be returned rather than actually completing the request. This toggle is useful for manually managed batch requests.</li></ul>
	 * @return CFResponse A <CFResponse> object containing a parsed HTTP response.
	 */
	public function update_application_version($application_name, $version_label, $opt = null)
	{
		if (!$opt) $opt = array();
		$opt['ApplicationName'] = $application_name;
		$opt['VersionLabel'] = $version_label;
		
		return $this->authenticate('UpdateApplicationVersion', $opt);
	}

	/**
	 * Updates the specified configuration template to have the specified properties or configuration
	 * option values.
	 * 
	 * <p class="note">
	 * If a property (for example, <code>ApplicationName</code>) is not provided, its value remains
	 * unchanged. To clear such properties, specify an empty string.
	 * </p> 
	 * Related Topics
	 * 
	 * <ul>
	 * 	<li><code>DescribeConfigurationOptions</code></li>
	 * </ul>
	 *
	 * @param string $application_name (Required) The name of the application associated with the configuration template to update. If no application is found with this name, <code>UpdateConfigurationTemplate</code> returns an <code>InvalidParameterValue</code> error.
	 * @param string $template_name (Required) The name of the configuration template to update. If no configuration template is found with this name, <code>UpdateConfigurationTemplate</code> returns an <code>InvalidParameterValue</code> error.
	 * @param array $opt (Optional) An associative array of parameters that can have the following keys: <ul>
	 * 	<li><code>Description</code> - <code>string</code> - Optional - A new description for the configuration.</li>
	 * 	<li><code>OptionSettings</code> - <code>array</code> - Optional - A list of configuration option settings to update with the new specified option value. <ul>
	 * 		<li><code>x</code> - <code>array</code> - Optional - This represents a simple array index. <ul>
	 * 			<li><code>Namespace</code> - <code>string</code> - Optional - A unique namespace identifying the option's associated AWS resource.</li>
	 * 			<li><code>OptionName</code> - <code>string</code> - Optional - The name of the configuration option.</li>
	 * 			<li><code>Value</code> - <code>string</code> - Optional - The current value for the configuration option.</li>
	 * 		</ul></li>
	 * 	</ul></li>
	 * 	<li><code>OptionsToRemove</code> - <code>array</code> - Optional - A list of configuration options to remove from the configuration set. Constraint: You can remove only <code>UserDefined</code> configuration options. <ul>
	 * 		<li><code>x</code> - <code>array</code> - Optional - This represents a simple array index. <ul>
	 * 			<li><code>Namespace</code> - <code>string</code> - Optional - A unique namespace identifying the option's associated AWS resource.</li>
	 * 			<li><code>OptionName</code> - <code>string</code> - Optional - The name of the configuration option.</li>
	 * 		</ul></li>
	 * 	</ul></li>
	 * 	<li><code>curlopts</code> - <code>array</code> - Optional - A set of values to pass directly into <code>curl_setopt()</code>, where the key is a pre-defined <code>CURLOPT_*</code> constant.</li>
	 * 	<li><code>returnCurlHandle</code> - <code>boolean</code> - Optional - A private toggle specifying that the cURL handle be returned rather than actually completing the request. This toggle is useful for manually managed batch requests.</li></ul>
	 * @return CFResponse A <CFResponse> object containing a parsed HTTP response.
	 */
	public function update_configuration_template($application_name, $template_name, $opt = null)
	{
		if (!$opt) $opt = array();
		$opt['ApplicationName'] = $application_name;
		$opt['TemplateName'] = $template_name;
		
		// Optional list + map
		if (isset($opt['OptionSettings']))
		{
			$opt = array_merge($opt, CFComplexType::map(array(
				'OptionSettings' => $opt['OptionSettings']
			), 'member'));
			unset($opt['OptionSettings']);
		}
		
		// Optional list + map
		if (isset($opt['OptionsToRemove']))
		{
			$opt = array_merge($opt, CFComplexType::map(array(
				'OptionsToRemove' => $opt['OptionsToRemove']
			), 'member'));
			unset($opt['OptionsToRemove']);
		}

		return $this->authenticate('UpdateConfigurationTemplate', $opt);
	}

	/**
	 * Updates the environment description, deploys a new application version, updates the
	 * configuration settings to an entirely new configuration template, or updates select
	 * configuration option values in the running environment.
	 *  
	 * Attempting to update both the release and configuration is not allowed and AWS Elastic
	 * Beanstalk returns an <code>InvalidParameterCombination</code> error.
	 *  
	 * When updating the configuration settings to a new template or individual settings, a draft
	 * configuration is created and <code>DescribeConfigurationSettings</code> for this environment
	 * returns two setting descriptions with different <code>DeploymentStatus</code> values.
	 *
	 * @param array $opt (Optional) An associative array of parameters that can have the following keys: <ul>
	 * 	<li><code>EnvironmentId</code> - <code>string</code> - Optional - The ID of the environment to update. If no environment with this ID exists, AWS Elastic Beanstalk returns an <code>InvalidParameterValue</code> error. Condition: You must specify either this or an EnvironmentName, or both. If you do not specify either, AWS Elastic Beanstalk returns <code>MissingRequiredParameter</code> error.</li>
	 * 	<li><code>EnvironmentName</code> - <code>string</code> - Optional - The name of the environment to update. If no environment with this name exists, AWS Elastic Beanstalk returns an <code>InvalidParameterValue</code> error. Condition: You must specify either this or an EnvironmentId, or both. If you do not specify either, AWS Elastic Beanstalk returns <code>MissingRequiredParameter</code> error.</li>
	 * 	<li><code>VersionLabel</code> - <code>string</code> - Optional - If this parameter is specified, AWS Elastic Beanstalk deploys the named application version to the environment. If no such application version is found, returns an <code>InvalidParameterValue</code> error.</li>
	 * 	<li><code>TemplateName</code> - <code>string</code> - Optional - If this parameter is specified, AWS Elastic Beanstalk deploys this configuration template to the environment. If no such configuration template is found, AWS Elastic Beanstalk returns an <code>InvalidParameterValue</code> error.</li>
	 * 	<li><code>Description</code> - <code>string</code> - Optional - If this parameter is specified, AWS Elastic Beanstalk updates the description of this environment.</li>
	 * 	<li><code>OptionSettings</code> - <code>array</code> - Optional - If specified, AWS Elastic Beanstalk updates the configuration set associated with the running environment and sets the specified configuration options to the requested value. <ul>
	 * 		<li><code>x</code> - <code>array</code> - Optional - This represents a simple array index. <ul>
	 * 			<li><code>Namespace</code> - <code>string</code> - Optional - A unique namespace identifying the option's associated AWS resource.</li>
	 * 			<li><code>OptionName</code> - <code>string</code> - Optional - The name of the configuration option.</li>
	 * 			<li><code>Value</code> - <code>string</code> - Optional - The current value for the configuration option.</li>
	 * 		</ul></li>
	 * 	</ul></li>
	 * 	<li><code>OptionsToRemove</code> - <code>array</code> - Optional - A list of custom user-defined configuration options to remove from the configuration set for this environment. <ul>
	 * 		<li><code>x</code> - <code>array</code> - Optional - This represents a simple array index. <ul>
	 * 			<li><code>Namespace</code> - <code>string</code> - Optional - A unique namespace identifying the option's associated AWS resource.</li>
	 * 			<li><code>OptionName</code> - <code>string</code> - Optional - The name of the configuration option.</li>
	 * 		</ul></li>
	 * 	</ul></li>
	 * 	<li><code>curlopts</code> - <code>array</code> - Optional - A set of values to pass directly into <code>curl_setopt()</code>, where the key is a pre-defined <code>CURLOPT_*</code> constant.</li>
	 * 	<li><code>returnCurlHandle</code> - <code>boolean</code> - Optional - A private toggle specifying that the cURL handle be returned rather than actually completing the request. This toggle is useful for manually managed batch requests.</li></ul>
	 * @return CFResponse A <CFResponse> object containing a parsed HTTP response.
	 */
	public function update_environment($opt = null)
	{
		if (!$opt) $opt = array();
				
		// Optional list + map
		if (isset($opt['OptionSettings']))
		{
			$opt = array_merge($opt, CFComplexType::map(array(
				'OptionSettings' => $opt['OptionSettings']
			), 'member'));
			unset($opt['OptionSettings']);
		}
		
		// Optional list + map
		if (isset($opt['OptionsToRemove']))
		{
			$opt = array_merge($opt, CFComplexType::map(array(
				'OptionsToRemove' => $opt['OptionsToRemove']
			), 'member'));
			unset($opt['OptionsToRemove']);
		}

		return $this->authenticate('UpdateEnvironment', $opt);
	}

	/**
	 * Takes a set of configuration settings and either a configuration template or environment, and
	 * determines whether those values are valid.
	 *  
	 * This action returns a list of messages indicating any errors or warnings associated with the
	 * selection of option values.
	 *
	 * @param string $application_name (Required) The name of the application that the configuration template or environment belongs to.
	 * @param array $option_settings (Required) A list of the options and desired values to evaluate. <ul>
	 * 	<li><code>x</code> - <code>array</code> - Optional - This represents a simple array index. <ul>
	 * 		<li><code>Namespace</code> - <code>string</code> - Optional - A unique namespace identifying the option's associated AWS resource.</li>
	 * 		<li><code>OptionName</code> - <code>string</code> - Optional - The name of the configuration option.</li>
	 * 		<li><code>Value</code> - <code>string</code> - Optional - The current value for the configuration option.</li>
	 * 	</ul></li>
	 * </ul>
	 * @param array $opt (Optional) An associative array of parameters that can have the following keys: <ul>
	 * 	<li><code>TemplateName</code> - <code>string</code> - Optional - The name of the configuration template to validate the settings against. Condition: You cannot specify both this and an environment name.</li>
	 * 	<li><code>EnvironmentName</code> - <code>string</code> - Optional - The name of the environment to validate the settings against. Condition: You cannot specify both this and a configuration template name.</li>
	 * 	<li><code>curlopts</code> - <code>array</code> - Optional - A set of values to pass directly into <code>curl_setopt()</code>, where the key is a pre-defined <code>CURLOPT_*</code> constant.</li>
	 * 	<li><code>returnCurlHandle</code> - <code>boolean</code> - Optional - A private toggle specifying that the cURL handle be returned rather than actually completing the request. This toggle is useful for manually managed batch requests.</li></ul>
	 * @return CFResponse A <CFResponse> object containing a parsed HTTP response.
	 */
	public function validate_configuration_settings($application_name, $option_settings, $opt = null)
	{
		if (!$opt) $opt = array();
		$opt['ApplicationName'] = $application_name;
		
		// Required list + map
		$opt = array_merge($opt, CFComplexType::map(array(
			'OptionSettings' => (is_array($option_settings) ? $option_settings : array($option_settings))
		), 'member'));

		return $this->authenticate('ValidateConfigurationSettings', $opt);
	}
}


/*%******************************************************************************************%*/
// EXCEPTIONS

class ElasticBeanstalk_Exception extends Exception {}
