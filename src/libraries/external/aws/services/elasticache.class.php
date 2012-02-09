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
 * Amazon ElastiCache is a web service that makes it easier to set up, operate, and scale a
 * distributed cache in the cloud.
 *  
 * With Amazon ElastiCache, customers gain all of the benefits of a high-performance, in-memory
 * cache with far less of the administrative burden of launching and managing a distributed cache.
 * The service makes set-up, scaling, and cluster failure handling much simpler than in a
 * self-managed cache deployment.
 *  
 * In addition, through integration with Amazon CloudWatch, customers get enhanced visibility into
 * the key performance statistics associated with their cache and can receive alarms if a part of
 * their cache runs hot.
 *
 * @version 2012.01.16
 * @license See the included NOTICE.md file for complete information.
 * @copyright See the included NOTICE.md file for complete information.
 * @link http://aws.amazon.com/elasticache/ AWS ElastiCache
 * @link http://aws.amazon.com/elasticache/documentation/ AWS ElastiCache documentation
 */
class AmazonElastiCache extends CFRuntime
{
	/*%******************************************************************************************%*/
	// CLASS CONSTANTS

	/**
	 * Specify the queue URL for the United States East (Northern Virginia) Region.
	 */
	const REGION_US_E1 = 'elasticache.us-east-1.amazonaws.com';

	/**
	 * Specify the queue URL for the United States East (Northern Virginia) Region.
	 */
	const REGION_VIRGINIA = self::REGION_US_E1;

	/**
	 * Specify the queue URL for the United States West (Northern California) Region.
	 */
	const REGION_US_W1 = 'elasticache.us-west-1.amazonaws.com';

	/**
	 * Specify the queue URL for the United States West (Northern California) Region.
	 */
	const REGION_CALIFORNIA = self::REGION_US_W1;

	/**
	 * Specify the queue URL for the Europe West (Ireland) Region.
	 */
	const REGION_EU_W1 = 'elasticache.eu-west-1.amazonaws.com';

	/**
	 * Specify the queue URL for the Europe West (Ireland) Region.
	 */
	const REGION_IRELAND = self::REGION_EU_W1;

	/**
	 * Specify the queue URL for the Asia Pacific Southeast (Singapore) Region.
	 */
	const REGION_APAC_SE1 = 'elasticache.ap-southeast-1.amazonaws.com';

	/**
	 * Specify the queue URL for the Asia Pacific Southeast (Singapore) Region.
	 */
	const REGION_SINGAPORE = self::REGION_APAC_SE1;

	/**
	 * Specify the queue URL for the Asia Pacific Northeast (Tokyo) Region.
	 */
	const REGION_APAC_NE1 = 'elasticache.ap-northeast-1.amazonaws.com';

	/**
	 * Specify the queue URL for the Asia Pacific Northeast (Tokyo) Region.
	 */
	const REGION_TOKYO = self::REGION_APAC_NE1;

	/**
	 * Default service endpoint.
	 */
	const DEFAULT_URL = self::REGION_US_E1;


	/*%******************************************************************************************%*/
	// CONSTRUCTOR

	/**
	 * Constructs a new instance of <AmazonElastiCache>.
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
		$this->api_version = '2011-07-15';
		$this->hostname = self::DEFAULT_URL;
		$this->auth_class = 'AuthV2Query';

		return parent::__construct($options);
	}


	/*%******************************************************************************************%*/
	// SETTERS

	/**
	 * This allows you to explicitly sets the region for the service to use.
	 *
	 * @param string $region (Required) The region to explicitly set. Available options are <REGION_US_E1>, <REGION_US_W1>, <REGION_EU_W1>, <REGION_APAC_SE1>, <REGION_APAC_NE1>.
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
	 * Authorizes ingress to a CacheSecurityGroup using EC2 Security Groups as authorization
	 * (therefore the application using the cache must be running on EC2 clusters). This API requires
	 * the following parameters: EC2SecurityGroupName and EC2SecurityGroupOwnerId.
	 * 
	 * <p class="note">
	 * You cannot authorize ingress from an EC2 security group in one Region to an Amazon Cache
	 * Cluster in another.
	 * </p>
	 *
	 * @param string $cache_security_group_name (Required) The name of the Cache Security Group to authorize.
	 * @param string $ec2_security_group_name (Required) Name of the EC2 Security Group to include in the authorization.
	 * @param string $ec2_security_group_owner_id (Required) AWS Account Number of the owner of the security group specified in the EC2SecurityGroupName parameter. The AWS Access Key ID is not an acceptable value.
	 * @param array $opt (Optional) An associative array of parameters that can have the following keys: <ul>
	 * 	<li><code>curlopts</code> - <code>array</code> - Optional - A set of values to pass directly into <code>curl_setopt()</code>, where the key is a pre-defined <code>CURLOPT_*</code> constant.</li>
	 * 	<li><code>returnCurlHandle</code> - <code>boolean</code> - Optional - A private toggle specifying that the cURL handle be returned rather than actually completing the request. This toggle is useful for manually managed batch requests.</li></ul>
	 * @return CFResponse A <CFResponse> object containing a parsed HTTP response.
	 */
	public function authorize_cache_security_group_ingress($cache_security_group_name, $ec2_security_group_name, $ec2_security_group_owner_id, $opt = null)
	{
		if (!$opt) $opt = array();
		$opt['CacheSecurityGroupName'] = $cache_security_group_name;
		$opt['EC2SecurityGroupName'] = $ec2_security_group_name;
		$opt['EC2SecurityGroupOwnerId'] = $ec2_security_group_owner_id;
		
		return $this->authenticate('AuthorizeCacheSecurityGroupIngress', $opt);
	}

