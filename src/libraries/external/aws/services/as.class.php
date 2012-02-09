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
 * This is the <em>Auto Scaling API Reference</em>. This guide provides detailed information about
 * Auto Scaling actions, data types, parameters, and errors. For detailed information about Auto
 * Scaling features and their associated API calls, go to the <a href=
 * "http://docs.amazonwebservices.com/AutoScaling/latest/DeveloperGuide/">Auto Scaling Developer
 * Guide</a>.
 *  
 * Auto Scaling is a web service designed to automatically launch or terminate EC2 instances based
 * on user-defined policies, schedules, and health checks. This service is used in conjunction
 * with Amazon CloudWatch and Elastic Load Balancing services.
 *  
 * This reference is based on the current WSDL, which is available at:
 *  
 * 	<a href=
 * "http://autoscaling.amazonaws.com/doc/2011-01-01/AutoScaling.wsdl">http://autoscaling.amazonaws.com/doc/2011-01-01/AutoScaling.wsdl</a>
 *  
 * <strong>Endpoints</strong>
 *  
 * For information about this product's regions and endpoints, go to <a href=
 * "http://docs.amazonwebservices.com/general/latest/gr/index.html?rande.html">Regions and
 * Endpoints</a> in the Amazon Web Services General Reference.
 *
 * @version 2012.01.17
 * @license See the included NOTICE.md file for complete information.
 * @copyright See the included NOTICE.md file for complete information.
 * @link http://aws.amazon.com/as/ Auto Scaling
 * @link http://aws.amazon.com/as/documentation/ Auto Scaling documentation
 */
class AmazonAS extends CFRuntime
{
	/*%******************************************************************************************%*/
	// CLASS CONSTANTS

	/**
	 * Specify the queue URL for the United States East (Northern Virginia) Region.
	 */
	const REGION_US_E1 = 'autoscaling.us-east-1.amazonaws.com';

	/**
	 * Specify the queue URL for the United States East (Northern Virginia) Region.
	 */
	const REGION_VIRGINIA = self::REGION_US_E1;

	/**
	 * Specify the queue URL for the United States West (Northern California) Region.
	 */
	const REGION_US_W1 = 'autoscaling.us-west-1.amazonaws.com';

	/**
	 * Specify the queue URL for the United States West (Northern California) Region.
	 */
	const REGION_CALIFORNIA = self::REGION_US_W1;

	/**
	 * Specify the queue URL for the United States West (Oregon) Region.
	 */
	const REGION_US_W2 = 'autoscaling.us-west-2.amazonaws.com';

	/**
	 * Specify the queue URL for the United States West (Oregon) Region.
	 */
	const REGION_OREGON = self::REGION_US_W2;

	/**
	 * Specify the queue URL for the Europe West (Ireland) Region.
	 */
	const REGION_EU_W1 = 'autoscaling.eu-west-1.amazonaws.com';

	/**
	 * Specify the queue URL for the Europe West (Ireland) Region.
	 */
	const REGION_IRELAND = self::REGION_EU_W1;

	/**
	 * Specify the queue URL for the Asia Pacific Southeast (Singapore) Region.
	 */
	const REGION_APAC_SE1 = 'autoscaling.ap-southeast-1.amazonaws.com';

	/**
	 * Specify the queue URL for the Asia Pacific Southeast (Singapore) Region.
	 */
	const REGION_SINGAPORE = self::REGION_APAC_SE1;

	/**
	 * Specify the queue URL for the Asia Pacific Northeast (Tokyo) Region.
	 */
	const REGION_APAC_NE1 = 'autoscaling.ap-northeast-1.amazonaws.com';

	/**
	 * Specify the queue URL for the Asia Pacific Northeast (Tokyo) Region.
	 */
	const REGION_TOKYO = self::REGION_APAC_NE1;

	/**
	 * Specify the queue URL for the South America (Sao Paulo) Region.
	 */
	const REGION_SA_E1 = 'autoscaling.sa-east-1.amazonaws.com';

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
	 * Constructs a new instance of <AmazonAS>.
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
		$this->api_version = '2011-01-01';
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
	// SERVICE METHODS

	/**
	 * Creates a new Auto Scaling group with the specified name and other attributes. When the
	 * creation request is completed, the Auto Scaling group is ready to be used in other calls.
	 * 
	 * <p class="note">
	 * The Auto Scaling group name must be unique within the scope of your AWS account, and under the
	 * quota of Auto Scaling groups allowed for your account.
	 * </p>
	 *
	 * @param string $auto_scaling_group_name (Required) The name of the Auto Scaling group. [Constraints: The value must be between 1 and 255 characters, and must match the following regular expression pattern: <code>[\u0020-\uD7FF\uE000-\uFFFD\uD800\uDC00-\uDBFF\uDFFF\r\n\t]*</code>]
	 * @param string $launch_configuration_name (Required) The name of the launch configuration to use with the Auto Scaling group. [Constraints: The value must be between 1 and 1600 characters, and must match the following regular expression pattern: <code>[\u0020-\uD7FF\uE000-\uFFFD\uD800\uDC00-\uDBFF\uDFFF\r\n\t]*</code>]
	 * @param integer $min_size (Required) The minimum size of the Auto Scaling group.
	 * @param integer $max_size (Required) The maximum size of the Auto Scaling group.
	 * @param string|array $availability_zones (Required) A list of Availability Zones for the Auto Scaling group. Pass a string for a single value, or an indexed array for multiple values.
	 * @param array $opt (Optional) An associative array of parameters that can have the following keys: <ul>
	 * 	<li><code>DesiredCapacity</code> - <code>integer</code> - Optional - The number of Amazon EC2 instances that should be running in the group.</li>
	 * 	<li><code>DefaultCooldown</code> - <code>integer</code> - Optional - The amount of time, in seconds, after a scaling activity completes before any further trigger-related scaling activities can start.</li>
	 * 	<li><code>LoadBalancerNames</code> - <code>string|array</code> - Optional - A list of load balancers to use. Pass a string for a single value, or an indexed array for multiple values.</li>
	 * 	<li><code>HealthCheckType</code> - <code>string</code> - Optional - The service you want the health status from, Amazon EC2 or Elastic Load Balancer. Valid values are <code>EC2</code> or <code>ELB</code>. [Constraints: The value must be between 1 and 32 characters, and must match the following regular expression pattern: <code>[\u0020-\uD7FF\uE000-\uFFFD\uD800\uDC00-\uDBFF\uDFFF\r\n\t]*</code>]</li>
	 * 	<li><code>HealthCheckGracePeriod</code> - <code>integer</code> - Optional - Length of time in seconds after a new Amazon EC2 instance comes into service that Auto Scaling starts checking its health.</li>
	 * 	<li><code>PlacementGroup</code> - <code>string</code> - Optional - Physical location of your cluster placement group created in Amazon EC2. For more information about cluster placement group, see <a href="http://docs.amazonwebservices.com/AWSEC2/latest/UserGuide/using_cluster_computing.html">Using Cluster Instances</a> [Constraints: The value must be between 1 and 255 characters, and must match the following regular expression pattern: <code>[\u0020-\uD7FF\uE000-\uFFFD\uD800\uDC00-\uDBFF\uDFFF\r\n\t]*</code>]</li>
	 * 	<li><code>VPCZoneIdentifier</code> - <code>string</code> - Optional - A comma-separated list of subnet identifiers of Amazon Virtual Private Clouds (Amazon VPCs). When you specify subnets and Availability Zones with this call, ensure that the subnets' Availability Zones match the Availability Zones specified. [Constraints: The value must be between 1 and 255 characters, and must match the following regular expression pattern: <code>[\u0020-\uD7FF\uE000-\uFFFD\uD800\uDC00-\uDBFF\uDFFF\r\n\t]*</code>]</li>
	 * 	<li><code>Tags</code> - <code>array</code> - Optional - The tag to be created. Each tag should be defined by its resource ID, resource type, key, value, and a propagate flag. The <code>PropagateAtLaunch</code> flag defines whether the new tag will be applied to instances launched after the tag is created. <ul>
	 * 		<li><code>x</code> - <code>array</code> - Optional - This represents a simple array index. <ul>
	 * 			<li><code>ResourceId</code> - <code>string</code> - Optional - The name of the tag. [Constraints: The value must match the following regular expression pattern: <code>[\u0020-\uD7FF\uE000-\uFFFD\uD800\uDC00-\uDBFF\uDFFF\r\n\t]*</code>]</li>
	 * 			<li><code>ResourceType</code> - <code>string</code> - Optional - The kind of resource to which the tag is applied. [Constraints: The value must match the following regular expression pattern: <code>[\u0020-\uD7FF\uE000-\uFFFD\uD800\uDC00-\uDBFF\uDFFF\r\n\t]*</code>]</li>
	 * 			<li><code>Key</code> - <code>string</code> - Required - The key of the tag. [Constraints: The value must be between 1 and 128 characters, and must match the following regular expression pattern: <code>[\u0020-\uD7FF\uE000-\uFFFD\uD800\uDC00-\uDBFF\uDFFF\r\n\t]*</code>]</li>
	 * 			<li><code>Value</code> - <code>string</code> - Optional - The value of the tag. [Constraints: The value must be between 0 and 256 characters, and must match the following regular expression pattern: <code>[\u0020-\uD7FF\uE000-\uFFFD\uD800\uDC00-\uDBFF\uDFFF\r\n\t]*</code>]</li>
	 * 			<li><code>PropagateAtLaunch</code> - <code>boolean</code> - Optional - Specifies whether the new tag will be applied to instances launched after the tag is created. The same behavior applies to updates: If you change a tag, the changed tag will be applied to all instances launched after you made the change.</li>
	 * 		</ul></li>
	 * 	</ul></li>
	 * 	<li><code>curlopts</code> - <code>array</code> - Optional - A set of values to pass directly into <code>curl_setopt()</code>, where the key is a pre-defined <code>CURLOPT_*</code> constant.</li>
	 * 	<li><code>returnCurlHandle</code> - <code>boolean</code> - Optional - A private toggle specifying that the cURL handle be returned rather than actually completing the request. This toggle is useful for manually managed batch requests.</li></ul>
	 * @return CFResponse A <CFResponse> object containing a parsed HTTP response.
	 */
	public function create_auto_scaling_group($auto_scaling_group_name, $launch_configuration_name, $min_size, $max_size, $availability_zones, $opt = null)
	{
		if (!$opt) $opt = array();
		$opt['AutoScalingGroupName'] = $auto_scaling_group_name;
		$opt['LaunchConfigurationName'] = $launch_configuration_name;
		$opt['MinSize'] = $min_size;
		$opt['MaxSize'] = $max_size;
		
		// Required list (non-map)
		$opt = array_merge($opt, CFComplexType::map(array(
			'AvailabilityZones' => (is_array($availability_zones) ? $availability_zones : array($availability_zones))
		), 'member'));

		// Optional list (non-map)
		if (isset($opt['LoadBalancerNames']))
		{
			$opt = array_merge($opt, CFComplexType::map(array(
				'LoadBalancerNames' => (is_array($opt['LoadBalancerNames']) ? $opt['LoadBalancerNames'] : array($opt['LoadBalancerNames']))
			), 'member'));
			unset($opt['LoadBalancerNames']);
		}
		
		// Optional list + map
		if (isset($opt['Tags']))
		{
			$opt = array_merge($opt, CFComplexType::map(array(
				'Tags' => $opt['Tags']
			), 'member'));
			unset($opt['Tags']);
		}

		return $this->authenticate('CreateAutoScalingGroup', $opt);
	}

