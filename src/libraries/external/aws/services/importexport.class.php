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
 * AWS Import/Export accelerates transferring large amounts of data between the AWS cloud and
 * portable storage devices that you mail to us. AWS Import/Export transfers data directly onto
 * and off of your storage devices using Amazon's high-speed internal network and bypassing the
 * Internet. For large data sets, AWS Import/Export is often faster than Internet transfer and
 * more cost effective than upgrading your connectivity.
 *
 * @version 2012.01.16
 * @license See the included NOTICE.md file for complete information.
 * @copyright See the included NOTICE.md file for complete information.
 * @link http://aws.amazon.com/importexport/ AWS Import/Export
 * @link http://aws.amazon.com/importexport/documentation/ AWS Import/Export documentation
 */
class AmazonImportExport extends CFRuntime
{
	/*%******************************************************************************************%*/
	// CLASS CONSTANTS

	/**
	 * Specify the queue URL for the United States East (Northern Virginia) Region.
	 */
	const REGION_US_E1 = 'importexport.amazonaws.com';

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
	 * Constructs a new instance of <AmazonImportExport>.
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
		$this->api_version = '2010-06-01';
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
	 * This operation cancels a specified job. Only the job owner can cancel it. The operation fails
	 * if the job has already started or is complete.
	 *
	 * @param string $job_id (Required) A unique identifier which refers to a particular job.
	 * @param array $opt (Optional) An associative array of parameters that can have the following keys: <ul>
	 * 	<li><code>curlopts</code> - <code>array</code> - Optional - A set of values to pass directly into <code>curl_setopt()</code>, where the key is a pre-defined <code>CURLOPT_*</code> constant.</li>
	 * 	<li><code>returnCurlHandle</code> - <code>boolean</code> - Optional - A private toggle specifying that the cURL handle be returned rather than actually completing the request. This toggle is useful for manually managed batch requests.</li></ul>
	 * @return CFResponse A <CFResponse> object containing a parsed HTTP response.
	 */
	public function cancel_job($job_id, $opt = null)
	{
		if (!$opt) $opt = array();
		$opt['JobId'] = $job_id;
		
		return $this->authenticate('CancelJob', $opt);
	}

	/**
	 * This operation initiates the process of scheduling an upload or download of your data. You
	 * include in the request a manifest that describes the data transfer specifics. The response to
	 * the request includes a job ID, which you can use in other operations, a signature that you use
	 * to identify your storage device, and the address where you should ship your storage device.
	 *
	 * @param string $job_type (Required) Specifies whether the job to initiate is an import or export job. [Allowed values: <code>Import</code>, <code>Export</code>]
	 * @param string $manifest (Required) The UTF-8 encoded text of the manifest file.
	 * @param boolean $validate_only (Required) Validate the manifest and parameter values in the request but do not actually create a job.
	 * @param array $opt (Optional) An associative array of parameters that can have the following keys: <ul>
	 * 	<li><code>ManifestAddendum</code> - <code>string</code> - Optional - For internal use only.</li>
	 * 	<li><code>curlopts</code> - <code>array</code> - Optional - A set of values to pass directly into <code>curl_setopt()</code>, where the key is a pre-defined <code>CURLOPT_*</code> constant.</li>
	 * 	<li><code>returnCurlHandle</code> - <code>boolean</code> - Optional - A private toggle specifying that the cURL handle be returned rather than actually completing the request. This toggle is useful for manually managed batch requests.</li></ul>
	 * @return CFResponse A <CFResponse> object containing a parsed HTTP response.
	 */
	public function create_job($job_type, $manifest, $validate_only, $opt = null)
	{
		if (!$opt) $opt = array();
		$opt['JobType'] = $job_type;
		$opt['Manifest'] = $manifest;
		$opt['ValidateOnly'] = $validate_only;
		
		return $this->authenticate('CreateJob', $opt);
	}