	/**
	 * Creates a new Cache Cluster.
	 *
	 * @param string $cache_cluster_id (Required) The Cache Cluster identifier. This parameter is stored as a lowercase string. Constraints:<ul><li>Must contain from 1 to 20 alphanumeric characters or hyphens.</li><li>First character must be a letter.</li><li>Cannot end with a hyphen or contain two consecutive hyphens.</li></ul>Example: <code>mycachecluster</code>
	 * @param integer $num_cache_nodes (Required) The number of Cache Nodes the Cache Cluster should have.
	 * @param string $cache_node_type (Required) The compute and memory capacity of nodes in a Cache Cluster. Valid values: <code>cache.m1.large | cache.m1.xlarge | cache.m2.xlarge | cache.m2.2xlarge | cache.m2.4xlarge | cache.c1.xlarge</code>
	 * @param string $engine (Required) The name of the cache engine to be used for this Cache Cluster. <p class="note">Currently, <em>memcached</em> is the only cache engine supported by the service.</p>
	 * @param string|array $cache_security_group_names (Required) A list of Cache Security Group Names to associate with this Cache Cluster. Pass a string for a single value, or an indexed array for multiple values.
	 * @param array $opt (Optional) An associative array of parameters that can have the following keys: <ul>
	 * 	<li><code>EngineVersion</code> - <code>string</code> - Optional - The version of the cache engine to be used for this cluster.</li>
	 * 	<li><code>CacheParameterGroupName</code> - <code>string</code> - Optional - The name of the cache parameter group to associate with this Cache cluster. If this argument is omitted, the default CacheParameterGroup for the specified engine will be used.</li>
	 * 	<li><code>PreferredAvailabilityZone</code> - <code>string</code> - Optional - The EC2 Availability Zone that the Cache Cluster will be created in. In normal use, all CacheNodes belonging to a CacheCluster are placed in the preferred availability zone. In rare circumstances, some of the CacheNodes might temporarily be in a different availability zone. Default: System chosen (random) availability zone.</li>
	 * 	<li><code>PreferredMaintenanceWindow</code> - <code>string</code> - Optional - The weekly time range (in UTC) during which system maintenance can occur. Example: <code>sun:05:00-sun:09:00</code></li>
	 * 	<li><code>Port</code> - <code>integer</code> - Optional - The port number on which each of the Cache Nodes will accept connections.</li>
	 * 	<li><code>NotificationTopicArn</code> - <code>string</code> - Optional - The Amazon Resource Name (ARN) of the Amazon Simple Notification Service (SNS) topic to which notifications will be sent. <p class="note">The Amazon SNS topic owner must be the same as the Cache Cluster owner.</p></li>
	 * 	<li><code>AutoMinorVersionUpgrade</code> - <code>boolean</code> - Optional - Indicates that minor engine upgrades will be applied automatically to the Cache Cluster during the maintenance window. Default: <code>true</code></li>
	 * 	<li><code>curlopts</code> - <code>array</code> - Optional - A set of values to pass directly into <code>curl_setopt()</code>, where the key is a pre-defined <code>CURLOPT_*</code> constant.</li>
	 * 	<li><code>returnCurlHandle</code> - <code>boolean</code> - Optional - A private toggle specifying that the cURL handle be returned rather than actually completing the request. This toggle is useful for manually managed batch requests.</li></ul>
	 * @return CFResponse A <CFResponse> object containing a parsed HTTP response.
	 */
	public function create_cache_cluster($cache_cluster_id, $num_cache_nodes, $cache_node_type, $engine, $cache_security_group_names, $opt = null)
	{
		if (!$opt) $opt = array();
		$opt['CacheClusterId'] = $cache_cluster_id;
		$opt['NumCacheNodes'] = $num_cache_nodes;
		$opt['CacheNodeType'] = $cache_node_type;
		$opt['Engine'] = $engine;
		
		// Required list (non-map)
		$opt = array_merge($opt, CFComplexType::map(array(
			'CacheSecurityGroupNames' => (is_array($cache_security_group_names) ? $cache_security_group_names : array($cache_security_group_names))
		), 'member'));

		return $this->authenticate('CreateCacheCluster', $opt);
	}