	/**
	 * Creates a new launch configuration. The launch configuration name must be unique within the
	 * scope of the client's AWS account. The maximum limit of launch configurations, which by default
	 * is 100, must not yet have been met; otherwise, the call will fail. When created, the new launch
	 * configuration is available for immediate use.
	 *  
	 * You can create a launch configuration with Amazon EC2 security groups or with Amazon VPC
	 * security groups. However, you can't use Amazon EC2 security groups together with Amazon VPC
	 * security groups, or vice versa.
	 * 
	 * <p class="note">
	 * At this time, Auto Scaling launch configurations don't support compressed (e.g. zipped) user
	 * data files.
	 * </p>
	 *
	 * @param string $launch_configuration_name (Required) The name of the launch configuration to create. [Constraints: The value must be between 1 and 255 characters, and must match the following regular expression pattern: <code>[\u0020-\uD7FF\uE000-\uFFFD\uD800\uDC00-\uDBFF\uDFFF\r\n\t]*</code>]
	 * @param string $image_id (Required) Unique ID of the <em>Amazon Machine Image</em> (AMI) which was assigned during registration. For more information about Amazon EC2 images, please see <a href="http://aws.amazon.com/ec2/">Amazon EC2 product documentation</a>. [Constraints: The value must be between 1 and 255 characters, and must match the following regular expression pattern: <code>[\u0020-\uD7FF\uE000-\uFFFD\uD800\uDC00-\uDBFF\uDFFF\r\n\t]*</code>]
	 * @param string $instance_type (Required) The instance type of the Amazon EC2 instance. For more information about Amazon EC2 instance types, please see <a href="http://aws.amazon.com/ec2/">Amazon EC2 product documentation</a> [Constraints: The value must be between 1 and 255 characters, and must match the following regular expression pattern: <code>[\u0020-\uD7FF\uE000-\uFFFD\uD800\uDC00-\uDBFF\uDFFF\r\n\t]*</code>]
	 * @param array $opt (Optional) An associative array of parameters that can have the following keys: <ul>
	 * 	<li><code>KeyName</code> - <code>string</code> - Optional - The name of the Amazon EC2 key pair. [Constraints: The value must be between 1 and 255 characters, and must match the following regular expression pattern: <code>[\u0020-\uD7FF\uE000-\uFFFD\uD800\uDC00-\uDBFF\uDFFF\r\n\t]*</code>]</li>
	 * 	<li><code>SecurityGroups</code> - <code>string|array</code> - Optional - The names of the security groups with which to associate Amazon EC2 or Amazon VPC instances. Specify Amazon EC2 security groups using security group names, such as <code>websrv</code>. Specify Amazon VPC security groups using security group IDs, such as <code>sg-12345678</code>. For more information about Amazon EC2 security groups, go to <a href="http://docs.amazonwebservices.com/AWSEC2/latest/UserGuide/index.html?using-network-security.html">Using Security Groups</a> in the Amazon EC2 product documentation. For more information about Amazon VPC security groups, go to <a href="http://docs.amazonwebservices.com/AmazonVPC/latest/UserGuide/index.html?VPC_SecurityGroups.html">Security Groups</a> in the Amazon VPC product documentation. Pass a string for a single value, or an indexed array for multiple values.</li>
	 * 	<li><code>UserData</code> - <code>string</code> - Optional - The user data available to the launched Amazon EC2 instances. For more information about Amazon EC2 user data, please see <a href="http://aws.amazon.com/ec2/">Amazon EC2 product documentation</a>. [Constraints: The value must be between 0 and 21847 characters, and must match the following regular expression pattern: <code>[\u0020-\uD7FF\uE000-\uFFFD\uD800\uDC00-\uDBFF\uDFFF\r\n\t]*</code>]</li>
	 * 	<li><code>KernelId</code> - <code>string</code> - Optional - The ID of the kernel associated with the Amazon EC2 AMI. [Constraints: The value must be between 1 and 255 characters, and must match the following regular expression pattern: <code>[\u0020-\uD7FF\uE000-\uFFFD\uD800\uDC00-\uDBFF\uDFFF\r\n\t]*</code>]</li>
	 * 	<li><code>RamdiskId</code> - <code>string</code> - Optional - The ID of the RAM disk associated with the Amazon EC2 AMI. [Constraints: The value must be between 1 and 255 characters, and must match the following regular expression pattern: <code>[\u0020-\uD7FF\uE000-\uFFFD\uD800\uDC00-\uDBFF\uDFFF\r\n\t]*</code>]</li>
	 * 	<li><code>BlockDeviceMappings</code> - <code>array</code> - Optional - A list of mappings that specify how block devices are exposed to the instance. Each mapping is made up of a <em>VirtualName</em>, a <em>DeviceName</em>, and an <em>ebs</em> data structure that contains information about the associated Elastic Block Storage volume. For more information about Amazon EC2 BlockDeviceMappings, go to <a href="http://docs.amazonwebservices.com/AWSEC2/latest/UserGuide/index.html?block-device-mapping-concepts.html">Block Device Mapping</a> in the Amazon EC2 product documentation. <ul>
	 * 		<li><code>x</code> - <code>array</code> - Optional - This represents a simple array index. <ul>
	 * 			<li><code>VirtualName</code> - <code>string</code> - Optional - The virtual name associated with the device. [Constraints: The value must be between 1 and 255 characters, and must match the following regular expression pattern: <code>[\u0020-\uD7FF\uE000-\uFFFD\uD800\uDC00-\uDBFF\uDFFF\r\n\t]*</code>]</li>
	 * 			<li><code>DeviceName</code> - <code>string</code> - Required - The name of the device within Amazon EC2. [Constraints: The value must be between 1 and 255 characters, and must match the following regular expression pattern: <code>[\u0020-\uD7FF\uE000-\uFFFD\uD800\uDC00-\uDBFF\uDFFF\r\n\t]*</code>]</li>
	 * 			<li><code>Ebs</code> - <code>array</code> - Optional - The Elastic Block Storage volume information. <ul>
	 * 				<li><code>x</code> - <code>array</code> - Optional - This represents a simple array index. <ul>
	 * 					<li><code>SnapshotId</code> - <code>string</code> - Optional - The snapshot ID. [Constraints: The value must be between 1 and 255 characters, and must match the following regular expression pattern: <code>[\u0020-\uD7FF\uE000-\uFFFD\uD800\uDC00-\uDBFF\uDFFF\r\n\t]*</code>]</li>
	 * 					<li><code>VolumeSize</code> - <code>integer</code> - Optional - The volume size, in gigabytes.</li>
	 * 				</ul></li>
	 * 			</ul></li>
	 * 		</ul></li>
	 * 	</ul></li>
	 * 	<li><code>InstanceMonitoring</code> - <code>array</code> - Optional - Enables detailed monitoring, which is enabled by default. When detailed monitoring is enabled, CloudWatch will generate metrics every minute and your account will be charged a fee. When you disable detailed monitoring, by specifying <code>False</code>, Cloudwatch will generate metrics every 5 minutes. For information about monitoring, see the <a href="http://aws.amazon.com/cloudwatch/">Amazon CloudWatch</a> product page. <ul>
	 * 		<li><code>x</code> - <code>array</code> - Optional - This represents a simple array index. <ul>
	 * 			<li><code>Enabled</code> - <code>boolean</code> - Optional - If <code>True</code>, instance monitoring is enabled.</li>
	 * 		</ul></li>
	 * 	</ul></li>
	 * 	<li><code>curlopts</code> - <code>array</code> - Optional - A set of values to pass directly into <code>curl_setopt()</code>, where the key is a pre-defined <code>CURLOPT_*</code> constant.</li>
	 * 	<li><code>returnCurlHandle</code> - <code>boolean</code> - Optional - A private toggle specifying that the cURL handle be returned rather than actually completing the request. This toggle is useful for manually managed batch requests.</li></ul>
	 * @return CFResponse A <CFResponse> object containing a parsed HTTP response.
	 */
	public function create_launch_configuration($launch_configuration_name, $image_id, $instance_type, $opt = null)
	{
		if (!$opt) $opt = array();
		$opt['LaunchConfigurationName'] = $launch_configuration_name;
		$opt['ImageId'] = $image_id;
		$opt['InstanceType'] = $instance_type;
		
		// Optional list (non-map)
		if (isset($opt['SecurityGroups']))
		{
			$opt = array_merge($opt, CFComplexType::map(array(
				'SecurityGroups' => (is_array($opt['SecurityGroups']) ? $opt['SecurityGroups'] : array($opt['SecurityGroups']))
			), 'member'));
			unset($opt['SecurityGroups']);
		}
		
		// Optional list + map
		if (isset($opt['BlockDeviceMappings']))
		{
			$opt = array_merge($opt, CFComplexType::map(array(
				'BlockDeviceMappings' => $opt['BlockDeviceMappings']
			), 'member'));
			unset($opt['BlockDeviceMappings']);
		}
		
		// Optional map (non-list)
		if (isset($opt['InstanceMonitoring']))
		{
			$opt = array_merge($opt, CFComplexType::map(array(
				'InstanceMonitoring' => $opt['InstanceMonitoring']
			), 'member'));
			unset($opt['InstanceMonitoring']);
		}

		return $this->authenticate('CreateLaunchConfiguration', $opt);
	}

	/**
	 * Creates new tags or updates existing tags for an Auto Scaling group.
	 *  
	 * When you use the <code>PropagateAtLaunch</code> flag, the tag you create will be applied to new
	 * instances launched by the Auto Scaling group. However, instances already running will not get
	 * the new or updated tag. Likewise, when you modify a tag, the updated version will be applied to
	 * new instances launched by the Auto Scaling group after the change. Instances that are already
	 * running that had the previous version of the tag will continue to have the older tag.
	 *
	 * @param array $tags (Required) The tag to be created or updated. Each tag should be defined by its resource ID, resource type, key, value, and a propagate flag. The <code>PropagateAtLaunch</code> flag defines whether the new tag will be applied to instances launched after the tag is created. <ul>
	 * 	<li><code>x</code> - <code>array</code> - Optional - This represents a simple array index. <ul>
	 * 		<li><code>ResourceId</code> - <code>string</code> - Optional - The name of the tag. [Constraints: The value must match the following regular expression pattern: <code>[\u0020-\uD7FF\uE000-\uFFFD\uD800\uDC00-\uDBFF\uDFFF\r\n\t]*</code>]</li>
	 * 		<li><code>ResourceType</code> - <code>string</code> - Optional - The kind of resource to which the tag is applied. [Constraints: The value must match the following regular expression pattern: <code>[\u0020-\uD7FF\uE000-\uFFFD\uD800\uDC00-\uDBFF\uDFFF\r\n\t]*</code>]</li>
	 * 		<li><code>Key</code> - <code>string</code> - Required - The key of the tag. [Constraints: The value must be between 1 and 128 characters, and must match the following regular expression pattern: <code>[\u0020-\uD7FF\uE000-\uFFFD\uD800\uDC00-\uDBFF\uDFFF\r\n\t]*</code>]</li>
	 * 		<li><code>Value</code> - <code>string</code> - Optional - The value of the tag. [Constraints: The value must be between 0 and 256 characters, and must match the following regular expression pattern: <code>[\u0020-\uD7FF\uE000-\uFFFD\uD800\uDC00-\uDBFF\uDFFF\r\n\t]*</code>]</li>
	 * 		<li><code>PropagateAtLaunch</code> - <code>boolean</code> - Optional - Specifies whether the new tag will be applied to instances launched after the tag is created. The same behavior applies to updates: If you change a tag, the changed tag will be applied to all instances launched after you made the change.</li>
	 * 	</ul></li>
	 * </ul>
	 * @param array $opt (Optional) An associative array of parameters that can have the following keys: <ul>
	 * 	<li><code>curlopts</code> - <code>array</code> - Optional - A set of values to pass directly into <code>curl_setopt()</code>, where the key is a pre-defined <code>CURLOPT_*</code> constant.</li>
	 * 	<li><code>returnCurlHandle</code> - <code>boolean</code> - Optional - A private toggle specifying that the cURL handle be returned rather than actually completing the request. This toggle is useful for manually managed batch requests.</li></ul>
	 * @return CFResponse A <CFResponse> object containing a parsed HTTP response.
	 */
	public function create_or_update_tags($tags, $opt = null)
	{
		if (!$opt) $opt = array();
				
		// Required list + map
		$opt = array_merge($opt, CFComplexType::map(array(
			'Tags' => (is_array($tags) ? $tags : array($tags))
		), 'member'));

		return $this->authenticate('CreateOrUpdateTags', $opt);
	}