	/**
	 * This operation returns information about a job, including where the job is in the processing
	 * pipeline, the status of the results, and the signature value associated with the job. You can
	 * only return information about jobs you own.
	 *
	 * @param string $job_id (Required) A unique identifier which refers to a particular job.
	 * @param array $opt (Optional) An associative array of parameters that can have the following keys: <ul>
	 * 	<li><code>curlopts</code> - <code>array</code> - Optional - A set of values to pass directly into <code>curl_setopt()</code>, where the key is a pre-defined <code>CURLOPT_*</code> constant.</li>
	 * 	<li><code>returnCurlHandle</code> - <code>boolean</code> - Optional - A private toggle specifying that the cURL handle be returned rather than actually completing the request. This toggle is useful for manually managed batch requests.</li></ul>
	 * @return CFResponse A <CFResponse> object containing a parsed HTTP response.
	 */
	public function get_status($job_id, $opt = null)
	{
		if (!$opt) $opt = array();
		$opt['JobId'] = $job_id;
		
		return $this->authenticate('GetStatus', $opt);
	}

	/**
	 * This operation returns the jobs associated with the requester. AWS Import/Export lists the jobs
	 * in reverse chronological order based on the date of creation. For example if Job Test1 was
	 * created 2009Dec30 and Test2 was created 2010Feb05, the ListJobs operation would return Test2
	 * followed by Test1.
	 *
	 * @param array $opt (Optional) An associative array of parameters that can have the following keys: <ul>
	 * 	<li><code>MaxJobs</code> - <code>integer</code> - Optional - Sets the maximum number of jobs returned in the response. If there are additional jobs that were not returned because MaxJobs was exceeded, the response contains<IsTruncated>true</IsTruncated>. To return the additional jobs, see Marker.</li>
	 * 	<li><code>Marker</code> - <code>string</code> - Optional - Specifies the JOBID to start after when listing the jobs created with your account. AWS Import/Export lists your jobs in reverse chronological order. See MaxJobs.</li>
	 * 	<li><code>curlopts</code> - <code>array</code> - Optional - A set of values to pass directly into <code>curl_setopt()</code>, where the key is a pre-defined <code>CURLOPT_*</code> constant.</li>
	 * 	<li><code>returnCurlHandle</code> - <code>boolean</code> - Optional - A private toggle specifying that the cURL handle be returned rather than actually completing the request. This toggle is useful for manually managed batch requests.</li></ul>
	 * @return CFResponse A <CFResponse> object containing a parsed HTTP response.
	 */
	public function list_jobs($opt = null)
	{
		if (!$opt) $opt = array();
				
		return $this->authenticate('ListJobs', $opt);
	}

	/**
	 * You use this operation to change the parameters specified in the original manifest file by
	 * supplying a new manifest file. The manifest file attached to this request replaces the original
	 * manifest file. You can only use the operation after a CreateJob request but before the data
	 * transfer starts and you can only use it on jobs you own.
	 *
	 * @param string $job_id (Required) A unique identifier which refers to a particular job.
	 * @param string $manifest (Required) The UTF-8 encoded text of the manifest file.
	 * @param string $job_type (Required) Specifies whether the job to initiate is an import or export job. [Allowed values: <code>Import</code>, <code>Export</code>]
	 * @param boolean $validate_only (Required) Validate the manifest and parameter values in the request but do not actually create a job.
	 * @param array $opt (Optional) An associative array of parameters that can have the following keys: <ul>
	 * 	<li><code>curlopts</code> - <code>array</code> - Optional - A set of values to pass directly into <code>curl_setopt()</code>, where the key is a pre-defined <code>CURLOPT_*</code> constant.</li>
	 * 	<li><code>returnCurlHandle</code> - <code>boolean</code> - Optional - A private toggle specifying that the cURL handle be returned rather than actually completing the request. This toggle is useful for manually managed batch requests.</li></ul>
	 * @return CFResponse A <CFResponse> object containing a parsed HTTP response.
	 */
	public function update_job($job_id, $manifest, $job_type, $validate_only, $opt = null)
	{
		if (!$opt) $opt = array();
		$opt['JobId'] = $job_id;
		$opt['Manifest'] = $manifest;
		$opt['JobType'] = $job_type;
		$opt['ValidateOnly'] = $validate_only;
		
		return $this->authenticate('UpdateJob', $opt);
	}
}


/*%******************************************************************************************%*/
// EXCEPTIONS

class ImportExport_Exception extends Exception {}