	/**
	 * Creates a new Cache Parameter Group. Cache Parameter groups control the parameters for a Cache
	 * Cluster.
	 *
	 * @param string $cache_parameter_group_name (Required) The name of the Cache Parameter Group.
	 * @param string $cache_parameter_group_family (Required) The name of the Cache Parameter Group Family the Cache Parameter Group can be used with. <p class="note">Currently, <em>memcached1.4</em> is the only cache parameter group family supported by the service.</p>
	 * @param string $description (Required) The description for the Cache Parameter Group.
	 * @param array $opt (Optional) An associative array of parameters that can have the following keys: <ul>
	 * 	<li><code>curlopts</code> - <code>array</code> - Optional - A set of values to pass directly into <code>curl_setopt()</code>, where the key is a pre-defined <code>CURLOPT_*</code> constant.</li>
	 * 	<li><code>returnCurlHandle</code> - <code>boolean</code> - Optional - A private toggle specifying that the cURL handle be returned rather than actually completing the request. This toggle is useful for manually managed batch requests.</li></ul>
	 * @return CFResponse A <CFResponse> object containing a parsed HTTP response.
	 */
	public function create_cache_parameter_group($cache_parameter_group_name, $cache_parameter_group_family, $description, $opt = null)
	{
		if (!$opt) $opt = array();
		$opt['CacheParameterGroupName'] = $cache_parameter_group_name;
		$opt['CacheParameterGroupFamily'] = $cache_parameter_group_family;
		$opt['Description'] = $description;
		
		return $this->authenticate('CreateCacheParameterGroup', $opt);
	}

	/**
	 * Creates a new Cache Security Group. Cache Security groups control access to one or more Cache
	 * Clusters.
	 *
	 * @param string $cache_security_group_name (Required) The name for the Cache Security Group. This value is stored as a lowercase string. Constraints: Must contain no more than 255 alphanumeric characters. Must not be "Default". Example: <code>mysecuritygroup</code>
	 * @param string $description (Required) The description for the Cache Security Group.
	 * @param array $opt (Optional) An associative array of parameters that can have the following keys: <ul>
	 * 	<li><code>curlopts</code> - <code>array</code> - Optional - A set of values to pass directly into <code>curl_setopt()</code>, where the key is a pre-defined <code>CURLOPT_*</code> constant.</li>
	 * 	<li><code>returnCurlHandle</code> - <code>boolean</code> - Optional - A private toggle specifying that the cURL handle be returned rather than actually completing the request. This toggle is useful for manually managed batch requests.</li></ul>
	 * @return CFResponse A <CFResponse> object containing a parsed HTTP response.
	 */
	public function create_cache_security_group($cache_security_group_name, $description, $opt = null)
	{
		if (!$opt) $opt = array();
		$opt['CacheSecurityGroupName'] = $cache_security_group_name;
		$opt['Description'] = $description;
		
		return $this->authenticate('CreateCacheSecurityGroup', $opt);
	}

	/**
	 * Deletes a previously provisioned Cache Cluster. A successful response from the web service
	 * indicates the request was received correctly. This action cannot be canceled or reverted.
	 * DeleteCacheCluster deletes all associated Cache Nodes, node endpoints and the Cache Cluster
	 * itself.
	 *
	 * @param string $cache_cluster_id (Required) The Cache Cluster identifier for the Cache Cluster to be deleted. This parameter isn't case sensitive.
	 * @param array $opt (Optional) An associative array of parameters that can have the following keys: <ul>
	 * 	<li><code>curlopts</code> - <code>array</code> - Optional - A set of values to pass directly into <code>curl_setopt()</code>, where the key is a pre-defined <code>CURLOPT_*</code> constant.</li>
	 * 	<li><code>returnCurlHandle</code> - <code>boolean</code> - Optional - A private toggle specifying that the cURL handle be returned rather than actually completing the request. This toggle is useful for manually managed batch requests.</li></ul>
	 * @return CFResponse A <CFResponse> object containing a parsed HTTP response.
	 */
	public function delete_cache_cluster($cache_cluster_id, $opt = null)
	{
		if (!$opt) $opt = array();
		$opt['CacheClusterId'] = $cache_cluster_id;
		
		return $this->authenticate('DeleteCacheCluster', $opt);
	}

	/**
	 * Deletes the specified CacheParameterGroup. The CacheParameterGroup cannot be deleted if it is
	 * associated with any cache clusters.
	 *
	 * @param string $cache_parameter_group_name (Required) The name of the Cache Parameter Group to delete. <p class="note">The specified cache security group must not be associated with any Cache clusters.</p>
	 * @param array $opt (Optional) An associative array of parameters that can have the following keys: <ul>
	 * 	<li><code>curlopts</code> - <code>array</code> - Optional - A set of values to pass directly into <code>curl_setopt()</code>, where the key is a pre-defined <code>CURLOPT_*</code> constant.</li>
	 * 	<li><code>returnCurlHandle</code> - <code>boolean</code> - Optional - A private toggle specifying that the cURL handle be returned rather than actually completing the request. This toggle is useful for manually managed batch requests.</li></ul>
	 * @return CFResponse A <CFResponse> object containing a parsed HTTP response.
	 */
	public function delete_cache_parameter_group($cache_parameter_group_name, $opt = null)
	{
		if (!$opt) $opt = array();
		$opt['CacheParameterGroupName'] = $cache_parameter_group_name;
		
		return $this->authenticate('DeleteCacheParameterGroup', $opt);
	}