	/**
	 * Deletes the specified Auto Scaling group if the group has no instances and no scaling
	 * activities in progress.
	 * 
	 * <p class="note">
	 * To remove all instances before calling <code>DeleteAutoScalingGroup</code>, you can call
	 * <code>UpdateAutoScalingGroup</code> to set the minimum and maximum size of the AutoScalingGroup
	 * to zero.
	 * </p>
	 *
	 * @param string $auto_scaling_group_name (Required) The name of the Auto Scaling group. [Constraints: The value must be between 1 and 1600 characters, and must match the following regular expression pattern: <code>[\u0020-\uD7FF\uE000-\uFFFD\uD800\uDC00-\uDBFF\uDFFF\r\n\t]*</code>]
	 * @param array $opt (Optional) An associative array of parameters that can have the following keys: <ul>
	 * 	<li><code>ForceDelete</code> - <code>boolean</code> - Optional - Starting with API version 2011-01-01, specifies that the Auto Scaling group will be deleted along with all instances associated with the group, without waiting for all instances to be terminated.</li>
	 * 	<li><code>curlopts</code> - <code>array</code> - Optional - A set of values to pass directly into <code>curl_setopt()</code>, where the key is a pre-defined <code>CURLOPT_*</code> constant.</li>
	 * 	<li><code>returnCurlHandle</code> - <code>boolean</code> - Optional - A private toggle specifying that the cURL handle be returned rather than actually completing the request. This toggle is useful for manually managed batch requests.</li></ul>
	 * @return CFResponse A <CFResponse> object containing a parsed HTTP response.
	 */
	public function delete_auto_scaling_group($auto_scaling_group_name, $opt = null)
	{
		if (!$opt) $opt = array();
		$opt['AutoScalingGroupName'] = $auto_scaling_group_name;
		
		return $this->authenticate('DeleteAutoScalingGroup', $opt);
	}

	/**
	 * Deletes the specified <code>LaunchConfiguration</code>.
	 *  
	 * The specified launch configuration must not be attached to an Auto Scaling group. When this
	 * call completes, the launch configuration is no longer available for use.
	 *
	 * @param string $launch_configuration_name (Required) The name of the launch configuration. [Constraints: The value must be between 1 and 1600 characters, and must match the following regular expression pattern: <code>[\u0020-\uD7FF\uE000-\uFFFD\uD800\uDC00-\uDBFF\uDFFF\r\n\t]*</code>]
	 * @param array $opt (Optional) An associative array of parameters that can have the following keys: <ul>
	 * 	<li><code>curlopts</code> - <code>array</code> - Optional - A set of values to pass directly into <code>curl_setopt()</code>, where the key is a pre-defined <code>CURLOPT_*</code> constant.</li>
	 * 	<li><code>returnCurlHandle</code> - <code>boolean</code> - Optional - A private toggle specifying that the cURL handle be returned rather than actually completing the request. This toggle is useful for manually managed batch requests.</li></ul>
	 * @return CFResponse A <CFResponse> object containing a parsed HTTP response.
	 */
	public function delete_launch_configuration($launch_configuration_name, $opt = null)
	{
		if (!$opt) $opt = array();
		$opt['LaunchConfigurationName'] = $launch_configuration_name;
		
		return $this->authenticate('DeleteLaunchConfiguration', $opt);
	}

	/**
	 * Deletes notifications created by <code>PutNotificationConfiguration</code>.
	 *
	 * @param string $auto_scaling_group_name (Required) The name of the Auto Scaling group. [Constraints: The value must be between 1 and 1600 characters, and must match the following regular expression pattern: <code>[\u0020-\uD7FF\uE000-\uFFFD\uD800\uDC00-\uDBFF\uDFFF\r\n\t]*</code>]
	 * @param string $topic_arn (Required) The Amazon Resource Name (ARN) of the Amazon Simple Notification Service (SNS) topic. [Constraints: The value must be between 1 and 1600 characters, and must match the following regular expression pattern: <code>[\u0020-\uD7FF\uE000-\uFFFD\uD800\uDC00-\uDBFF\uDFFF\r\n\t]*</code>]
	 * @param array $opt (Optional) An associative array of parameters that can have the following keys: <ul>
	 * 	<li><code>curlopts</code> - <code>array</code> - Optional - A set of values to pass directly into <code>curl_setopt()</code>, where the key is a pre-defined <code>CURLOPT_*</code> constant.</li>
	 * 	<li><code>returnCurlHandle</code> - <code>boolean</code> - Optional - A private toggle specifying that the cURL handle be returned rather than actually completing the request. This toggle is useful for manually managed batch requests.</li></ul>
	 * @return CFResponse A <CFResponse> object containing a parsed HTTP response.
	 */
	public function delete_notification_configuration($auto_scaling_group_name, $topic_arn, $opt = null)
	{
		if (!$opt) $opt = array();
		$opt['AutoScalingGroupName'] = $auto_scaling_group_name;
		$opt['TopicARN'] = $topic_arn;
		
		return $this->authenticate('DeleteNotificationConfiguration', $opt);
	}

	/**
	 * Deletes a policy created by <code>PutScalingPolicy</code>.
	 *
	 * @param string $policy_name (Required) The name or PolicyARN of the policy you want to delete. [Constraints: The value must be between 1 and 1600 characters, and must match the following regular expression pattern: <code>[\u0020-\uD7FF\uE000-\uFFFD\uD800\uDC00-\uDBFF\uDFFF\r\n\t]*</code>]
	 * @param array $opt (Optional) An associative array of parameters that can have the following keys: <ul>
	 * 	<li><code>AutoScalingGroupName</code> - <code>string</code> - Optional - The name of the Auto Scaling group. [Constraints: The value must be between 1 and 1600 characters, and must match the following regular expression pattern: <code>[\u0020-\uD7FF\uE000-\uFFFD\uD800\uDC00-\uDBFF\uDFFF\r\n\t]*</code>]</li>
	 * 	<li><code>curlopts</code> - <code>array</code> - Optional - A set of values to pass directly into <code>curl_setopt()</code>, where the key is a pre-defined <code>CURLOPT_*</code> constant.</li>
	 * 	<li><code>returnCurlHandle</code> - <code>boolean</code> - Optional - A private toggle specifying that the cURL handle be returned rather than actually completing the request. This toggle is useful for manually managed batch requests.</li></ul>
	 * @return CFResponse A <CFResponse> object containing a parsed HTTP response.
	 */
	public function delete_policy($policy_name, $opt = null)
	{
		if (!$opt) $opt = array();
		$opt['PolicyName'] = $policy_name;
		
		return $this->authenticate('DeletePolicy', $opt);
	}

	/**
	 * Deletes a scheduled action previously created using the
	 * <code>PutScheduledUpdateGroupAction</code>.
	 *
	 * @param string $scheduled_action_name (Required) The name of the action you want to delete. [Constraints: The value must be between 1 and 1600 characters, and must match the following regular expression pattern: <code>[\u0020-\uD7FF\uE000-\uFFFD\uD800\uDC00-\uDBFF\uDFFF\r\n\t]*</code>]
	 * @param array $opt (Optional) An associative array of parameters that can have the following keys: <ul>
	 * 	<li><code>AutoScalingGroupName</code> - <code>string</code> - Optional - The name of the Auto Scaling group. [Constraints: The value must be between 1 and 1600 characters, and must match the following regular expression pattern: <code>[\u0020-\uD7FF\uE000-\uFFFD\uD800\uDC00-\uDBFF\uDFFF\r\n\t]*</code>]</li>
	 * 	<li><code>curlopts</code> - <code>array</code> - Optional - A set of values to pass directly into <code>curl_setopt()</code>, where the key is a pre-defined <code>CURLOPT_*</code> constant.</li>
	 * 	<li><code>returnCurlHandle</code> - <code>boolean</code> - Optional - A private toggle specifying that the cURL handle be returned rather than actually completing the request. This toggle is useful for manually managed batch requests.</li></ul>
	 * @return CFResponse A <CFResponse> object containing a parsed HTTP response.
	 */
	public function delete_scheduled_action($scheduled_action_name, $opt = null)
	{
		if (!$opt) $opt = array();
		$opt['ScheduledActionName'] = $scheduled_action_name;
		
		return $this->authenticate('DeleteScheduledAction', $opt);
	}

	/**
	 * Removes the specified tags or a set of tags from a set of resources.
	 *
	 * @param array $tags (Required) The tag to be deleted. Each tag should be defined by its resource ID, resource type, key, value, and a propagate flag. The <code>PropagateAtLaunch</code> flag defines whether the new tag will be applied to instances launched after the tag is created. <ul>
	 * 	<li><code>x</code> - <code>array</code> - Optional - This represents a simple array index. <ul>
	 * 		<li><code>ResourceId</code> - <code>string</code> - Optional - The name of the tag. [Constraints: The value must match the following regular expression pattern: <code>[\u0020-\uD7FF\uE000-\uFFFD\uD800\uDC00-\uDBFF\uDFFF\r\n\t]*</code>]</li>
	 * 		<li><code>ResourceType</code> - <code>string</code> - Optional - The kind of resource to which the tag is applied. [Constraints: The value must match the following regular expression pattern: <code>[\u0020-\uD7FF\uE000-\uFFFD\uD800\uDC00-\uDBFF\uDFFF\r\n\t]*</code>]</li>
	 * 		<li><code>Key</code> - <code>string</code> - Required - The key of the tag. [Constraints: The value must be between 1 and 128 characters, and must match the following regular expression pattern: <code>[\u0020-\uD7FF\uE000-\uFFFD\uD800\uDC00-\uDBFF\uDFFF\r\n\t]*</code>]</li>
	 * 		<li><code>Value</code> - <code>string</code> - Optional - The value of the tag. [Constraints: The value must be between 0 and 256 characters, and must match the following regular expression pattern: <code>[\u0020-\uD7FF\uE000-\uFFFD\uD800\uDC00-\uDBFF\uDFFF\r\n\t]*</code>]</li>
	 * 		<li><code>PropagateAtLaunch</code> - <code>boolean</code> - Optional - Specifies whether the new tag will be applied to instances launched after the tag is created. The same behavior applies to updates: If you change a tag, the changed tag will be applied to all instances launched after you made the change.</li>
	 * 	</ul></li>
	 * </ul>
	 * @param array $opt (Optional) An associative array of parameters that can have the following keys: <ul>
	 * 	<li><code>curlopts</code> - <code>array</code> - Optional - A set of values to pass directly into <code>curl_setopt()</code>, where the key is a pre-defined <code>CURLOPT_*</code> constant.</li>
	 * 	<li><code>returnCurlHandle</code> - <code>boolean</code> - Optional - A private toggle specifying that the cURL handle be returned rather than actually completing the request. This toggle is useful for manually managed batch requests.</li></ul>
	 * @return CFResponse A <CFResponse> object containing a parsed HTTP response.
	 */
	public function delete_tags($tags, $opt = null)
	{
		if (!$opt) $opt = array();
				
		// Required list + map
		$opt = array_merge($opt, CFComplexType::map(array(
			'Tags' => (is_array($tags) ? $tags : array($tags))
		), 'member'));

		return $this->authenticate('DeleteTags', $opt);
	}