	/**
	 * Deletes a Cache Security Group.
	 * 
	 * <p class="note">
	 * The specified Cache Security Group must not be associated with any Cache Clusters.
	 * </p>
	 *
	 * @param string $cache_security_group_name (Required) The name of the Cache Security Group to delete. <p class="note">You cannot delete the default security group.</p>
	 * @param array $opt (Optional) An associative array of parameters that can have the following keys: <ul>
	 * 	<li><code>curlopts</code> - <code>array</code> - Optional - A set of values to pass directly into <code>curl_setopt()</code>, where the key is a pre-defined <code>CURLOPT_*</code> constant.</li>
	 * 	<li><code>returnCurlHandle</code> - <code>boolean</code> - Optional - A private toggle specifying that the cURL handle be returned rather than actually completing the request. This toggle is useful for manually managed batch requests.</li></ul>
	 * @return CFResponse A <CFResponse> object containing a parsed HTTP response.
	 */
	public function delete_cache_security_group($cache_security_group_name, $opt = null)
	{
		if (!$opt) $opt = array();
		$opt['CacheSecurityGroupName'] = $cache_security_group_name;
		
		return $this->authenticate('DeleteCacheSecurityGroup', $opt);
	}

	/**
	 * Returns information about all provisioned Cache Clusters if no Cache Cluster identifier is
	 * specified, or about a specific Cache Cluster if a Cache Cluster identifier is supplied.
	 *  
	 * Cluster information will be returned by default. An optional <em>ShowDetails</em> flag can be
	 * used to retrieve detailed information about the Cache Nodes associated with the Cache Cluster.
	 * Details include the DNS address and port for the Cache Node endpoint.
	 *  
	 * If the cluster is in the CREATING state, only cluster level information will be displayed until
	 * all of the nodes are successfully provisioned.
	 *  
	 * If the cluster is in the DELETING state, only cluster level information will be displayed.
	 *  
	 * While adding Cache Nodes, node endpoint information and creation time for the additional nodes
	 * will not be displayed until they are completely provisioned. The cluster lifecycle tells the
	 * customer when new nodes are AVAILABLE.
	 *  
	 * While removing existing Cache Nodes from an cluster, endpoint information for the removed nodes
	 * will not be displayed.
	 *  
	 * DescribeCacheClusters supports pagination.
	 *
	 * @param array $opt (Optional) An associative array of parameters that can have the following keys: <ul>
	 * 	<li><code>CacheClusterId</code> - <code>string</code> - Optional - The user-supplied cluster identifier. If this parameter is specified, only information about that specific Cache Cluster is returned. This parameter isn't case sensitive.</li>
	 * 	<li><code>MaxRecords</code> - <code>integer</code> - Optional - The maximum number of records to include in the response. If more records exist than the specified <em>MaxRecords</em> value, a marker is included in the response so that the remaining results may be retrieved. Default: 100 Constraints: minimum 20, maximum 100</li>
	 * 	<li><code>Marker</code> - <code>string</code> - Optional - An optional marker provided in the previous DescribeCacheClusters request. If this parameter is specified, the response includes only records beyond the marker, up to the value specified by <em>MaxRecords</em>.</li>
	 * 	<li><code>ShowCacheNodeInfo</code> - <code>boolean</code> - Optional - An optional flag that can be included in the DescribeCacheCluster request to retrieve Cache Nodes information.</li>
	 * 	<li><code>curlopts</code> - <code>array</code> - Optional - A set of values to pass directly into <code>curl_setopt()</code>, where the key is a pre-defined <code>CURLOPT_*</code> constant.</li>
	 * 	<li><code>returnCurlHandle</code> - <code>boolean</code> - Optional - A private toggle specifying that the cURL handle be returned rather than actually completing the request. This toggle is useful for manually managed batch requests.</li></ul>
	 * @return CFResponse A <CFResponse> object containing a parsed HTTP response.
	 */
	public function describe_cache_clusters($opt = null)
	{
		if (!$opt) $opt = array();
				
		return $this->authenticate('DescribeCacheClusters', $opt);
	}

	/**
	 * Returns a list of CacheParameterGroup descriptions. If a CacheParameterGroupName is specified,
	 * the list will contain only the descriptions of the specified CacheParameterGroup.
	 *
	 * @param array $opt (Optional) An associative array of parameters that can have the following keys: <ul>
	 * 	<li><code>CacheParameterGroupName</code> - <code>string</code> - Optional - The name of a specific cache parameter group to return details for.</li>
	 * 	<li><code>MaxRecords</code> - <code>integer</code> - Optional - The maximum number of records to include in the response. If more records exist than the specified <em>MaxRecords</em> value, a marker is included in the response so that the remaining results may be retrieved.</li>
	 * 	<li><code>Marker</code> - <code>string</code> - Optional - An optional marker provided in the previous DescribeCacheParameterGroups request. If this parameter is specified, the response includes only records beyond the marker, up to the value specified by <em>MaxRecords</em>.</li>
	 * 	<li><code>curlopts</code> - <code>array</code> - Optional - A set of values to pass directly into <code>curl_setopt()</code>, where the key is a pre-defined <code>CURLOPT_*</code> constant.</li>
	 * 	<li><code>returnCurlHandle</code> - <code>boolean</code> - Optional - A private toggle specifying that the cURL handle be returned rather than actually completing the request. This toggle is useful for manually managed batch requests.</li></ul>
	 * @return CFResponse A <CFResponse> object containing a parsed HTTP response.
	 */
	public function describe_cache_parameter_groups($opt = null)
	{
		if (!$opt) $opt = array();
				
		return $this->authenticate('DescribeCacheParameterGroups', $opt);
	}