	/**
	 * Returns policy adjustment types for use in the <code>PutScalingPolicy</code> action.
	 *
	 * @param array $opt (Optional) An associative array of parameters that can have the following keys: <ul>
	 * 	<li><code>curlopts</code> - <code>array</code> - Optional - A set of values to pass directly into <code>curl_setopt()</code>, where the key is a pre-defined <code>CURLOPT_*</code> constant.</li>
	 * 	<li><code>returnCurlHandle</code> - <code>boolean</code> - Optional - A private toggle specifying that the cURL handle be returned rather than actually completing the request. This toggle is useful for manually managed batch requests.</li></ul>
	 * @return CFResponse A <CFResponse> object containing a parsed HTTP response.
	 */
	public function describe_adjustment_types($opt = null)
	{
		if (!$opt) $opt = array();
				
		return $this->authenticate('DescribeAdjustmentTypes', $opt);
	}

	/**
	 * Returns a full description of each Auto Scaling group in the given list. This includes all
	 * Amazon EC2 instances that are members of the group. If a list of names is not provided, the
	 * service returns the full details of all Auto Scaling groups.
	 *  
	 * This action supports pagination by returning a token if there are more pages to retrieve. To
	 * get the next page, call this action again with the returned token as the <code>NextToken</code>
	 * parameter.
	 *
	 * @param array $opt (Optional) An associative array of parameters that can have the following keys: <ul>
	 * 	<li><code>AutoScalingGroupNames</code> - <code>string|array</code> - Optional - A list of Auto Scaling group names. Pass a string for a single value, or an indexed array for multiple values.</li>
	 * 	<li><code>NextToken</code> - <code>string</code> - Optional - A string that marks the start of the next batch of returned results. [Constraints: The value must match the following regular expression pattern: <code>[\u0020-\uD7FF\uE000-\uFFFD\uD800\uDC00-\uDBFF\uDFFF\r\n\t]*</code>]</li>
	 * 	<li><code>MaxRecords</code> - <code>integer</code> - Optional - The maximum number of records to return.</li>
	 * 	<li><code>curlopts</code> - <code>array</code> - Optional - A set of values to pass directly into <code>curl_setopt()</code>, where the key is a pre-defined <code>CURLOPT_*</code> constant.</li>
	 * 	<li><code>returnCurlHandle</code> - <code>boolean</code> - Optional - A private toggle specifying that the cURL handle be returned rather than actually completing the request. This toggle is useful for manually managed batch requests.</li></ul>
	 * @return CFResponse A <CFResponse> object containing a parsed HTTP response.
	 */
	public function describe_auto_scaling_groups($opt = null)
	{
		if (!$opt) $opt = array();
				
		// Optional list (non-map)
		if (isset($opt['AutoScalingGroupNames']))
		{
			$opt = array_merge($opt, CFComplexType::map(array(
				'AutoScalingGroupNames' => (is_array($opt['AutoScalingGroupNames']) ? $opt['AutoScalingGroupNames'] : array($opt['AutoScalingGroupNames']))
			), 'member'));
			unset($opt['AutoScalingGroupNames']);
		}

		return $this->authenticate('DescribeAutoScalingGroups', $opt);
	}

	/**
	 * Returns a description of each Auto Scaling instance in the <code>InstanceIds</code> list. If a
	 * list is not provided, the service returns the full details of all instances up to a maximum of
	 * 50. By default, the service returns a list of 20 items.
	 *  
	 * This action supports pagination by returning a token if there are more pages to retrieve. To
	 * get the next page, call this action again with the returned token as the <code>NextToken</code>
	 * parameter.
	 *
	 * @param array $opt (Optional) An associative array of parameters that can have the following keys: <ul>
	 * 	<li><code>InstanceIds</code> - <code>string|array</code> - Optional - The list of Auto Scaling instances to describe. If this list is omitted, all auto scaling instances are described. The list of requested instances cannot contain more than 50 items. If unknown instances are requested, they are ignored with no error. Pass a string for a single value, or an indexed array for multiple values.</li>
	 * 	<li><code>MaxRecords</code> - <code>integer</code> - Optional - The maximum number of Auto Scaling instances to be described with each call.</li>
	 * 	<li><code>NextToken</code> - <code>string</code> - Optional - The token returned by a previous call to indicate that there is more data available. [Constraints: The value must match the following regular expression pattern: <code>[\u0020-\uD7FF\uE000-\uFFFD\uD800\uDC00-\uDBFF\uDFFF\r\n\t]*</code>]</li>
	 * 	<li><code>curlopts</code> - <code>array</code> - Optional - A set of values to pass directly into <code>curl_setopt()</code>, where the key is a pre-defined <code>CURLOPT_*</code> constant.</li>
	 * 	<li><code>returnCurlHandle</code> - <code>boolean</code> - Optional - A private toggle specifying that the cURL handle be returned rather than actually completing the request. This toggle is useful for manually managed batch requests.</li></ul>
	 * @return CFResponse A <CFResponse> object containing a parsed HTTP response.
	 */
	public function describe_auto_scaling_instances($opt = null)
	{
		if (!$opt) $opt = array();
				
		// Optional list (non-map)
		if (isset($opt['InstanceIds']))
		{
			$opt = array_merge($opt, CFComplexType::map(array(
				'InstanceIds' => (is_array($opt['InstanceIds']) ? $opt['InstanceIds'] : array($opt['InstanceIds']))
			), 'member'));
			unset($opt['InstanceIds']);
		}

		return $this->authenticate('DescribeAutoScalingInstances', $opt);
	}

	/**
	 * Returns a list of all notification types that are supported by Auto Scaling.
	 *
	 * @param array $opt (Optional) An associative array of parameters that can have the following keys: <ul>
	 * 	<li><code>curlopts</code> - <code>array</code> - Optional - A set of values to pass directly into <code>curl_setopt()</code>, where the key is a pre-defined <code>CURLOPT_*</code> constant.</li>
	 * 	<li><code>returnCurlHandle</code> - <code>boolean</code> - Optional - A private toggle specifying that the cURL handle be returned rather than actually completing the request. This toggle is useful for manually managed batch requests.</li></ul>
	 * @return CFResponse A <CFResponse> object containing a parsed HTTP response.
	 */
	public function describe_auto_scaling_notification_types($opt = null)
	{
		if (!$opt) $opt = array();
				
		return $this->authenticate('DescribeAutoScalingNotificationTypes', $opt);
	}

	/**
	 * Returns a full description of the launch configurations, or the specified launch
	 * configurations, if they exist.
	 *  
	 * If no name is specified, then the full details of all launch configurations are returned.
	 *
	 * @param array $opt (Optional) An associative array of parameters that can have the following keys: <ul>
	 * 	<li><code>LaunchConfigurationNames</code> - <code>string|array</code> - Optional - A list of launch configuration names. Pass a string for a single value, or an indexed array for multiple values.</li>
	 * 	<li><code>NextToken</code> - <code>string</code> - Optional - A string that marks the start of the next batch of returned results. [Constraints: The value must match the following regular expression pattern: <code>[\u0020-\uD7FF\uE000-\uFFFD\uD800\uDC00-\uDBFF\uDFFF\r\n\t]*</code>]</li>
	 * 	<li><code>MaxRecords</code> - <code>integer</code> - Optional - The maximum number of launch configurations. The default is 100.</li>
	 * 	<li><code>curlopts</code> - <code>array</code> - Optional - A set of values to pass directly into <code>curl_setopt()</code>, where the key is a pre-defined <code>CURLOPT_*</code> constant.</li>
	 * 	<li><code>returnCurlHandle</code> - <code>boolean</code> - Optional - A private toggle specifying that the cURL handle be returned rather than actually completing the request. This toggle is useful for manually managed batch requests.</li></ul>
	 * @return CFResponse A <CFResponse> object containing a parsed HTTP response.
	 */
	public function describe_launch_configurations($opt = null)
	{
		if (!$opt) $opt = array();
				
		// Optional list (non-map)
		if (isset($opt['LaunchConfigurationNames']))
		{
			$opt = array_merge($opt, CFComplexType::map(array(
				'LaunchConfigurationNames' => (is_array($opt['LaunchConfigurationNames']) ? $opt['LaunchConfigurationNames'] : array($opt['LaunchConfigurationNames']))
			), 'member'));
			unset($opt['LaunchConfigurationNames']);
		}

		return $this->authenticate('DescribeLaunchConfigurations', $opt);
	}

	/**
	 * Returns a list of metrics and a corresponding list of granularities for each metric.
	 *
	 * @param array $opt (Optional) An associative array of parameters that can have the following keys: <ul>
	 * 	<li><code>curlopts</code> - <code>array</code> - Optional - A set of values to pass directly into <code>curl_setopt()</code>, where the key is a pre-defined <code>CURLOPT_*</code> constant.</li>
	 * 	<li><code>returnCurlHandle</code> - <code>boolean</code> - Optional - A private toggle specifying that the cURL handle be returned rather than actually completing the request. This toggle is useful for manually managed batch requests.</li></ul>
	 * @return CFResponse A <CFResponse> object containing a parsed HTTP response.
	 */
	public function describe_metric_collection_types($opt = null)
	{
		if (!$opt) $opt = array();
				
		return $this->authenticate('DescribeMetricCollectionTypes', $opt);
	}

	/**
	 * Returns a list of notification actions associated with Auto Scaling groups for specified
	 * events.
	 *
	 * @param array $opt (Optional) An associative array of parameters that can have the following keys: <ul>
	 * 	<li><code>AutoScalingGroupNames</code> - <code>string|array</code> - Optional - The name of the Auto Scaling group. Pass a string for a single value, or an indexed array for multiple values.</li>
	 * 	<li><code>NextToken</code> - <code>string</code> - Optional - A string that is used to mark the start of the next batch of returned results for pagination. [Constraints: The value must match the following regular expression pattern: <code>[\u0020-\uD7FF\uE000-\uFFFD\uD800\uDC00-\uDBFF\uDFFF\r\n\t]*</code>]</li>
	 * 	<li><code>MaxRecords</code> - <code>integer</code> - Optional - Maximum number of records to be returned.</li>
	 * 	<li><code>curlopts</code> - <code>array</code> - Optional - A set of values to pass directly into <code>curl_setopt()</code>, where the key is a pre-defined <code>CURLOPT_*</code> constant.</li>
	 * 	<li><code>returnCurlHandle</code> - <code>boolean</code> - Optional - A private toggle specifying that the cURL handle be returned rather than actually completing the request. This toggle is useful for manually managed batch requests.</li></ul>
	 * @return CFResponse A <CFResponse> object containing a parsed HTTP response.
	 */
	public function describe_notification_configurations($opt = null)
	{
		if (!$opt) $opt = array();
				
		// Optional list (non-map)
		if (isset($opt['AutoScalingGroupNames']))
		{
			$opt = array_merge($opt, CFComplexType::map(array(
				'AutoScalingGroupNames' => (is_array($opt['AutoScalingGroupNames']) ? $opt['AutoScalingGroupNames'] : array($opt['AutoScalingGroupNames']))
			), 'member'));
			unset($opt['AutoScalingGroupNames']);
		}

		return $this->authenticate('DescribeNotificationConfigurations', $opt);
	}