	/**
	 * Returns the detailed parameter list for a particular CacheParameterGroup.
	 *
	 * @param string $cache_parameter_group_name (Required) The name of a specific cache parameter group to return details for.
	 * @param array $opt (Optional) An associative array of parameters that can have the following keys: <ul>
	 * 	<li><code>Source</code> - <code>string</code> - Optional - The parameter types to return. Valid values: <code>user</code> | <code>system</code> | <code>engine-default</code></li>
	 * 	<li><code>MaxRecords</code> - <code>integer</code> - Optional - The maximum number of records to include in the response. If more records exist than the specified <em>MaxRecords</em> value, a marker is included in the response so that the remaining results may be retrieved.</li>
	 * 	<li><code>Marker</code> - <code>string</code> - Optional - An optional marker provided in the previous DescribeCacheClusters request. If this parameter is specified, the response includes only records beyond the marker, up to the value specified by <em>MaxRecords</em>.</li>
	 * 	<li><code>curlopts</code> - <code>array</code> - Optional - A set of values to pass directly into <code>curl_setopt()</code>, where the key is a pre-defined <code>CURLOPT_*</code> constant.</li>
	 * 	<li><code>returnCurlHandle</code> - <code>boolean</code> - Optional - A private toggle specifying that the cURL handle be returned rather than actually completing the request. This toggle is useful for manually managed batch requests.</li></ul>
	 * @return CFResponse A <CFResponse> object containing a parsed HTTP response.
	 */
	public function describe_cache_parameters($cache_parameter_group_name, $opt = null)
	{
		if (!$opt) $opt = array();
		$opt['CacheParameterGroupName'] = $cache_parameter_group_name;
		
		return $this->authenticate('DescribeCacheParameters', $opt);
	}

	/**
	 * Returns a list of CacheSecurityGroup descriptions. If a CacheSecurityGroupName is specified,
	 * the list will contain only the description of the specified CacheSecurityGroup.
	 *
	 * @param array $opt (Optional) An associative array of parameters that can have the following keys: <ul>
	 * 	<li><code>CacheSecurityGroupName</code> - <code>string</code> - Optional - The name of the Cache Security Group to return details for.</li>
	 * 	<li><code>MaxRecords</code> - <code>integer</code> - Optional - The maximum number of records to include in the response. If more records exist than the specified <em>MaxRecords</em> value, a marker is included in the response so that the remaining results may be retrieved. Default: 100 Constraints: minimum 20, maximum 100</li>
	 * 	<li><code>Marker</code> - <code>string</code> - Optional - An optional marker provided in the previous DescribeCacheClusters request. If this parameter is specified, the response includes only records beyond the marker, up to the value specified by <em>MaxRecords</em>.</li>
	 * 	<li><code>curlopts</code> - <code>array</code> - Optional - A set of values to pass directly into <code>curl_setopt()</code>, where the key is a pre-defined <code>CURLOPT_*</code> constant.</li>
	 * 	<li><code>returnCurlHandle</code> - <code>boolean</code> - Optional - A private toggle specifying that the cURL handle be returned rather than actually completing the request. This toggle is useful for manually managed batch requests.</li></ul>
	 * @return CFResponse A <CFResponse> object containing a parsed HTTP response.
	 */
	public function describe_cache_security_groups($opt = null)
	{
		if (!$opt) $opt = array();
				
		return $this->authenticate('DescribeCacheSecurityGroups', $opt);
	}

	/**
	 * Returns the default engine and system parameter information for the specified cache engine.
	 *
	 * @param string $cache_parameter_group_family (Required) The name of the Cache Parameter Group Family. <p class="note">Currently, <em>memcached1.4</em> is the only cache parameter group family supported by the service.</p>
	 * @param array $opt (Optional) An associative array of parameters that can have the following keys: <ul>
	 * 	<li><code>MaxRecords</code> - <code>integer</code> - Optional - The maximum number of records to include in the response. If more records exist than the specified <em>MaxRecords</em> value, a marker is included in the response so that the remaining results may be retrieved.</li>
	 * 	<li><code>Marker</code> - <code>string</code> - Optional - An optional marker provided in the previous DescribeCacheClusters request. If this parameter is specified, the response includes only records beyond the marker, up to the value specified by <em>MaxRecords</em>.</li>
	 * 	<li><code>curlopts</code> - <code>array</code> - Optional - A set of values to pass directly into <code>curl_setopt()</code>, where the key is a pre-defined <code>CURLOPT_*</code> constant.</li>
	 * 	<li><code>returnCurlHandle</code> - <code>boolean</code> - Optional - A private toggle specifying that the cURL handle be returned rather than actually completing the request. This toggle is useful for manually managed batch requests.</li></ul>
	 * @return CFResponse A <CFResponse> object containing a parsed HTTP response.
	 */
	public function describe_engine_default_parameters($cache_parameter_group_family, $opt = null)
	{
		if (!$opt) $opt = array();
		$opt['CacheParameterGroupFamily'] = $cache_parameter_group_family;
		
		return $this->authenticate('DescribeEngineDefaultParameters', $opt);
	}