	/**
	 * Returns descriptions of what each policy does. This action supports pagination. If the response
	 * includes a token, there are more records available. To get the additional records, repeat the
	 * request with the response token as the <code>NextToken</code> parameter.
	 *
	 * @param array $opt (Optional) An associative array of parameters that can have the following keys: <ul>
	 * 	<li><code>AutoScalingGroupName</code> - <code>string</code> - Optional - The name of the Auto Scaling group. [Constraints: The value must be between 1 and 1600 characters, and must match the following regular expression pattern: <code>[\u0020-\uD7FF\uE000-\uFFFD\uD800\uDC00-\uDBFF\uDFFF\r\n\t]*</code>]</li>
	 * 	<li><code>PolicyNames</code> - <code>string|array</code> - Optional - A list of policy names or policy ARNs to be described. If this list is omitted, all policy names are described. If an auto scaling group name is provided, the results are limited to that group. The list of requested policy names cannot contain more than 50 items. If unknown policy names are requested, they are ignored with no error. Pass a string for a single value, or an indexed array for multiple values.</li>
	 * 	<li><code>NextToken</code> - <code>string</code> - Optional - A string that is used to mark the start of the next batch of returned results for pagination. [Constraints: The value must match the following regular expression pattern: <code>[\u0020-\uD7FF\uE000-\uFFFD\uD800\uDC00-\uDBFF\uDFFF\r\n\t]*</code>]</li>
	 * 	<li><code>MaxRecords</code> - <code>integer</code> - Optional - The maximum number of policies that will be described with each call.</li>
	 * 	<li><code>curlopts</code> - <code>array</code> - Optional - A set of values to pass directly into <code>curl_setopt()</code>, where the key is a pre-defined <code>CURLOPT_*</code> constant.</li>
	 * 	<li><code>returnCurlHandle</code> - <code>boolean</code> - Optional - A private toggle specifying that the cURL handle be returned rather than actually completing the request. This toggle is useful for manually managed batch requests.</li></ul>
	 * @return CFResponse A <CFResponse> object containing a parsed HTTP response.
	 */
	public function describe_policies($opt = null)
	{
		if (!$opt) $opt = array();
				
		// Optional list (non-map)
		if (isset($opt['PolicyNames']))
		{
			$opt = array_merge($opt, CFComplexType::map(array(
				'PolicyNames' => (is_array($opt['PolicyNames']) ? $opt['PolicyNames'] : array($opt['PolicyNames']))
			), 'member'));
			unset($opt['PolicyNames']);
		}

		return $this->authenticate('DescribePolicies', $opt);
	}

	/**
	 * Returns the scaling activities for the specified Auto Scaling group.
	 *  
	 * If the specified <code>ActivityIds</code> list is empty, all the activities from the past six
	 * weeks are returned. Activities are sorted by completion time. Activities still in progress
	 * appear first on the list.
	 *  
	 * This action supports pagination. If the response includes a token, there are more records
	 * available. To get the additional records, repeat the request with the response token as the
	 * <code>NextToken</code> parameter.
	 *
	 * @param array $opt (Optional) An associative array of parameters that can have the following keys: <ul>
	 * 	<li><code>ActivityIds</code> - <code>string|array</code> - Optional - A list containing the activity IDs of the desired scaling activities. If this list is omitted, all activities are described. If an <code>AutoScalingGroupName</code> is provided, the results are limited to that group. The list of requested activities cannot contain more than 50 items. If unknown activities are requested, they are ignored with no error. Pass a string for a single value, or an indexed array for multiple values.</li>
	 * 	<li><code>AutoScalingGroupName</code> - <code>string</code> - Optional - The name of the <code>AutoScalingGroup</code>. [Constraints: The value must be between 1 and 1600 characters, and must match the following regular expression pattern: <code>[\u0020-\uD7FF\uE000-\uFFFD\uD800\uDC00-\uDBFF\uDFFF\r\n\t]*</code>]</li>
	 * 	<li><code>MaxRecords</code> - <code>integer</code> - Optional - The maximum number of scaling activities to return.</li>
	 * 	<li><code>NextToken</code> - <code>string</code> - Optional - A string that marks the start of the next batch of returned results for pagination. [Constraints: The value must match the following regular expression pattern: <code>[\u0020-\uD7FF\uE000-\uFFFD\uD800\uDC00-\uDBFF\uDFFF\r\n\t]*</code>]</li>
	 * 	<li><code>curlopts</code> - <code>array</code> - Optional - A set of values to pass directly into <code>curl_setopt()</code>, where the key is a pre-defined <code>CURLOPT_*</code> constant.</li>
	 * 	<li><code>returnCurlHandle</code> - <code>boolean</code> - Optional - A private toggle specifying that the cURL handle be returned rather than actually completing the request. This toggle is useful for manually managed batch requests.</li></ul>
	 * @return CFResponse A <CFResponse> object containing a parsed HTTP response.
	 */
	public function describe_scaling_activities($opt = null)
	{
		if (!$opt) $opt = array();
				
		// Optional list (non-map)
		if (isset($opt['ActivityIds']))
		{
			$opt = array_merge($opt, CFComplexType::map(array(
				'ActivityIds' => (is_array($opt['ActivityIds']) ? $opt['ActivityIds'] : array($opt['ActivityIds']))
			), 'member'));
			unset($opt['ActivityIds']);
		}

		return $this->authenticate('DescribeScalingActivities', $opt);
	}

	/**
	 * Returns scaling process types for use in the <code>ResumeProcesses</code> and
	 * <code>SuspendProcesses</code> actions.
	 *
	 * @param array $opt (Optional) An associative array of parameters that can have the following keys: <ul>
	 * 	<li><code>curlopts</code> - <code>array</code> - Optional - A set of values to pass directly into <code>curl_setopt()</code>, where the key is a pre-defined <code>CURLOPT_*</code> constant.</li>
	 * 	<li><code>returnCurlHandle</code> - <code>boolean</code> - Optional - A private toggle specifying that the cURL handle be returned rather than actually completing the request. This toggle is useful for manually managed batch requests.</li></ul>
	 * @return CFResponse A <CFResponse> object containing a parsed HTTP response.
	 */
	public function describe_scaling_process_types($opt = null)
	{
		if (!$opt) $opt = array();
				
		return $this->authenticate('DescribeScalingProcessTypes', $opt);
	}

	/**
	 * Lists all the actions scheduled for your Auto Scaling group that haven't been executed. To see
	 * a list of actions already executed, see the activity record returned in
	 * <code>DescribeScalingActivities</code>.
	 *
	 * @param array $opt (Optional) An associative array of parameters that can have the following keys: <ul>
	 * 	<li><code>AutoScalingGroupName</code> - <code>string</code> - Optional - The name of the Auto Scaling group. [Constraints: The value must be between 1 and 1600 characters, and must match the following regular expression pattern: <code>[\u0020-\uD7FF\uE000-\uFFFD\uD800\uDC00-\uDBFF\uDFFF\r\n\t]*</code>]</li>
	 * 	<li><code>ScheduledActionNames</code> - <code>string|array</code> - Optional - A list of scheduled actions to be described. If this list is omitted, all scheduled actions are described. The list of requested scheduled actions cannot contain more than 50 items. If an auto scaling group name is provided, the results are limited to that group. If unknown scheduled actions are requested, they are ignored with no error. Pass a string for a single value, or an indexed array for multiple values.</li>
	 * 	<li><code>StartTime</code> - <code>string</code> - Optional - The earliest scheduled start time to return. If scheduled action names are provided, this field will be ignored. May be passed as a number of seconds since UNIX Epoch, or any string compatible with <php:strtotime()>.</li>
	 * 	<li><code>EndTime</code> - <code>string</code> - Optional - The latest scheduled start time to return. If scheduled action names are provided, this field is ignored. May be passed as a number of seconds since UNIX Epoch, or any string compatible with <php:strtotime()>.</li>
	 * 	<li><code>NextToken</code> - <code>string</code> - Optional - A string that marks the start of the next batch of returned results. [Constraints: The value must match the following regular expression pattern: <code>[\u0020-\uD7FF\uE000-\uFFFD\uD800\uDC00-\uDBFF\uDFFF\r\n\t]*</code>]</li>
	 * 	<li><code>MaxRecords</code> - <code>integer</code> - Optional - The maximum number of scheduled actions to return.</li>
	 * 	<li><code>curlopts</code> - <code>array</code> - Optional - A set of values to pass directly into <code>curl_setopt()</code>, where the key is a pre-defined <code>CURLOPT_*</code> constant.</li>
	 * 	<li><code>returnCurlHandle</code> - <code>boolean</code> - Optional - A private toggle specifying that the cURL handle be returned rather than actually completing the request. This toggle is useful for manually managed batch requests.</li></ul>
	 * @return CFResponse A <CFResponse> object containing a parsed HTTP response.
	 */
	public function describe_scheduled_actions($opt = null)
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

		// Optional list (non-map)
		if (isset($opt['ScheduledActionNames']))
		{
			$opt = array_merge($opt, CFComplexType::map(array(
				'ScheduledActionNames' => (is_array($opt['ScheduledActionNames']) ? $opt['ScheduledActionNames'] : array($opt['ScheduledActionNames']))
			), 'member'));
			unset($opt['ScheduledActionNames']);
		}