	/**
	 * Returns events related to Cache Clusters, Cache Security Groups, and Cache Parameter Groups for
	 * the past 14 days. Events specific to a particular Cache Cluster, Cache Security Group, or Cache
	 * Parameter Group can be obtained by providing the name as a parameter. By default, the past hour
	 * of events are returned.
	 *
	 * @param array $opt (Optional) An associative array of parameters that can have the following keys: <ul>
	 * 	<li><code>SourceIdentifier</code> - <code>string</code> - Optional - The identifier of the event source for which events will be returned. If not specified, then all sources are included in the response.</li>
	 * 	<li><code>SourceType</code> - <code>string</code> - Optional - The event source to retrieve events for. If no value is specified, all events are returned. [Allowed values: <code>cache-cluster</code>, <code>cache-parameter-group</code>, <code>cache-security-group</code>]</li>
	 * 	<li><code>StartTime</code> - <code>string</code> - Optional - The beginning of the time interval to retrieve events for, specified in ISO 8601 format. May be passed as a number of seconds since UNIX Epoch, or any string compatible with <php:strtotime()>.</li>
	 * 	<li><code>EndTime</code> - <code>string</code> - Optional - The end of the time interval for which to retrieve events, specified in ISO 8601 format. May be passed as a number of seconds since UNIX Epoch, or any string compatible with <php:strtotime()>.</li>
	 * 	<li><code>Duration</code> - <code>integer</code> - Optional - The number of minutes to retrieve events for.</li>
	 * 	<li><code>MaxRecords</code> - <code>integer</code> - Optional - The maximum number of records to include in the response. If more records exist than the specified <em>MaxRecords</em> value, a marker is included in the response so that the remaining results may be retrieved.</li>
	 * 	<li><code>Marker</code> - <code>string</code> - Optional - An optional marker provided in the previous DescribeCacheClusters request. If this parameter is specified, the response includes only records beyond the marker, up to the value specified by <em>MaxRecords</em>.</li>
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
	 * Modifies the Cache Cluster settings. You can change one or more Cache Cluster configuration
	 * parameters by specifying the parameters and the new values in the request.
	 *
	 * @param string $cache_cluster_id (Required) The Cache Cluster identifier. This value is stored as a lowercase string.
	 * @param array $opt (Optional) An associative array of parameters that can have the following keys: <ul>
	 * 	<li><code>NumCacheNodes</code> - <code>integer</code> - Optional - The number of Cache Nodes the Cache Cluster should have. If NumCacheNodes is greater than the existing number of Cache Nodes, Cache Nodes will be added. If NumCacheNodes is less than the existing number of Cache Nodes, Cache Nodes will be removed. When removing Cache Nodes, the Ids of the specific Cache Nodes to be removed must be supplied using the CacheNodeIdsToRemove parameter.</li>
	 * 	<li><code>CacheNodeIdsToRemove</code> - <code>string|array</code> - Optional - The list of Cache Node IDs to be removed. This parameter is only valid when NumCacheNodes is less than the existing number of Cache Nodes. The number of Cache Node Ids supplied in this parameter must match the difference between the existing number of Cache Nodes in the cluster and the new NumCacheNodes requested. Pass a string for a single value, or an indexed array for multiple values.</li>
	 * 	<li><code>CacheSecurityGroupNames</code> - <code>string|array</code> - Optional - A list of Cache Security Group Names to authorize on this Cache Cluster. This change is asynchronously applied as soon as possible. Constraints: Must contain no more than 255 alphanumeric characters. Must not be "Default". Pass a string for a single value, or an indexed array for multiple values.</li>
	 * 	<li><code>PreferredMaintenanceWindow</code> - <code>string</code> - Optional - The weekly time range (in UTC) during which system maintenance can occur, which may result in an outage. This change is made immediately. If moving this window to the current time, there must be at least 120 minutes between the current time and end of the window to ensure pending changes are applied.</li>
	 * 	<li><code>NotificationTopicArn</code> - <code>string</code> - Optional - The Amazon Resource Name (ARN) of the SNS topic to which notifications will be sent. <p class="note">The SNS topic owner must be same as the Cache Cluster owner.</p></li>
	 * 	<li><code>CacheParameterGroupName</code> - <code>string</code> - Optional - The name of the Cache Parameter Group to apply to this Cache Cluster. This change is asynchronously applied as soon as possible for parameters when the <em>ApplyImmediately</em> parameter is specified as <em>true</em> for this request.</li>
	 * 	<li><code>NotificationTopicStatus</code> - <code>string</code> - Optional - The status of the Amazon SNS notification topic. The value can be <em>active</em> or <em>inactive</em>. Notifications are sent only if the status is <em>active</em>.</li>
	 * 	<li><code>ApplyImmediately</code> - <code>boolean</code> - Optional - Specifies whether or not the modifications in this request and any pending modifications are asynchronously applied as soon as possible, regardless of the <em>PreferredMaintenanceWindow</em> setting for the Cache Cluster. If this parameter is passed as <code>false</code>, changes to the Cache Cluster are applied on the next maintenance reboot, or the next failure reboot, whichever occurs first. Default: <code>false</code></li>
	 * 	<li><code>EngineVersion</code> - <code>string</code> - Optional - The version of the cache engine to upgrade this cluster to.</li>
	 * 	<li><code>AutoMinorVersionUpgrade</code> - <code>boolean</code> - Optional - Indicates that minor engine upgrades will be applied automatically to the Cache Cluster during the maintenance window. Default: <code>true</code></li>
	 * 	<li><code>curlopts</code> - <code>array</code> - Optional - A set of values to pass directly into <code>curl_setopt()</code>, where the key is a pre-defined <code>CURLOPT_*</code> constant.</li>
	 * 	<li><code>returnCurlHandle</code> - <code>boolean</code> - Optional - A private toggle specifying that the cURL handle be returned rather than actually completing the request. This toggle is useful for manually managed batch requests.</li></ul>
	 * @return CFResponse A <CFResponse> object containing a parsed HTTP response.
	 */
	public function modify_cache_cluster($cache_cluster_id, $opt = null)
	{
		if (!$opt) $opt = array();
		$opt['CacheClusterId'] = $cache_cluster_id;
		
		// Optional list (non-map)
		if (isset($opt['CacheNodeIdsToRemove']))
		{
			$opt = array_merge($opt, CFComplexType::map(array(
				'CacheNodeIdsToRemove' => (is_array($opt['CacheNodeIdsToRemove']) ? $opt['CacheNodeIdsToRemove'] : array($opt['CacheNodeIdsToRemove']))
			), 'member'));
			unset($opt['CacheNodeIdsToRemove']);
		}
		
		// Optional list (non-map)
		if (isset($opt['CacheSecurityGroupNames']))
		{
			$opt = array_merge($opt, CFComplexType::map(array(
				'CacheSecurityGroupNames' => (is_array($opt['CacheSecurityGroupNames']) ? $opt['CacheSecurityGroupNames'] : array($opt['CacheSecurityGroupNames']))
			), 'member'));
			unset($opt['CacheSecurityGroupNames']);
		}

		return $this->authenticate('ModifyCacheCluster', $opt);
	}