		return $this->authenticate('DescribeScheduledActions', $opt);
	}

	/**
	 * Lists the Auto Scaling tags.
	 *  
	 * You can use filters to limit results when describing tags. For example, you can query for tags
	 * of a particular Auto Scaling group. You can specify multiple values for a filter. A tag must
	 * match at least one of the specified values for it to be included in the results.
	 *  
	 * You can also specify multiple filters. The result includes information for a particular tag
	 * only if it matches all your filters. If there's no match, no special message is returned.
	 *
	 * @param array $opt (Optional) An associative array of parameters that can have the following keys: <ul>
	 * 	<li><code>Filters</code> - <code>array</code> - Optional - The value of the filter type used to identify the tags to be returned. For example, you can filter so that<c>DescribeTags</c>returns tags according to Auto Scaling group, the key and value, or whether the new tag will be applied to instances launched after the tag is created (PropagateAtLaunch). <ul>
	 * 		<li><code>x</code> - <code>array</code> - Optional - This represents a simple array index. <ul>
	 * 			<li><code>Name</code> - <code>string</code> - Optional - The name of the filter. [Constraints: The value must match the following regular expression pattern: <code>[\u0020-\uD7FF\uE000-\uFFFD\uD800\uDC00-\uDBFF\uDFFF\r\n\t]*</code>]</li>
	 * 			<li><code>Values</code> - <code>string|array</code> - Optional - The value of the filter. Pass a string for a single value, or an indexed array for multiple values.</li>
	 * 		</ul></li>
	 * 	</ul></li>
	 * 	<li><code>NextToken</code> - <code>string</code> - Optional - A string that marks the start of the next batch of returned results. [Constraints: The value must match the following regular expression pattern: <code>[\u0020-\uD7FF\uE000-\uFFFD\uD800\uDC00-\uDBFF\uDFFF\r\n\t]*</code>]</li>
	 * 	<li><code>MaxRecords</code> - <code>integer</code> - Optional - The maximum number of records to return.</li>
	 * 	<li><code>curlopts</code> - <code>array</code> - Optional - A set of values to pass directly into <code>curl_setopt()</code>, where the key is a pre-defined <code>CURLOPT_*</code> constant.</li>
	 * 	<li><code>returnCurlHandle</code> - <code>boolean</code> - Optional - A private toggle specifying that the cURL handle be returned rather than actually completing the request. This toggle is useful for manually managed batch requests.</li></ul>
	 * @return CFResponse A <CFResponse> object containing a parsed HTTP response.
	 */
	public function describe_tags($opt = null)
	{
		if (!$opt) $opt = array();
				
		// Optional list + map
		if (isset($opt['Filters']))
		{
			$opt = array_merge($opt, CFComplexType::map(array(
				'Filters' => $opt['Filters']
			), 'member'));
			unset($opt['Filters']);
		}

		return $this->authenticate('DescribeTags', $opt);
	}

	/**
	 * Disables monitoring of group metrics for the Auto Scaling group specified in
	 * <code>AutoScalingGroupName</code>. You can specify the list of affected metrics with the
	 * <code>Metrics</code> parameter.
	 *
	 * @param string $auto_scaling_group_name (Required) The name or ARN of the Auto Scaling Group. [Constraints: The value must be between 1 and 1600 characters, and must match the following regular expression pattern: <code>[\u0020-\uD7FF\uE000-\uFFFD\uD800\uDC00-\uDBFF\uDFFF\r\n\t]*</code>]
	 * @param array $opt (Optional) An associative array of parameters that can have the following keys: <ul>
	 * 	<li><code>Metrics</code> - <code>string|array</code> - Optional - The list of metrics to disable. If no metrics are specified, all metrics are disabled. The following metrics are supported:<ul><li>GroupMinSize</li><li>GroupMaxSize</li><li>GroupDesiredCapacity</li><li>GroupInServiceInstances</li><li>GroupPendingInstances</li><li>GroupTerminatingInstances</li><li>GroupTotalInstances</li></ul> Pass a string for a single value, or an indexed array for multiple values.</li>
	 * 	<li><code>curlopts</code> - <code>array</code> - Optional - A set of values to pass directly into <code>curl_setopt()</code>, where the key is a pre-defined <code>CURLOPT_*</code> constant.</li>
	 * 	<li><code>returnCurlHandle</code> - <code>boolean</code> - Optional - A private toggle specifying that the cURL handle be returned rather than actually completing the request. This toggle is useful for manually managed batch requests.</li></ul>
	 * @return CFResponse A <CFResponse> object containing a parsed HTTP response.
	 */
	public function disable_metrics_collection($auto_scaling_group_name, $opt = null)
	{
		if (!$opt) $opt = array();
		$opt['AutoScalingGroupName'] = $auto_scaling_group_name;
		
		// Optional list (non-map)
		if (isset($opt['Metrics']))
		{
			$opt = array_merge($opt, CFComplexType::map(array(
				'Metrics' => (is_array($opt['Metrics']) ? $opt['Metrics'] : array($opt['Metrics']))
			), 'member'));
			unset($opt['Metrics']);
		}

		return $this->authenticate('DisableMetricsCollection', $opt);
	}

	/**
	 * Enables monitoring of group metrics for the Auto Scaling group specified in
	 * <code>AutoScalingGroupName</code>. You can specify the list of enabled metrics with the
	 * <code>Metrics</code> parameter.
	 *  
	 * Auto scaling metrics collection can be turned on only if the <code>InstanceMonitoring</code>
	 * flag, in the Auto Scaling group's launch configuration, is set to <code>True</code>.
	 *
	 * @param string $auto_scaling_group_name (Required) The name or ARN of the Auto Scaling group. [Constraints: The value must be between 1 and 1600 characters, and must match the following regular expression pattern: <code>[\u0020-\uD7FF\uE000-\uFFFD\uD800\uDC00-\uDBFF\uDFFF\r\n\t]*</code>]
	 * @param string $granularity (Required) The granularity to associate with the metrics to collect. Currently, the only legal granularity is "1Minute". [Constraints: The value must be between 1 and 255 characters, and must match the following regular expression pattern: <code>[\u0020-\uD7FF\uE000-\uFFFD\uD800\uDC00-\uDBFF\uDFFF\r\n\t]*</code>]
	 * @param array $opt (Optional) An associative array of parameters that can have the following keys: <ul>
	 * 	<li><code>Metrics</code> - <code>string|array</code> - Optional - The list of metrics to collect. If no metrics are specified, all metrics are enabled. The following metrics are supported:<ul><li>GroupMinSize</li><li>GroupMaxSize</li><li>GroupDesiredCapacity</li><li>GroupInServiceInstances</li><li>GroupPendingInstances</li><li>GroupTerminatingInstances</li><li>GroupTotalInstances</li></ul> Pass a string for a single value, or an indexed array for multiple values.</li>
	 * 	<li><code>curlopts</code> - <code>array</code> - Optional - A set of values to pass directly into <code>curl_setopt()</code>, where the key is a pre-defined <code>CURLOPT_*</code> constant.</li>
	 * 	<li><code>returnCurlHandle</code> - <code>boolean</code> - Optional - A private toggle specifying that the cURL handle be returned rather than actually completing the request. This toggle is useful for manually managed batch requests.</li></ul>
	 * @return CFResponse A <CFResponse> object containing a parsed HTTP response.
	 */
	public function enable_metrics_collection($auto_scaling_group_name, $granularity, $opt = null)
	{
		if (!$opt) $opt = array();
		$opt['AutoScalingGroupName'] = $auto_scaling_group_name;
		$opt['Granularity'] = $granularity;
		
		// Optional list (non-map)
		if (isset($opt['Metrics']))
		{
			$opt = array_merge($opt, CFComplexType::map(array(
				'Metrics' => (is_array($opt['Metrics']) ? $opt['Metrics'] : array($opt['Metrics']))
			), 'member'));
			unset($opt['Metrics']);
		}

		return $this->authenticate('EnableMetricsCollection', $opt);
	}

	/**
	 * Runs the policy you create for your Auto Scaling group in <code>PutScalingPolicy</code>.
	 *
	 * @param string $policy_name (Required) The name or PolicyARN of the policy you want to run. [Constraints: The value must be between 1 and 1600 characters, and must match the following regular expression pattern: <code>[\u0020-\uD7FF\uE000-\uFFFD\uD800\uDC00-\uDBFF\uDFFF\r\n\t]*</code>]
	 * @param array $opt (Optional) An associative array of parameters that can have the following keys: <ul>
	 * 	<li><code>AutoScalingGroupName</code> - <code>string</code> - Optional - The name or ARN of the Auto Scaling group. [Constraints: The value must be between 1 and 1600 characters, and must match the following regular expression pattern: <code>[\u0020-\uD7FF\uE000-\uFFFD\uD800\uDC00-\uDBFF\uDFFF\r\n\t]*</code>]</li>
	 * 	<li><code>HonorCooldown</code> - <code>boolean</code> - Optional - Set to <code>True</code> if you want Auto Scaling to reject this request when the Auto Scaling group is in cooldown.</li>
	 * 	<li><code>curlopts</code> - <code>array</code> - Optional - A set of values to pass directly into <code>curl_setopt()</code>, where the key is a pre-defined <code>CURLOPT_*</code> constant.</li>
	 * 	<li><code>returnCurlHandle</code> - <code>boolean</code> - Optional - A private toggle specifying that the cURL handle be returned rather than actually completing the request. This toggle is useful for manually managed batch requests.</li></ul>
	 * @return CFResponse A <CFResponse> object containing a parsed HTTP response.
	 */
	public function execute_policy($policy_name, $opt = null)
	{
		if (!$opt) $opt = array();
		$opt['PolicyName'] = $policy_name;
		
		return $this->authenticate('ExecutePolicy', $opt);
	}

	/**
	 * Configures an Auto Scaling group to send notifications when specified events take place.
	 * Subscribers to this topic can have messages for events delivered to an endpoint such as a web
	 * server or email address.
	 *  
	 * A new <code>PutNotificationConfiguration</code> overwrites an existing configuration.
	 *
	 * @param string $auto_scaling_group_name (Required) The name of the Auto Scaling group. [Constraints: The value must be between 1 and 1600 characters, and must match the following regular expression pattern: <code>[\u0020-\uD7FF\uE000-\uFFFD\uD800\uDC00-\uDBFF\uDFFF\r\n\t]*</code>]
	 * @param string $topic_arn (Required) The Amazon Resource Name (ARN) of the Amazon Simple Notification Service (SNS) topic. [Constraints: The value must be between 1 and 1600 characters, and must match the following regular expression pattern: <code>[\u0020-\uD7FF\uE000-\uFFFD\uD800\uDC00-\uDBFF\uDFFF\r\n\t]*</code>]
	 * @param string|array $notification_types (Required) The type of events that will trigger the notification. For more information, go to <code>DescribeAutoScalingNotificationTypes</code>. Pass a string for a single value, or an indexed array for multiple values.
	 * @param array $opt (Optional) An associative array of parameters that can have the following keys: <ul>
	 * 	<li><code>curlopts</code> - <code>array</code> - Optional - A set of values to pass directly into <code>curl_setopt()</code>, where the key is a pre-defined <code>CURLOPT_*</code> constant.</li>
	 * 	<li><code>returnCurlHandle</code> - <code>boolean</code> - Optional - A private toggle specifying that the cURL handle be returned rather than actually completing the request. This toggle is useful for manually managed batch requests.</li></ul>
	 * @return CFResponse A <CFResponse> object containing a parsed HTTP response.
	 */
	public function put_notification_configuration($auto_scaling_group_name, $topic_arn, $notification_types, $opt = null)
	{
		if (!$opt) $opt = array();
		$opt['AutoScalingGroupName'] = $auto_scaling_group_name;
		$opt['TopicARN'] = $topic_arn;
		
		// Required list (non-map)
		$opt = array_merge($opt, CFComplexType::map(array(
			'NotificationTypes' => (is_array($notification_types) ? $notification_types : array($notification_types))
		), 'member'));

		return $this->authenticate('PutNotificationConfiguration', $opt);
	}

	/**
	 * Creates or updates a policy for an Auto Scaling group. To update an existing policy, use the
	 * existing policy name and set the parameter(s) you want to change. Any existing parameter not
	 * changed in an update to an existing policy is not changed in this update request.
	 *
	 * @param string $auto_scaling_group_name (Required) The name or ARN of the Auto Scaling group. [Constraints: The value must be between 1 and 1600 characters, and must match the following regular expression pattern: <code>[\u0020-\uD7FF\uE000-\uFFFD\uD800\uDC00-\uDBFF\uDFFF\r\n\t]*</code>]
	 * @param string $policy_name (Required) The name of the policy you want to create or update. [Constraints: The value must be between 1 and 255 characters, and must match the following regular expression pattern: <code>[\u0020-\uD7FF\uE000-\uFFFD\uD800\uDC00-\uDBFF\uDFFF\r\n\t]*</code>]
	 * @param integer $scaling_adjustment (Required) The number of instances by which to scale. <code>AdjustmentType</code> determines the interpretation of this number (e.g., as an absolute number or as a percentage of the existing Auto Scaling group size). A positive increment adds to the current capacity and a negative value removes from the current capacity.
	 * @param string $adjustment_type (Required) Specifies whether the <code>ScalingAdjustment</code> is an absolute number or a percentage of the current capacity. Valid values are <code>ChangeInCapacity</code>, <code>ExactCapacity</code>, and <code>PercentChangeInCapacity</code>. [Constraints: The value must be between 1 and 255 characters, and must match the following regular expression pattern: <code>[\u0020-\uD7FF\uE000-\uFFFD\uD800\uDC00-\uDBFF\uDFFF\r\n\t]*</code>]
	 * @param array $opt (Optional) An associative array of parameters that can have the following keys: <ul>
	 * 	<li><code>Cooldown</code> - <code>integer</code> - Optional - The amount of time, in seconds, after a scaling activity completes before any further trigger-related scaling activities can start.</li>
	 * 	<li><code>curlopts</code> - <code>array</code> - Optional - A set of values to pass directly into <code>curl_setopt()</code>, where the key is a pre-defined <code>CURLOPT_*</code> constant.</li>
	 * 	<li><code>returnCurlHandle</code> - <code>boolean</code> - Optional - A private toggle specifying that the cURL handle be returned rather than actually completing the request. This toggle is useful for manually managed batch requests.</li></ul>
	 * @return CFResponse A <CFResponse> object containing a parsed HTTP response.
	 */
	public function put_scaling_policy($auto_scaling_group_name, $policy_name, $scaling_adjustment, $adjustment_type, $opt = null)
	{
		if (!$opt) $opt = array();
		$opt['AutoScalingGroupName'] = $auto_scaling_group_name;
		$opt['PolicyName'] = $policy_name;
		$opt['ScalingAdjustment'] = $scaling_adjustment;
		$opt['AdjustmentType'] = $adjustment_type;
		
		return $this->authenticate('PutScalingPolicy', $opt);
	}

	/**
	 * Creates a scheduled scaling action for an Auto Scaling group. If you leave a parameter
	 * unspecified, the corresponding value remains unchanged in the affected Auto Scaling group.
	 *
	 * @param string $auto_scaling_group_name (Required) The name or ARN of the Auto Scaling group. [Constraints: The value must be between 1 and 1600 characters, and must match the following regular expression pattern: <code>[\u0020-\uD7FF\uE000-\uFFFD\uD800\uDC00-\uDBFF\uDFFF\r\n\t]*</code>]
	 * @param string $scheduled_action_name (Required) The name of this scaling action. [Constraints: The value must be between 1 and 255 characters, and must match the following regular expression pattern: <code>[\u0020-\uD7FF\uE000-\uFFFD\uD800\uDC00-\uDBFF\uDFFF\r\n\t]*</code>]
	 * @param array $opt (Optional) An associative array of parameters that can have the following keys: <ul>
	 * 	<li><code>Time</code> - <code>string</code> - Optional - <code>Time</code> is deprecated. The time for this action to start. <code>Time</code> is an alias for <code>StartTime</code> and can be specified instead of <code>StartTime</code>, or vice versa. If both <code>Time</code> and <code>StartTime</code> are specified, their values should be identical. Otherwise, <code>PutScheduledUpdateGroupAction</code> will return an error. May be passed as a number of seconds since UNIX Epoch, or any string compatible with <php:strtotime()>.</li>
	 * 	<li><code>StartTime</code> - <code>string</code> - Optional - The time for this action to start, as in <code>--start-time 2010-06-01T00:00:00Z</code>. When <code>StartTime</code> and <code>EndTime</code> are specified with <code>Recurrence</code>, they form the boundaries of when the recurring action will start and stop. May be passed as a number of seconds since UNIX Epoch, or any string compatible with <php:strtotime()>.</li>
	 * 	<li><code>EndTime</code> - <code>string</code> - Optional - The time for this action to end. May be passed as a number of seconds since UNIX Epoch, or any string compatible with <php:strtotime()>.</li>
	 * 	<li><code>Recurrence</code> - <code>string</code> - Optional - The time when recurring future actions will start. Start time is specified by the user following the Unix cron syntax format. For information about cron syntax, go to <a href="http://en.wikipedia.org/wiki/Cron">Wikipedia, The Free Encyclopedia</a>. When <code>StartTime</code> and <code>EndTime</code> are specified with <code>Recurrence</code>, they form the boundaries of when the recurring action will start and stop. [Constraints: The value must be between 1 and 255 characters, and must match the following regular expression pattern: <code>[\u0020-\uD7FF\uE000-\uFFFD\uD800\uDC00-\uDBFF\uDFFF\r\n\t]*</code>]</li>
	 * 	<li><code>MinSize</code> - <code>integer</code> - Optional - The minimum size for the new Auto Scaling group.</li>
	 * 	<li><code>MaxSize</code> - <code>integer</code> - Optional - The maximum size for the Auto Scaling group.</li>
	 * 	<li><code>DesiredCapacity</code> - <code>integer</code> - Optional - The number of Amazon EC2 instances that should be running in the group.</li>
	 * 	<li><code>curlopts</code> - <code>array</code> - Optional - A set of values to pass directly into <code>curl_setopt()</code>, where the key is a pre-defined <code>CURLOPT_*</code> constant.</li>
	 * 	<li><code>returnCurlHandle</code> - <code>boolean</code> - Optional - A private toggle specifying that the cURL handle be returned rather than actually completing the request. This toggle is useful for manually managed batch requests.</li></ul>
	 * @return CFResponse A <CFResponse> object containing a parsed HTTP response.
	 */
	public function put_scheduled_update_group_action($auto_scaling_group_name, $scheduled_action_name, $opt = null)
	{
		if (!$opt) $opt = array();
		$opt['AutoScalingGroupName'] = $auto_scaling_group_name;
		$opt['ScheduledActionName'] = $scheduled_action_name;
		
		// Optional DateTime
		if (isset($opt['Time']))
		{
			$opt['Time'] = $this->util->convert_date_to_iso8601($opt['Time']);
		}
		
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

		return $this->authenticate('PutScheduledUpdateGroupAction', $opt);
	}

	/**
	 * Resumes Auto Scaling processes for an Auto Scaling group. For more information, see
	 * <code>SuspendProcesses</code> and <code>ProcessType</code>.
	 *
	 * @param string $auto_scaling_group_name (Required) The name or Amazon Resource Name (ARN) of the Auto Scaling group. [Constraints: The value must be between 1 and 1600 characters, and must match the following regular expression pattern: <code>[\u0020-\uD7FF\uE000-\uFFFD\uD800\uDC00-\uDBFF\uDFFF\r\n\t]*</code>]
	 * @param array $opt (Optional) An associative array of parameters that can have the following keys: <ul>
	 * 	<li><code>ScalingProcesses</code> - <code>string|array</code> - Optional - The processes that you want to suspend or resume, which can include one or more of the following:<ul><li>Launch</li><li>Terminate</li><li>HealthCheck</li><li>ReplaceUnhealthy</li><li>AZRebalance</li><li>AlarmNotifications</li><li>ScheduledActions</li><li>AddToLoadBalancer</li></ul>To suspend all process types, omit this parameter. Pass a string for a single value, or an indexed array for multiple values.</li>
	 * 	<li><code>curlopts</code> - <code>array</code> - Optional - A set of values to pass directly into <code>curl_setopt()</code>, where the key is a pre-defined <code>CURLOPT_*</code> constant.</li>
	 * 	<li><code>returnCurlHandle</code> - <code>boolean</code> - Optional - A private toggle specifying that the cURL handle be returned rather than actually completing the request. This toggle is useful for manually managed batch requests.</li></ul>
	 * @return CFResponse A <CFResponse> object containing a parsed HTTP response.
	 */
	public function resume_processes($auto_scaling_group_name, $opt = null)
	{
		if (!$opt) $opt = array();
		$opt['AutoScalingGroupName'] = $auto_scaling_group_name;
		
		// Optional list (non-map)
		if (isset($opt['ScalingProcesses']))
		{
			$opt = array_merge($opt, CFComplexType::map(array(
				'ScalingProcesses' => (is_array($opt['ScalingProcesses']) ? $opt['ScalingProcesses'] : array($opt['ScalingProcesses']))
			), 'member'));
			unset($opt['ScalingProcesses']);
		}

		return $this->authenticate('ResumeProcesses', $opt);
	}

	/**
	 * Adjusts the desired size of the <code>AutoScalingGroup</code> by initiating scaling activities.
	 * When reducing the size of the group, it is not possible to define which Amazon EC2 instances
	 * will be terminated. This applies to any Auto Scaling decisions that might result in terminating
	 * instances.
	 *  
	 * There are two common use cases for <code>SetDesiredCapacity</code>: one for users of the Auto
	 * Scaling triggering system, and another for developers who write their own triggering systems.
	 * Both use cases relate to the concept of cooldown.
	 *  
	 * In the first case, if you use the Auto Scaling triggering system,
	 * <code>SetDesiredCapacity</code> changes the size of your Auto Scaling group without regard to
	 * the cooldown period. This could be useful, for example, if Auto Scaling did something
	 * unexpected for some reason. If your cooldown period is 10 minutes, Auto Scaling would normally
	 * reject requests to change the size of the group for that entire 10-minute period. The
	 * <code>SetDesiredCapacity</code> command allows you to circumvent this restriction and change
	 * the size of the group before the end of the cooldown period.
	 *  
	 * In the second case, if you write your own triggering system, you can use
	 * <code>SetDesiredCapacity</code> to control the size of your Auto Scaling group. If you want the
	 * same cooldown functionality that Auto Scaling offers, you can configure
	 * <code>SetDesiredCapacity</code> to honor cooldown by setting the <code>HonorCooldown</code>
	 * parameter to <code>true</code>.
	 *
	 * @param string $auto_scaling_group_name (Required) The name of the Auto Scaling group. [Constraints: The value must be between 1 and 1600 characters, and must match the following regular expression pattern: <code>[\u0020-\uD7FF\uE000-\uFFFD\uD800\uDC00-\uDBFF\uDFFF\r\n\t]*</code>]
	 * @param integer $desired_capacity (Required) The new capacity setting for the Auto Scaling group.
	 * @param array $opt (Optional) An associative array of parameters that can have the following keys: <ul>
	 * 	<li><code>HonorCooldown</code> - <code>boolean</code> - Optional - By default, <code>SetDesiredCapacity</code> overrides any cooldown period. Set to <code>True</code> if you want Auto Scaling to reject this request when the Auto Scaling group is in cooldown.</li>
	 * 	<li><code>curlopts</code> - <code>array</code> - Optional - A set of values to pass directly into <code>curl_setopt()</code>, where the key is a pre-defined <code>CURLOPT_*</code> constant.</li>
	 * 	<li><code>returnCurlHandle</code> - <code>boolean</code> - Optional - A private toggle specifying that the cURL handle be returned rather than actually completing the request. This toggle is useful for manually managed batch requests.</li></ul>
	 * @return CFResponse A <CFResponse> object containing a parsed HTTP response.
	 */
	public function set_desired_capacity($auto_scaling_group_name, $desired_capacity, $opt = null)
	{
		if (!$opt) $opt = array();
		$opt['AutoScalingGroupName'] = $auto_scaling_group_name;
		$opt['DesiredCapacity'] = $desired_capacity;
		
		return $this->authenticate('SetDesiredCapacity', $opt);
	}

	/**
	 * Sets the health status of an instance.
	 *
	 * @param string $instance_id (Required) The identifier of the Amazon EC2 instance. [Constraints: The value must be between 1 and 16 characters, and must match the following regular expression pattern: <code>[\u0020-\uD7FF\uE000-\uFFFD\uD800\uDC00-\uDBFF\uDFFF\r\n\t]*</code>]
	 * @param string $health_status (Required) The health status of the instance. "Healthy" means that the instance is healthy and should remain in service. "Unhealthy" means that the instance is unhealthy. Auto Scaling should terminate and replace it. [Constraints: The value must be between 1 and 32 characters, and must match the following regular expression pattern: <code>[\u0020-\uD7FF\uE000-\uFFFD\uD800\uDC00-\uDBFF\uDFFF\r\n\t]*</code>]
	 * @param array $opt (Optional) An associative array of parameters that can have the following keys: <ul>
	 * 	<li><code>ShouldRespectGracePeriod</code> - <code>boolean</code> - Optional - If True, this call should respect the grace period associated with the group.</li>
	 * 	<li><code>curlopts</code> - <code>array</code> - Optional - A set of values to pass directly into <code>curl_setopt()</code>, where the key is a pre-defined <code>CURLOPT_*</code> constant.</li>
	 * 	<li><code>returnCurlHandle</code> - <code>boolean</code> - Optional - A private toggle specifying that the cURL handle be returned rather than actually completing the request. This toggle is useful for manually managed batch requests.</li></ul>
	 * @return CFResponse A <CFResponse> object containing a parsed HTTP response.
	 */
	public function set_instance_health($instance_id, $health_status, $opt = null)
	{
		if (!$opt) $opt = array();
		$opt['InstanceId'] = $instance_id;
		$opt['HealthStatus'] = $health_status;
		
		return $this->authenticate('SetInstanceHealth', $opt);
	}

	/**
	 * Suspends Auto Scaling processes for an Auto Scaling group. To suspend specific process types,
	 * specify them by name with the <code>ScalingProcesses.member.N</code> parameter. To suspend all
	 * process types, omit the <code>ScalingProcesses.member.N</code> parameter.
	 * 
	 * <p class="important"></p> 
	 * Suspending either of the two primary process types, <code>Launch</code> or
	 * <code>Terminate</code>, can prevent other process types from functioning properly. For more
	 * information about processes and their dependencies, see <code>ProcessType</code>.
	 *  
	 * To resume processes that have been suspended, use <code>ResumeProcesses</code>.
	 *
	 * @param string $auto_scaling_group_name (Required) The name or Amazon Resource Name (ARN) of the Auto Scaling group. [Constraints: The value must be between 1 and 1600 characters, and must match the following regular expression pattern: <code>[\u0020-\uD7FF\uE000-\uFFFD\uD800\uDC00-\uDBFF\uDFFF\r\n\t]*</code>]
	 * @param array $opt (Optional) An associative array of parameters that can have the following keys: <ul>
	 * 	<li><code>ScalingProcesses</code> - <code>string|array</code> - Optional - The processes that you want to suspend or resume, which can include one or more of the following:<ul><li>Launch</li><li>Terminate</li><li>HealthCheck</li><li>ReplaceUnhealthy</li><li>AZRebalance</li><li>AlarmNotifications</li><li>ScheduledActions</li><li>AddToLoadBalancer</li></ul>To suspend all process types, omit this parameter. Pass a string for a single value, or an indexed array for multiple values.</li>
	 * 	<li><code>curlopts</code> - <code>array</code> - Optional - A set of values to pass directly into <code>curl_setopt()</code>, where the key is a pre-defined <code>CURLOPT_*</code> constant.</li>
	 * 	<li><code>returnCurlHandle</code> - <code>boolean</code> - Optional - A private toggle specifying that the cURL handle be returned rather than actually completing the request. This toggle is useful for manually managed batch requests.</li></ul>
	 * @return CFResponse A <CFResponse> object containing a parsed HTTP response.
	 */
	public function suspend_processes($auto_scaling_group_name, $opt = null)
	{
		if (!$opt) $opt = array();
		$opt['AutoScalingGroupName'] = $auto_scaling_group_name;
		
		// Optional list (non-map)
		if (isset($opt['ScalingProcesses']))
		{
			$opt = array_merge($opt, CFComplexType::map(array(
				'ScalingProcesses' => (is_array($opt['ScalingProcesses']) ? $opt['ScalingProcesses'] : array($opt['ScalingProcesses']))
			), 'member'));
			unset($opt['ScalingProcesses']);
		}

		return $this->authenticate('SuspendProcesses', $opt);
	}

	/**
	 * Terminates the specified instance. Optionally, the desired group size can be adjusted.
	 * 
	 * <p class="note">
	 * This call simply registers a termination request. The termination of the instance cannot happen
	 * immediately.
	 * </p>
	 *
	 * @param string $instance_id (Required) The ID of the Amazon EC2 instance to be terminated. [Constraints: The value must be between 1 and 16 characters, and must match the following regular expression pattern: <code>[\u0020-\uD7FF\uE000-\uFFFD\uD800\uDC00-\uDBFF\uDFFF\r\n\t]*</code>]
	 * @param boolean $should_decrement_desired_capacity (Required) Specifies whether (<em>true</em>) or not (<em>false</em>) terminating this instance should also decrement the size of the <code>AutoScalingGroup</code>.
	 * @param array $opt (Optional) An associative array of parameters that can have the following keys: <ul>
	 * 	<li><code>curlopts</code> - <code>array</code> - Optional - A set of values to pass directly into <code>curl_setopt()</code>, where the key is a pre-defined <code>CURLOPT_*</code> constant.</li>
	 * 	<li><code>returnCurlHandle</code> - <code>boolean</code> - Optional - A private toggle specifying that the cURL handle be returned rather than actually completing the request. This toggle is useful for manually managed batch requests.</li></ul>
	 * @return CFResponse A <CFResponse> object containing a parsed HTTP response.
	 */
	public function terminate_instance_in_auto_scaling_group($instance_id, $should_decrement_desired_capacity, $opt = null)
	{
		if (!$opt) $opt = array();
		$opt['InstanceId'] = $instance_id;
		$opt['ShouldDecrementDesiredCapacity'] = $should_decrement_desired_capacity;
		
		return $this->authenticate('TerminateInstanceInAutoScalingGroup', $opt);
	}

	/**
	 * Updates the configuration for the specified <code>AutoScalingGroup</code>.
	 * 
	 * <p class="note"></p> 
	 * To update an Auto Scaling group with a launch configuration that has the
	 * <code>InstanceMonitoring</code> flag set to <code>False</code>, you must first ensure that
	 * collection of group metrics is disabled. Otherwise, calls to
	 * <code>UpdateAutoScalingGroup</code> will fail. If you have previously enabled group metrics
	 * collection, you can disable collection of all group metrics by calling
	 * <code>DisableMetricsCollection</code>.
	 *  
	 * The new settings are registered upon the completion of this call. Any launch configuration
	 * settings take effect on any triggers after this call returns. Triggers that are currently in
	 * progress aren't affected.
	 * 
	 * <p class="note">
	 * If the new values are specified for the <em>MinSize</em> or <em>MaxSize</em> parameters, then
	 * there will be an implicit call to <code>SetDesiredCapacity</code> to set the group to the new
	 * <em>MaxSize</em>. All optional parameters are left unchanged if not passed in the request.
	 * </p>
	 *
	 * @param string $auto_scaling_group_name (Required) The name of the Auto Scaling group. [Constraints: The value must be between 1 and 1600 characters, and must match the following regular expression pattern: <code>[\u0020-\uD7FF\uE000-\uFFFD\uD800\uDC00-\uDBFF\uDFFF\r\n\t]*</code>]
	 * @param array $opt (Optional) An associative array of parameters that can have the following keys: <ul>
	 * 	<li><code>LaunchConfigurationName</code> - <code>string</code> - Optional - The name of the launch configuration. [Constraints: The value must be between 1 and 1600 characters, and must match the following regular expression pattern: <code>[\u0020-\uD7FF\uE000-\uFFFD\uD800\uDC00-\uDBFF\uDFFF\r\n\t]*</code>]</li>
	 * 	<li><code>MinSize</code> - <code>integer</code> - Optional - The minimum size of the Auto Scaling group.</li>
	 * 	<li><code>MaxSize</code> - <code>integer</code> - Optional - The maximum size of the Auto Scaling group.</li>
	 * 	<li><code>DesiredCapacity</code> - <code>integer</code> - Optional - The desired capacity for the Auto Scaling group.</li>
	 * 	<li><code>DefaultCooldown</code> - <code>integer</code> - Optional - The amount of time, in seconds, after a scaling activity completes before any further trigger-related scaling activities can start.</li>
	 * 	<li><code>AvailabilityZones</code> - <code>string|array</code> - Optional - Availability Zones for the group. Pass a string for a single value, or an indexed array for multiple values.</li>
	 * 	<li><code>HealthCheckType</code> - <code>string</code> - Optional - The service of interest for the health status check, either "EC2" for Amazon EC2 or "ELB" for Elastic Load Balancing. [Constraints: The value must be between 1 and 32 characters, and must match the following regular expression pattern: <code>[\u0020-\uD7FF\uE000-\uFFFD\uD800\uDC00-\uDBFF\uDFFF\r\n\t]*</code>]</li>
	 * 	<li><code>HealthCheckGracePeriod</code> - <code>integer</code> - Optional - The length of time that Auto Scaling waits before checking an instance's health status. The grace period begins when an instance comes into service.</li>
	 * 	<li><code>PlacementGroup</code> - <code>string</code> - Optional - The name of the cluster placement group, if applicable. For more information, go to <a href="http://docs.amazonwebservices.com/AWSEC2/latest/UserGuide/using_cluster_computing.html">Using Cluster Instances</a> in the Amazon EC2 User Guide. [Constraints: The value must be between 1 and 255 characters, and must match the following regular expression pattern: <code>[\u0020-\uD7FF\uE000-\uFFFD\uD800\uDC00-\uDBFF\uDFFF\r\n\t]*</code>]</li>
	 * 	<li><code>VPCZoneIdentifier</code> - <code>string</code> - Optional - The subnet identifier for the Amazon VPC connection, if applicable. You can specify several subnets in a comma-separated list. When you specify <code>VPCZoneIdentifier</code> with <code>AvailabilityZones</code>, ensure that the subnets' Availability Zones match the values you specify for <code>AvailabilityZones</code>. [Constraints: The value must be between 1 and 255 characters, and must match the following regular expression pattern: <code>[\u0020-\uD7FF\uE000-\uFFFD\uD800\uDC00-\uDBFF\uDFFF\r\n\t]*</code>]</li>
	 * 	<li><code>curlopts</code> - <code>array</code> - Optional - A set of values to pass directly into <code>curl_setopt()</code>, where the key is a pre-defined <code>CURLOPT_*</code> constant.</li>
	 * 	<li><code>returnCurlHandle</code> - <code>boolean</code> - Optional - A private toggle specifying that the cURL handle be returned rather than actually completing the request. This toggle is useful for manually managed batch requests.</li></ul>
	 * @return CFResponse A <CFResponse> object containing a parsed HTTP response.
	 */
	public function update_auto_scaling_group($auto_scaling_group_name, $opt = null)
	{
		if (!$opt) $opt = array();
		$opt['AutoScalingGroupName'] = $auto_scaling_group_name;
		
		// Optional list (non-map)
		if (isset($opt['AvailabilityZones']))
		{
			$opt = array_merge($opt, CFComplexType::map(array(
				'AvailabilityZones' => (is_array($opt['AvailabilityZones']) ? $opt['AvailabilityZones'] : array($opt['AvailabilityZones']))
			), 'member'));
			unset($opt['AvailabilityZones']);
		}

		return $this->authenticate('UpdateAutoScalingGroup', $opt);
	}
}


/*%******************************************************************************************%*/
// EXCEPTIONS

class AS_Exception extends Exception {}