	/**
	 * Modifies the parameters of a CacheParameterGroup. To modify more than one parameter, submit a
	 * list of ParameterName and ParameterValue parameters. A maximum of 20 parameters can be modified
	 * in a single request.
	 *
	 * @param string $cache_parameter_group_name (Required) The name of the cache parameter group to modify.
	 * @param array $parameter_name_values (Required) An array of parameter names and values for the parameter update. At least one parameter name and value must be supplied; subsequent arguments are optional. A maximum of 20 parameters may be modified in a single request. <ul>
	 * 	<li><code>x</code> - <code>array</code> - Optional - This represents a simple array index. <ul>
	 * 		<li><code>ParameterName</code> - <code>string</code> - Optional - Specifies the name of the parameter.</li>
	 * 		<li><code>ParameterValue</code> - <code>string</code> - Optional - Specifies the value of the parameter.</li>
	 * 	</ul></li>
	 * </ul>
	 * @param array $opt (Optional) An associative array of parameters that can have the following keys: <ul>
	 * 	<li><code>curlopts</code> - <code>array</code> - Optional - A set of values to pass directly into <code>curl_setopt()</code>, where the key is a pre-defined <code>CURLOPT_*</code> constant.</li>
	 * 	<li><code>returnCurlHandle</code> - <code>boolean</code> - Optional - A private toggle specifying that the cURL handle be returned rather than actually completing the request. This toggle is useful for manually managed batch requests.</li></ul>
	 * @return CFResponse A <CFResponse> object containing a parsed HTTP response.
	 */
	public function modify_cache_parameter_group($cache_parameter_group_name, $parameter_name_values, $opt = null)
	{
		if (!$opt) $opt = array();
		$opt['CacheParameterGroupName'] = $cache_parameter_group_name;
		
		// Required list + map
		$opt = array_merge($opt, CFComplexType::map(array(
			'ParameterNameValues' => (is_array($parameter_name_values) ? $parameter_name_values : array($parameter_name_values))
		), 'member'));

		return $this->authenticate('ModifyCacheParameterGroup', $opt);
	}

	/**
	 * Reboots some (or all) of the cache cluster nodes within a previously provisioned ElastiCache
	 * cluster. This API results in the application of modified CacheParameterGroup parameters to the
	 * cache cluster. This action is taken as soon as possible, and results in a momentary outage to
	 * the cache cluster during which the cache cluster status is set to rebooting. During that
	 * momentary outage, the contents of the cache (for each cache cluster node being rebooted) are
	 * lost. A CacheCluster event is created when the reboot is completed.
	 *
	 * @param string $cache_cluster_id (Required) The Cache Cluster identifier. This parameter is stored as a lowercase string.
	 * @param string|array $cache_node_ids_to_reboot (Required) A list of Cache Cluster Node Ids to reboot. To reboot an entire cache cluster, specify all cache cluster node Ids. Pass a string for a single value, or an indexed array for multiple values.
	 * @param array $opt (Optional) An associative array of parameters that can have the following keys: <ul>
	 * 	<li><code>curlopts</code> - <code>array</code> - Optional - A set of values to pass directly into <code>curl_setopt()</code>, where the key is a pre-defined <code>CURLOPT_*</code> constant.</li>
	 * 	<li><code>returnCurlHandle</code> - <code>boolean</code> - Optional - A private toggle specifying that the cURL handle be returned rather than actually completing the request. This toggle is useful for manually managed batch requests.</li></ul>
	 * @return CFResponse A <CFResponse> object containing a parsed HTTP response.
	 */
	public function reboot_cache_cluster($cache_cluster_id, $cache_node_ids_to_reboot, $opt = null)
	{
		if (!$opt) $opt = array();
		$opt['CacheClusterId'] = $cache_cluster_id;
		
		// Required list (non-map)
		$opt = array_merge($opt, CFComplexType::map(array(
			'CacheNodeIdsToReboot' => (is_array($cache_node_ids_to_reboot) ? $cache_node_ids_to_reboot : array($cache_node_ids_to_reboot))
		), 'member'));

		return $this->authenticate('RebootCacheCluster', $opt);
	}

	/**
	 * Modifies the parameters of a CacheParameterGroup to the engine or system default value. To
	 * reset specific parameters submit a list of the parameter names. To reset the entire
	 * CacheParameterGroup, specify the CacheParameterGroup name and ResetAllParameters parameters.
	 *
	 * @param string $cache_parameter_group_name (Required) The name of the Cache Parameter Group.
	 * @param array $parameter_name_values (Required) An array of parameter names which should be reset. If not resetting the entire CacheParameterGroup, at least one parameter name must be supplied. <ul>
	 * 	<li><code>x</code> - <code>array</code> - Optional - This represents a simple array index. <ul>
	 * 		<li><code>ParameterName</code> - <code>string</code> - Optional - Specifies the name of the parameter.</li>
	 * 		<li><code>ParameterValue</code> - <code>string</code> - Optional - Specifies the value of the parameter.</li>
	 * 	</ul></li>
	 * </ul>
	 * @param array $opt (Optional) An associative array of parameters that can have the following keys: <ul>
	 * 	<li><code>ResetAllParameters</code> - <code>boolean</code> - Optional - Specifies whether (<em>true</em>) or not (<em>false</em>) to reset all parameters in the Cache Parameter Group to default values.</li>
	 * 	<li><code>curlopts</code> - <code>array</code> - Optional - A set of values to pass directly into <code>curl_setopt()</code>, where the key is a pre-defined <code>CURLOPT_*</code> constant.</li>
	 * 	<li><code>returnCurlHandle</code> - <code>boolean</code> - Optional - A private toggle specifying that the cURL handle be returned rather than actually completing the request. This toggle is useful for manually managed batch requests.</li></ul>
	 * @return CFResponse A <CFResponse> object containing a parsed HTTP response.
	 */
	public function reset_cache_parameter_group($cache_parameter_group_name, $parameter_name_values, $opt = null)
	{
		if (!$opt) $opt = array();
		$opt['CacheParameterGroupName'] = $cache_parameter_group_name;
		
		// Required list + map
		$opt = array_merge($opt, CFComplexType::map(array(
			'ParameterNameValues' => (is_array($parameter_name_values) ? $parameter_name_values : array($parameter_name_values))
		), 'member'));

		return $this->authenticate('ResetCacheParameterGroup', $opt);
	}

	/**
	 * Revokes ingress from a CacheSecurityGroup for previously authorized EC2 Security Groups.
	 *
	 * @param string $cache_security_group_name (Required) The name of the Cache Security Group to revoke ingress from.
	 * @param string $ec2_security_group_name (Required) The name of the EC2 Security Group to revoke access from.
	 * @param string $ec2_security_group_owner_id (Required) The AWS Account Number of the owner of the security group specified in the <em>EC2SecurityGroupName</em> parameter. The AWS Access Key ID is not an acceptable value.
	 * @param array $opt (Optional) An associative array of parameters that can have the following keys: <ul>
	 * 	<li><code>curlopts</code> - <code>array</code> - Optional - A set of values to pass directly into <code>curl_setopt()</code>, where the key is a pre-defined <code>CURLOPT_*</code> constant.</li>
	 * 	<li><code>returnCurlHandle</code> - <code>boolean</code> - Optional - A private toggle specifying that the cURL handle be returned rather than actually completing the request. This toggle is useful for manually managed batch requests.</li></ul>
	 * @return CFResponse A <CFResponse> object containing a parsed HTTP response.
	 */
	public function revoke_cache_security_group_ingress($cache_security_group_name, $ec2_security_group_name, $ec2_security_group_owner_id, $opt = null)
	{
		if (!$opt) $opt = array();
		$opt['CacheSecurityGroupName'] = $cache_security_group_name;
		$opt['EC2SecurityGroupName'] = $ec2_security_group_name;
		$opt['EC2SecurityGroupOwnerId'] = $ec2_security_group_owner_id;
		
		return $this->authenticate('RevokeCacheSecurityGroupIngress', $opt);
	}
}


/*%******************************************************************************************%*/
// EXCEPTIONS

class ElastiCache_Exception extends Exception {}
