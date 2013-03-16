<?php
/**
 * AppDotNet.php
 * App.net PHP library
 * https://github.com/jdolitsky/AppDotNetPHP
 *
 * This class handles a lower level type of access to App.net. It's ideal
 * for command line scripts and other places where you want full control
 * over what's happening, and you're at least a little familiar with oAuth.
 *
 * Alternatively you can use the EZAppDotNet class which automatically takes
 * care of a lot of the details like logging in, keeping track of tokens,
 * etc. EZAppDotNet assumes you're accessing App.net via a browser, whereas
 * this class tries to make no assumptions at all.
 */
class AppDotNet {

	protected $_baseUrl = 'https://alpha-api.app.net/stream/0/';
	protected $_authUrl = 'https://account.app.net/oauth/';

	private $_authPostParams=array();

	// stores the access token after login
	private $_accessToken = null;

	// stores the user ID returned when fetching the auth token
	private $_user_id = null;

	// stores the username returned when fetching the auth token
	private $_username = null;

	// The total number of requests you're allowed within the alloted time period
	private $_rateLimit = null;

	// The number of requests you have remaining within the alloted time period
	private $_rateLimitRemaining = null;

	// The number of seconds remaining in the alloted time period
	private $_rateLimitReset = null;

	// The scope the user has
	private $_scope = null;

	// token scopes
	private $_scopes=array();

	// debug info
	private $_last_request = null;
	private $_last_response = null;

	// ssl certification
	private $_sslCA = null;

	// the callback function to be called when an event is received from the stream
	private $_streamCallback = null;

	// the stream buffer
	private $_streamBuffer = '';

	// stores the curl handler for the current stream
	private $_currentStream = null;

	// stores the curl multi handler for the current stream
	private $_multiStream = null;

	// stores the number of failed connects, so we can back off multiple failures
	private $_connectFailCounter = 0;

	// stores the most recent stream url, so we can re-connect when needed
	private $_streamUrl = null;

	// keeps track of the last time we've received a packet from the api, if it's too long we'll reconnect
	private $_lastStreamActivity = null;

	// stores the headers received when connecting to the stream
	private $_streamHeaders = null;

	// response meta max_id data
	private $_maxid = null;

	// response meta min_id data
	private $_minid = null;

	// response meta more data
	private $_more = null;

	// response stream marker data
	private $_last_marker = null;

	// strip envelope response from returned value
	private $_stripResponseEnvelope=true;
	/**
	 * Constructs an AppDotNet PHP object with the specified client ID and
	 * client secret.
	 * @param string $client_id The client ID you received from App.net when
	 * creating your app.
	 * @param string $client_secret The client secret you received from
	 * App.net when creating your app.
	 */
	public function __construct($client_id,$client_secret) {
		$this->_clientId = $client_id;
		$this->_clientSecret = $client_secret;

		// if the digicert certificate exists in the same folder as this file,
		// remember that fact for later
		if (file_exists(dirname(__FILE__).'/DigiCertHighAssuranceEVRootCA.pem')) {
			$this->_sslCA = dirname(__FILE__).'/DigiCertHighAssuranceEVRootCA.pem';
		}
	}

	/**
	 * Set whether or not to strip Envelopse Response (meta) information
	 * This option will be deprecated in the future. Is it to allow
	 * a stepped migration path between code expecting the old behavior
	 * and new behavior. When not stripped, you still can use the proper
	 * method to pull the meta information. Please start converting your code ASAP
	 */
	public function includeResponseEnvelope() {
		$this->_stripResponseEnvelope=false;
	}

	/**
	 * Construct the proper Auth URL for the user to visit and either grant
	 * or not access to your app. Usually you would place this as a link for
	 * the user to client, or a redirect to send them to the auth URL.
	 * Also can be called after authentication for additional scopes
	 * @param string $callbackUri Where you want the user to be directed
	 * after authenticating with App.net. This must be one of the URIs
	 * allowed by your App.net application settings.
	 * @param array $scope An array of scopes (permissions) you wish to obtain
	 * from the user. Currently options are stream, email, write_post, follow,
	 * messages, and export. If you don't specify anything, you'll only receive
	 * access to the user's basic profile (the default).
	 */
	public function getAuthUrl($callback_uri,$scope=null) {

		// construct an authorization url based on our client id and other data
		$data = array(
			'client_id'=>$this->_clientId,
			'response_type'=>'code',
			'redirect_uri'=>$callback_uri,
		);

		$url = $this->_authUrl;
		if ($this->_accessToken) {
			$url .= 'authorize?';
		} else {
			$url .= 'authenticate?';
		}
		$url .= $this->buildQueryString($data);

		if ($scope) {
			$url .= '&scope='.implode('+',$scope);
		}

		// return the constructed url
		return $url;
	}

	/**
	 * Call this after they return from the auth page, or anytime you need the
	 * token. For example, you could store it in a database and use
	 * setAccessToken() later on to return on behalf of the user.
	 */
	public function getAccessToken($callback_uri) {
		// if there's no access token set, and they're returning from
		// the auth page with a code, use the code to get a token
		if (!$this->_accessToken && isset($_GET['code']) && $_GET['code']) {

			// construct the necessary elements to get a token
			$data = array(
				'client_id'=>$this->_clientId,
				'client_secret'=>$this->_clientSecret,
				'grant_type'=>'authorization_code',
				'redirect_uri'=>$callback_uri,
				'code'=>$_GET['code']
			);

			// try and fetch the token with the above data
			$res = $this->httpReq('post',$this->_authUrl.'access_token', $data);

			// store it for later
			$this->_accessToken = $res['access_token'];
			$this->_username = $res['username'];
			$this->_user_id = $res['user_id'];
		}

		// return what we have (this may be a token, or it may be nothing)
		return $this->_accessToken;
	}

  /**
   * Check the scope of current token to see if it has required scopes
   * has to be done after a check
   */
  public function checkScopes($app_scopes) {
    if (!count($this->_scopes)) {
      return -1; // _scope is empty
    }
    $missing=array();
    foreach($app_scopes as $scope) {
      if (!in_array($scope,$this->_scopes)) {
        if ($scope=='public_messages') {
          // messages works for public_messages
          if (in_array('messages',$this->_scopes)) {
            // if we have messages in our scopes
            continue;
          }
        }
        $missing[]=$scope;
      }
    }
    // identify the ones missing
    if (count($missing)) {
      // do something
      return $missing;
    }
    return 0; // 0 missing
  }

	/**
	 * Set the access token (eg: after retrieving it from offline storage)
	 * @param string $token A valid access token you're previously received
	 * from calling getAccessToken().
	 */
	public function setAccessToken($token) {
		$this->_accessToken = $token;
	}

	/**
	 * Retrieve an app access token from the app.net API. This allows you
	 * to access the API without going through the user access flow if you
	 * just want to (eg) consume global. App access tokens are required for
	 * some actions (like streaming global). DO NOT share the return value
	 * of this function with any user (or save it in a cookie, etc). This
	 * is considered secret info for your app only.
	 * @return string The app access token
	 */
	public function getAppAccessToken() {

		// construct the necessary elements to get a token
		$data = array(
			'client_id'=>$this->_clientId,
			'client_secret'=>$this->_clientSecret,
			'grant_type'=>'client_credentials',
		);

		// try and fetch the token with the above data
		$res = $this->httpReq('post',$this->_authUrl.'access_token', $data);

		// store it for later
		$this->_accessToken = $res['access_token'];
		$this->_username = null;
		$this->_user_id = null;

		return $this->_accessToken;
	}

	/**
	 * Returns the total number of requests you're allowed within the
	 * alloted time period.
	 * @see getRateLimitReset()
	 */
	public function getRateLimit() {
		return $this->_rateLimit;
	}

	/**
	 * The number of requests you have remaining within the alloted time period
	 * @see getRateLimitReset()
	 */
	public function getRateLimitRemaining() {
		return $this->_rateLimitRemaining;
	}

	/**
	 * The number of seconds remaining in the alloted time period.
	 * When this time is up you'll have getRateLimit() available again.
	 */
	public function getRateLimitReset() {
		return $this->$_rateLimitReset;
	}

	/**
	 * The scope the user has
	 */
	public function getScope() {
		return $this->_scope;
	}

	/**
	 * Internal function, parses out important information App.net adds
	 * to the headers.
	 */
	protected function parseHeaders($response) {
		// take out the headers
		// set internal variables
		// return the body/content
		$this->_rateLimit = null;
		$this->_rateLimitRemaining = null;
		$this->_rateLimitReset = null;
		$this->_scope = null;

		$response = explode("\r\n\r\n",$response,2);
		$headers = $response[0];

                if($headers == 'HTTP/1.1 100 Continue') {
                        $response = explode("\r\n\r\n",$response[1],2);
                        $headers = $response[0];
                }

		if (isset($response[1])) {
			$content = $response[1];
		}
		else {
			$content = null;
		}

		// this is not a good way to parse http headers
		// it will not (for example) take into account multiline headers
		// but what we're looking for is pretty basic, so we can ignore those shortcomings
		$headers = explode("\r\n",$headers);
		foreach ($headers as $header) {
			$header = explode(': ',$header,2);
			if (count($header)<2) {
				continue;
			}
			list($k,$v) = $header;
			switch ($k) {
				case 'X-RateLimit-Remaining':
					$this->_rateLimitRemaining = $v;
					break;
				case 'X-RateLimit-Limit':
					$this->_rateLimit = $v;
					break;
				case 'X-RateLimit-Reset':
					$this->_rateLimitReset = $v;
					break;
				case 'X-OAuth-Scopes':
					$this->_scope = $v;
					$this->_scopes=explode(',',$v);
					break;
			}
		}
		return $content;
	}

	/**
	 * Internal function. Used to turn things like TRUE into 1, and then
	 * calls http_build_query.
	 */
	protected function buildQueryString($array) {
		foreach ($array as $k=>&$v) {
			if ($v===true) {
				$v = '1';
			}
			elseif ($v===false) {
				$v = '0';
			}
			unset($v);
		}
		return http_build_query($array);
	}


	/**
	 * Internal function to handle all
	 * HTTP requests (POST,PUT,GET,DELETE)
	 */
	protected function httpReq($act, $req, $params=array(),$contentType='application/x-www-form-urlencoded') {
		$ch = curl_init($req);
		$headers = array();
		if($act != 'get') {
			curl_setopt($ch, CURLOPT_POST, true);
			// if they passed an array, build a list of parameters from it
			if (is_array($params) && $act != 'post-raw') {
				$params = $this->buildQueryString($params);
			}
			curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
			$headers[] = "Content-Type: ".$contentType;
		}
		if($act != 'post' && $act != 'post-raw') {
			curl_setopt($ch, CURLOPT_CUSTOMREQUEST, strtoupper($act));
		}
		if($act == 'get' && isset($params['access_token'])) {
			$headers[] = 'Authorization: Bearer '.$params['access_token'];
		}
		else if ($this->_accessToken) {
			$headers[] = 'Authorization: Bearer '.$this->_accessToken;
		}
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLINFO_HEADER_OUT, true);
		curl_setopt($ch, CURLOPT_HEADER, true);
		if ($this->_sslCA) {
			curl_setopt($ch, CURLOPT_CAINFO, $this->_sslCA);
		}
		$this->_last_response = curl_exec($ch);
		$this->_last_request = curl_getinfo($ch,CURLINFO_HEADER_OUT);
		$http_status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		curl_close($ch);
		if ($http_status==0) {
			throw new AppDotNetException('Unable to connect to '.$req);
		}
		if ($http_status<200 || $http_status>=300) {
			throw new AppDotNetException('HTTP error '.$this->_last_response);
		}
		if ($this->_last_request===false) {
			if (!curl_getinfo($ch,CURLINFO_SSL_VERIFYRESULT)) {
				throw new AppDotNetException('SSL verification failed, connection terminated.');
			}
		}
		$response = $this->parseHeaders($this->_last_response);
		$response = json_decode($response,true);

		if (isset($response['meta'])) {
			if (isset($response['meta']['max_id'])) {
				$this->_maxid=$response['meta']['max_id'];
				$this->_minid=$response['meta']['min_id'];
			}
			if (isset($response['meta']['more'])) {
				$this->_more=$response['meta']['more'];
			}
			if (isset($response['meta']['marker'])) {
				$this->_last_marker=$response['meta']['marker'];
			}
		}

		// look for errors
		if (isset($response['error'])) {
			if (is_array($response['error'])) {
				throw new AppDotNetException($response['error']['message'],
								$response['error']['code']);
			}
			else {
				throw new AppDotNetException($response['error']);
			}
		}

		// look for response migration errors
		elseif (isset($response['meta']) && isset($response['meta']['error_message'])) {
			throw new AppDotNetException($response['meta']['error_message'],$response['meta']['code']);
		}

		// if we've received a migration response, handle it and return data only
		elseif ($this->_stripResponseEnvelope && isset($response['meta']) && isset($response['data'])) {
			return $response['data'];
		}

		// else non response migration response, just return it
		else {
			return $response;
		}
	}


	/**
	 * Get max_id from last meta response data envelope
	 */
	public function getResponseMaxID() {
		return $this->_maxid;
	}

	/**
	 * Get min_id from last meta response data envelope
	 */
	public function getResponseMinID() {
		return $this->_minid;
	}

	/**
	 * Get more from last meta response data envelope
	 */
	public function getResponseMore() {
		return $this->_more;
	}

	/**
	 * Get marker from last meta response data envelope
	 */
	public function getResponseMarker() {
		return $this->_last_marker;
	}

	/**
	 * Return the Filters for the current user.
	 */
	public function getAllFilters() {
		return $this->httpReq('get',$this->_baseUrl.'filters');
	}

	/**
	 * Create a Filter for the current user.
	 * @param string $name The name of the new filter
	 * @param array $filters An associative array of filters to be applied.
	 * This may change as the API evolves, as of this writing possible
	 * values are: user_ids, hashtags, link_domains, and mention_user_ids.
	 * You will need to provide at least one filter name=>value pair.
	 */
	public function createFilter($name='New filter', $filters=array()) {
		$filters['name'] = $name;
		return $this->httpReq('post',$this->_baseUrl.'filters',$filters);
	}

	/**
	 * Returns a specific Filter object.
	 * @param integer $filter_id The ID of the filter you wish to retrieve.
	 */
	public function getFilter($filter_id=null) {
		return $this->httpReq('get',$this->_baseUrl.'filters/'.urlencode($filter_id));
	}

	/**
	 * Delete a Filter. The Filter must belong to the current User.
	 * @return object Returns the deleted Filter on success.
	 */
	public function deleteFilter($filter_id=null) {
		return $this->httpReq('delete',$this->_baseUrl.'filters/'.urlencode($filter_id));
	}

	/**
	 * Create a new Post object. Mentions and hashtags will be parsed out of the
	 * post text, as will bare URLs. To create a link in a post without using a
	 * bare URL, include the anchor text in the post's text and include a link
	 * entity in the post creation call.
	 * @param string $text The text of the post
	 * @param array $data An associative array of optional post data. This
	 * will likely change as the API evolves, as of this writing allowed keys are:
	 * reply_to, and annotations. "annotations" may be a complex object represented
	 * by an associative array.
	 * @param array $params An associative array of optional data to be included
         * in the URL (such as 'include_annotations' and 'include_machine')
	 * @return array An associative array representing the post.
	 */
	public function createPost($text=null, $data = array(), $params = array()) {
		$data['text'] = $text;
		$json = json_encode($data);
		$qs = '';
		if (!empty($params)) {
			$qs = '?'.$this->buildQueryString($params);
		}
		return $this->httpReq('post',$this->_baseUrl.'posts'.$qs, $json, 'application/json');
	}

	/**
	 * Returns a specific Post.
	 * @param integer $post_id The ID of the post to retrieve
	 * @param array $params An associative array of optional general parameters.
	 * This will likely change as the API evolves, as of this writing allowed keys
	 * are: include_annotations.
	 * @return array An associative array representing the post
	 */
	public function getPost($post_id=null,$params = array()) {
		return $this->httpReq('get',$this->_baseUrl.'posts/'.urlencode($post_id)
						.'?'.$this->buildQueryString($params));
	}

	/**
	 * Delete a Post. The current user must be the same user who created the Post.
	 * It returns the deleted Post on success.
	 * @param integer $post_id The ID of the post to delete
	 * @param array An associative array representing the post that was deleted
	 */
	public function deletePost($post_id=null) {
		return $this->httpReq('delete',$this->_baseUrl.'posts/'.urlencode($post_id));
	}

	/**
	 * Retrieve the Posts that are 'in reply to' a specific Post.
	 * @param integer $post_id The ID of the post you want to retrieve replies for.
	 * @param array $params An associative array of optional general parameters.
	 * This will likely change as the API evolves, as of this writing allowed keys
	 * are:	count, before_id, since_id, include_muted, include_deleted,
	 * include_directed_posts, and include_annotations.
	 * @return An array of associative arrays, each representing a single post.
	 */
	public function getPostReplies($post_id=null,$params = array()) {
		return $this->httpReq('get',$this->_baseUrl.'posts/'.urlencode($post_id)
				.'/replies?'.$this->buildQueryString($params));
	}

	/**
	 * Get the most recent Posts created by a specific User in reverse
	 * chronological order (most recent first).
	 * @param mixed $user_id Either the ID of the user you wish to retrieve posts by,
	 * or the string "me", which will retrieve posts for the user you're authenticated
	 * as.
	 * @param array $params An associative array of optional general parameters.
	 * This will likely change as the API evolves, as of this writing allowed keys
	 * are:	count, before_id, since_id, include_muted, include_deleted,
	 * include_directed_posts, and include_annotations.
	 * @return An array of associative arrays, each representing a single post.
	 */
	public function getUserPosts($user_id='me', $params = array()) {
		return $this->httpReq('get',$this->_baseUrl.'users/'.urlencode($user_id)
					.'/posts?'.$this->buildQueryString($params));
	}

	/**
	 * Get the most recent Posts mentioning by a specific User in reverse
	 * chronological order (newest first).
	 * @param mixed $user_id Either the ID of the user who is being mentioned, or
	 * the string "me", which will retrieve posts for the user you're authenticated
	 * as.
	 * @param array $params An associative array of optional general parameters.
	 * This will likely change as the API evolves, as of this writing allowed keys
	 * are:	count, before_id, since_id, include_muted, include_deleted,
	 * include_directed_posts, and include_annotations.
	 * @return An array of associative arrays, each representing a single post.
	 */
	public function getUserMentions($user_id='me',$params = array()) {
		return $this->httpReq('get',$this->_baseUrl.'users/'
			.urlencode($user_id).'/mentions?'.$this->buildQueryString($params));
	}

	/**
	 * Return the 20 most recent posts from the current User and
	 * the Users they follow.
	 * @param array $params An associative array of optional general parameters.
	 * This will likely change as the API evolves, as of this writing allowed keys
	 * are:	count, before_id, since_id, include_muted, include_deleted,
	 * include_directed_posts, and include_annotations.
	 * @return An array of associative arrays, each representing a single post.
	 */
	public function getUserStream($params = array()) {
		return $this->httpReq('get',$this->_baseUrl.'posts/stream?'.$this->buildQueryString($params));
	}

	/**
	 * Returns a specific user object.
	 * @param mixed $user_id The ID of the user you want to retrieve, or the string
	 * "me" to retrieve data for the users you're currently authenticated as.
	 * @param array $params An associative array of optional general parameters.
	 * This will likely change as the API evolves, as of this writing allowed keys
	 * are: include_annotations|include_user_annotations.
	 * @return array An associative array representing the user data.
	 */
	public function getUser($user_id='me', $params = array()) {
		return $this->httpReq('get',$this->_baseUrl.'users/'.urlencode($user_id)
						.'?'.$this->buildQueryString($params));
	}

	/**
	 * Add the specified user ID to the list of users followed.
	 * Returns the User object of the user being followed.
	 * @param integer $user_id The user ID of the user to follow.
	 * @return array An associative array representing the user you just followed.
	 */
	public function followUser($user_id=null) {
		return $this->httpReq('post',$this->_baseUrl.'users/'.urlencode($user_id).'/follow');
	}

	/**
	 * Removes the specified user ID to the list of users followed.
	 * Returns the User object of the user being unfollowed.
	 * @param integer $user_id The user ID of the user to unfollow.
	 * @return array An associative array representing the user you just unfollowed.
	 */
	public function unfollowUser($user_id=null) {
		return $this->httpReq('delete',$this->_baseUrl.'users/'.urlencode($user_id).'/follow');
	}

	/**
	 * Returns an array of User objects the specified user is following.
	 * @param mixed $user_id Either the ID of the user being followed, or
	 * the string "me", which will retrieve posts for the user you're authenticated
	 * as.
	 * @return array An array of associative arrays, each representing a single
	 * user following $user_id
	 */
	public function getFollowing($user_id='me') {
		return $this->httpReq('get',$this->_baseUrl.'users/'.$user_id.'/following');
	}

	/**
	 * Returns an array of User objects for users following the specified user.
	 * @param mixed $user_id Either the ID of the user being followed, or
	 * the string "me", which will retrieve posts for the user you're authenticated
	 * as.
	 * @return array An array of associative arrays, each representing a single
	 * user following $user_id
	 */
	public function getFollowers($user_id='me') {
		return $this->httpReq('get',$this->_baseUrl.'users/'.$user_id.'/followers');
	}

	/**
	 * Return Posts matching a specific #hashtag.
	 * @param string $hashtag The hashtag you're looking for.
	 * @param array $params An associative array of optional general parameters.
	 * This will likely change as the API evolves, as of this writing allowed keys
	 * are:	count, before_id, since_id, include_muted, include_deleted,
	 * include_directed_posts, and include_annotations.
	 * @return An array of associative arrays, each representing a single post.
	 */
	public function searchHashtags($hashtag=null, $params = array()) {
		return $this->httpReq('get',$this->_baseUrl.'posts/tag/'
				.urlencode($hashtag).'?'.$this->buildQueryString($params));
	}

	/**
	 * Retrieve a list of all public Posts on App.net, often referred to as the
	 * global stream.
	 * @param array $params An associative array of optional general parameters.
	 * This will likely change as the API evolves, as of this writing allowed keys
	 * are:	count, before_id, since_id, include_muted, include_deleted,
	 * include_directed_posts, and include_annotations.
	 * @return An array of associative arrays, each representing a single post.
	 */
	public function getPublicPosts($params = array()) {
		return $this->httpReq('get',$this->_baseUrl.'posts/stream/global?'.$this->buildQueryString($params));
	}

  /**
   * List User interactions
   */
  public function getMyInteractions($params = array()) {
    return $this->httpReq('get',$this->_baseUrl.'users/me/interactions?'.$this->buildQueryString($params));
  }

	/**
	 * Retrieve a user's user ID by specifying their username.
	 * Now supported by the API. We use the API if we have a token
	 * Otherwise we scrape the alpha.app.net site for the info.
	 * @param string $username The username of the user you want the ID of, without
	 * an @ symbol at the beginning.
	 * @return integer The user's user ID
	 */
	public function getIdByUsername($username=null) {
		if ($this->_accessToken) {
			$res=$this->httpReq('get',$this->_baseUrl.'users/@'.$username);
			$user_id=$res['data']['id'];
		} else {
			$ch = curl_init('https://alpha.app.net/'.urlencode(strtolower($username)));
			curl_setopt($ch, CURLOPT_POST, false);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch,CURLOPT_USERAGENT,
				'Mozilla/5.0 (Macintosh; Intel Mac OS X 10.7; rv:7.0.1) Gecko/20100101 Firefox/7.0.1');
			$response = curl_exec($ch);
			curl_close($ch);
			$temp = explode('title="User Id ',$response);
			$temp2 = explode('"',$temp[1]);
			$user_id = $temp2[0];
		}
		return $user_id;
	}

	/**
	 * Mute a user
	 * @param integer $user_id The user ID to mute
	 */
	public function muteUser($user_id=null) {
	 	return $this->httpReq('post',$this->_baseUrl.'users/'.urlencode($user_id).'/mute');
	}

	/**
	 * Unmute a user
	 * @param integer $user_id The user ID to unmute
	 */
	public function unmuteUser($user_id=null) {
		return $this->httpReq('delete',$this->_baseUrl.'users/'.urlencode($user_id).'/mute');
	}

	/**
	 * List the users muted by the current user
	 * @return array An array of associative arrays, each representing one muted user.
	 */
	public function getMuted() {
		return $this->httpReq('get',$this->_baseUrl.'users/me/muted');
	}

	/**
	* Star a post
	* @param integer $post_id The post ID to star
	*/
	public function starPost($post_id=null) {
		return $this->httpReq('post',$this->_baseUrl.'posts/'.urlencode($post_id).'/star');
	}

	/**
	* Unstar a post
	* @param integer $post_id The post ID to unstar
	*/
	public function unstarPost($post_id=null) {
		return $this->httpReq('delete',$this->_baseUrl.'posts/'.urlencode($post_id).'/star');
	}

	/**
	* List the posts starred by the current user
	* @param array $params An associative array of optional general parameters.
	* This will likely change as the API evolves, as of this writing allowed keys
	* are:	count, before_id, since_id, include_muted, include_deleted,
	* include_directed_posts, and include_annotations.
	* See https://github.com/appdotnet/api-spec/blob/master/resources/posts.md#general-parameters
	* @return array An array of associative arrays, each representing a single
	* user who has starred a post
	*/
	public function getStarred($user_id='me', $params = array()) {
		return $this->httpReq('get',$this->_baseUrl.'users/'.urlencode($user_id).'/stars'
					.'?'.$this->buildQueryString($params));
	}

	/**
	* List the users who have starred a post
	* @param integer $post_id the post ID to get stars from
	* @return array An array of associative arrays, each representing one user.
	*/
	public function getStars($post_id=null) {
		return $this->httpReq('get',$this->_baseUrl.'posts/'.urlencode($post_id).'/stars');
	}

	/**
	 * Returns an array of User objects of users who reposted the specified post.
	 * @param integer $post_id the post ID to
	 * @return array An array of associative arrays, each representing a single
	 * user who reposted $post_id
	 */
	public function getReposters($post_id){
		return $this->httpReq('get',$this->_baseUrl.'posts/'.urlencode($post_id).'/reposters');
	}

	/**
	 * Repost an existing Post object.
	 * @param integer $post_id The id of the post
	 * @return not a clue
	 */
	public function repost($post_id){
		return $this->httpReq('post',$this->_baseUrl.'posts/'.urlencode($post_id).'/repost');
	}

	/**
	 * Delete a post that the user has reposted.
	 * @param integer $post_id The id of the post
	 * @return not a clue
	 */
	public function deleteRepost($post_id){
		return $this->httpReq('delete',$this->_baseUrl.'posts/'.urlencode($post_id).'/repost');
	}

	/**
	* List the users who match a specific search term
	* @param string $search The search query. Supports @username or #tag searches as
	* well as normal search terms. Searches username, display name, bio information.
	* Does not search posts.
	* @return array An array of associative arrays, each representing one user.
	*/
	public function searchUsers($search="") {
		return $this->httpReq('get',$this->_baseUrl.'users/search?q='.urlencode($search));
	}

	/**
	 * Return the 20 most recent posts for a stream using a valid Token
	 * @param array $params An associative array of optional general parameters.
	 * This will likely change as the API evolves, as of this writing allowed keys
	 * are: count, before_id, since_id, include_muted, include_deleted,
	 * include_directed_posts, and include_annotations.
	 * @return An array of associative arrays, each representing a single post.
	 */
	public function getTokenStream($params = array()) {
		if ($params['access_token']) {
			return $this->httpReq('get',$this->_baseUrl.'posts/stream?'.$this->buildQueryString($params),$params);
		} else {
			return $this->httpReq('get',$this->_baseUrl.'posts/stream?'.$this->buildQueryString($params));
		}
	}

	/**
	 * Get a user object by username
	 * @param string $name the @name to get
	 * @return array representing one user
	 */
	public function getUserByName($name=null) {
		return $this->httpReq('get',$this->_baseUrl.'users/@'.$name);
	}

	/**
	* Return the 20 most recent Posts from the current User's personalized stream
	* and mentions stream merged into one stream.
	* @param array $params An associative array of optional general parameters.
	* This will likely change as the API evolves, as of this writing allowed keys
	* are: count, before_id, since_id, include_muted, include_deleted,
	* include_directed_posts, and include_annotations.
	* @return An array of associative arrays, each representing a single post.
	*/
	public function getUserUnifiedStream($params = array()) {
		return $this->httpReq('get',$this->_baseUrl.'posts/stream/unified?'.$this->buildQueryString($params));
  }

  /**
	 * Update Profile Data via JSON
	 * @data array containing user descriptors
	 */
	public function updateUserData($data = array()) {
		$json = json_encode($data);
		return $this->httpReq('put',$this->_baseUrl.'users/me', $json, 'application/json');
	}

	/**
	 * Update a user image
	 * @which avatar|cover
	 * @image path reference to image
	 */
	protected function updateUserImage($which = 'avatar', $image = null) {
		$data = array($which=>"@$image");
		return $this->httpReq('post-raw',$this->_baseUrl.'users/me/'.$which, $data, 'multipart/form-data');
	}

	public function updateUserAvatar($avatar = null) {
		if($avatar != null)
			return $this->updateUserImage('avatar', $avatar);
	}

	public function updateUserCover($cover = null) {
		if($cover != null)
			return $this->updateUserImage('cover', $cover);
	}

  /**
   * update stream marker
   */
  public function updateStreamMarker($data = array()) {
		$json = json_encode($data);
		return $this->httpReq('post',$this->_baseUrl.'posts/marker', $json, 'application/json');
  }

  /**
   * get a page of current user subscribed channels
   */
  public function getUserSubscriptions($params = array()) {
		return $this->httpReq('get',$this->_baseUrl.'channels?'.$this->buildQueryString($params));
  }

  /**
   * create a channel
   * note: you cannot create a channel with type=net.app.core.pm (see createMessage)
   */
  public function createChannel($data = array()) {
		$json = json_encode($data);
		return $this->httpReq('post',$this->_baseUrl.'channels'.($pm?'/pm/messsages':''), $json, 'application/json');
  }

  /**
   * get channelid info
   */
  public function getChannel($channelid) {
		return $this->httpReq('get',$this->_baseUrl.'channels/'.$channelid);
  }

  /**
   * update channelid
   */
  public function updateChannel($channelid, $data = array()) {
		$json = json_encode($data);
		return $this->httpReq('put',$this->_baseUrl.'channels/'.$channelid, $json, 'application/json');
  }

  /**
   * subscribe from channelid
   */
  public function channelSubscribe($channelid) {
		return $this->httpReq('post',$this->_baseUrl.'channels/'.$channelid.'/subscribe');
  }

  /**
   * unsubscribe from channelid
   */
  public function channelUnsubscribe($channelid) {
		return $this->httpReq('delete',$this->_baseUrl.'channels/'.$channelid.'/subscribe');
  }

  /**
   * get all user objects subscribed to channelid
   */
  public function getChannelSubscriptions($channelid, $params = array()) {
		return $this->httpReq('get',$this->_baseUrl.'channel/'.$channelid.'/subscribers?'.$this->buildQueryString($params));
  }

  /**
   * get all user IDs subscribed to channelid
   */
  public function getChannelSubscriptionsById($channelid) {
		return $this->httpReq('get',$this->_baseUrl.'channel/'.$channelid.'/subscribers/ids');
  }


  /**
   * get a page of messages in channelid
   */
  public function getMessages($channelid, $params = array()) {
		return $this->httpReq('get',$this->_baseUrl.'channels/'.$channelid.'/messages?'.$this->buildQueryString($params));
  }

  /**
   * create message
   * @param $channelid numeric or "pm" for auto-chanenl (type=net.app.core.pm)
   * @param $data array('text'=>'YOUR_MESSAGE') If a type=net.app.core.pm, then "destinations" key can be set to address as an array of people to send this PM too
   */
  public function createMessage($channelid,$data) {
		$json = json_encode($data);
		return $this->httpReq('post',$this->_baseUrl.'channels/'.$channelid.'/messages', $json, 'application/json');
  }

  /**
   * get message
   */
  public function getMessage($channelid,$messageid) {
		return $this->httpReq('get',$this->_baseUrl.'channels/'.$channelid.'/messages/'.$messageid);
  }

  /**
   * delete messsage
   */
  public function deleteMessage($channelid,$messageid) {
		return $this->httpReq('delete',$this->_baseUrl.'channels/'.$channelid.'/messages/'.$messageid);
  }

	public function getLastRequest() {
		return $this->_last_request;
	}
	public function getLastResponse() {
		return $this->_last_response;
	}

	/**
	 * Registers your function (or an array of object and method) to be called
	 * whenever an event is received via an open app.net stream. Your function
	 * will receive a single parameter, which is the object wrapper containing
	 * the meta and data.
	 * @param mixed A PHP callback (either a string containing the function name,
	 * or an array where the first element is the class/object and the second
	 * is the method).
	 */
	public function registerStreamFunction($function) {
		$this->_streamCallback = $function;
	}

	/**
	 * Opens a stream that's been created for this user/app and starts sending
	 * events/objects to your defined callback functions. You must define at
	 * least one callback function before opening a stream.
	 * @param mixed $stream Either a stream ID or the endpoint of a stream
	 * you've already created. This stream must exist and must be valid for
	 * your current access token. If you pass a stream ID, the library will
	 * make an API call to get the endpoint.
	 *
	 * This function will return immediately, but your callback functions
	 * will continue to receive events until you call closeStream() or until
	 * App.net terminates the stream from their end with an error.
	 *
	 * If you're disconnected due to a network error, the library will
	 * automatically attempt to reconnect you to the same stream, no action
	 * on your part is necessary for this. However if the app.net API returns
	 * an error, a reconnection attempt will not be made.
	 *
	 * Note there is no closeStream, because once you open a stream you
	 * can't stop it (unless you exit() or die() or throw an uncaught
	 * exception, or something else that terminates the script).
	 * @return boolean True
	 * @see createStream()
	 */
	public function openStream($stream) {
		// if there's already a stream running, don't allow another
		if ($this->_currentStream) {
			throw new AppDotNetException('There is already a stream being consumed, only one stream can be consumed per AppDotNetStream instance');
		}
		// must register a callback (or the exercise is pointless)
		if (!$this->_streamCallback) {
			throw new AppDotNetException('You must define your callback function using registerStreamFunction() before calling openStream');
		}
		// if the stream is a numeric value, get the stream info from the api
		if (is_numeric($stream)) {
			$stream = $this->getStream($stream);
			$this->_streamUrl = $stream['endpoint'];
		}
		else {
			$this->_streamUrl = $stream;
		}
		// continue doing this until we get an error back or something...?
		$this->httpStream('get',$this->_streamUrl);

		return true;
	}

	/**
	 * Close the currently open stream.
	 * @return true;
	 */
	public function closeStream() {
		if (!$this->_lastStreamActivity) {
			// never opened
			return;
		}
		if (!$this->_multiStream) {
			throw new AppDotNetException('You must open a stream before calling closeStream()');
		}
		curl_close($this->_currentStream);
		curl_multi_remove_handle($this->_multiStream,$this->_currentStream);
		curl_multi_close($this->_multiStream);
		$this->_currentStream = null;
		$this->_multiStream = null;
	}

	/**
	 * Retrieve all streams for the current access token.
	 * @return array An array of stream definitions.
	 */
	public function getAllStreams() {
		return $this->httpReq('get',$this->_baseUrl.'streams');
	}

	/**
	 * Returns a single stream specified by a stream ID. The stream must have been
	 * created with the current access token.
	 * @return array A stream definition
	 */
	public function getStream($streamId) {
		return $this->httpReq('get',$this->_baseUrl.'streams/'.urlencode($streamId));
	}

	/**
	 * Creates a stream for the current app access token.
	 *
	 * @param array $objectTypes The objects you want to retrieve data for from the
	 * stream. At time of writing these can be 'post', 'star', and/or 'user_follow'.
	 * If you don't specify, all events will be retrieved.
	 */
	public function createStream($objectTypes=null) {
		// default object types to everything
		if (is_null($objectTypes)) {
			$objectTypes = array('post','star','user_follow');
		}
		$data = array(
			'object_types'=>$objectTypes,
			'type'=>'long_poll',
		);
		$data = json_encode($data);
		$response = $this->httpReq('post',$this->_baseUrl.'streams',$data,'application/json');
		return $response;
	}

  /**
   * Update stream for the current app access token
   *
   * @param integer $streamId The stream ID to update. This stream must have been
   * created by the current access token.
   * @param array $data allows object_types, type, filter_id and key to be updated. filter_id/key can be omitted
   */
  public function updateStream($streamId,$data) {
    // objectTypes is likely required
		if (is_null($data['object_types'])) {
			$data['object_types'] = array('post','star','user_follow');
		}
		// type can still only be long_poll
		if (is_null($data['type'])) {
		  $data['type']='long_poll';
		}
		$data = json_encode($data);
		$response = $this->httpReq('put',$this->_baseUrl.'streams/'.urlencode($streamId),$data,'application/json');
		return $response;
  }

	/**
	 * Deletes a stream if you no longer need it.
	 *
	 * @param integer $streamId The stream ID to delete. This stream must have been
	 * created by the current access token.
	 */
	public function deleteStream($streamId) {
		return $this->httpReq('delete',$this->_baseUrl.'streams/'.urlencode($streamId));
	}

	/**
	 * Deletes all streams created by the current access token.
	 */
	public function deleteAllStreams() {
		return $this->httpReq('delete',$this->_baseUrl.'streams');
	}

	/**
	 * Internal function used to process incoming chunks from the stream. This is only
	 * public because it needs to be accessed by CURL. Do not call or use this function
	 * in your own code.
	 * @ignore
	 */
	public function httpStreamReceive($ch,$data) {
		$this->_lastStreamActivity = time();
		$this->_streamBuffer .= $data;
		if (!$this->_streamHeaders) {
			$pos = strpos($this->_streamBuffer,"\r\n\r\n");
			if ($pos!==false) {
				$this->_streamHeaders = substr($this->_streamBuffer,0,$pos);
				$this->_streamBuffer = substr($this->_streamBuffer,$pos+4);
			}
		}
		else {
			$pos = strpos($this->_streamBuffer,"\r\n");
			if ($pos!==false) {
				$command = substr($this->_streamBuffer,0,$pos);
				$this->_streamBuffer = substr($this->_streamBuffer,$pos+2);
				$command = json_decode($command,true);
				if ($command) {
					call_user_func($this->_streamCallback,$command);
				}
			}
		}
		return strlen($data);
	}

	/**
	 * Opens a long lived HTTP connection to the app.net servers, and sends data
	 * received to the httpStreamReceive function. As a general rule you should not
	 * directly call this method, it's used by openStream().
	 */
	protected function httpStream($act, $req, $params=array(),$contentType='application/x-www-form-urlencoded') {
		if ($this->_currentStream) {
			throw new AppDotNetException('There is already an open stream, you must close the existing one before opening a new one');
		}
		$headers = array();
		$this->_streamBuffer = '';
		if ($this->_accessToken) {
			$headers[] = 'Authorization: Bearer '.$this->_accessToken;
		}
		$this->_currentStream = curl_init($req);
		curl_setopt($this->_currentStream, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($this->_currentStream, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($this->_currentStream, CURLINFO_HEADER_OUT, true);
		curl_setopt($this->_currentStream, CURLOPT_HEADER, true);
		if ($this->_sslCA) {
			curl_setopt($this->_currentStream, CURLOPT_CAINFO, $this->_sslCA);
		}
		// every time we receive a chunk of data, forward it to httpStreamReceive
		curl_setopt($this->_currentStream, CURLOPT_WRITEFUNCTION, array($this, "httpStreamReceive"));

		// curl_exec($ch);
		// return;

		$this->_multiStream = curl_multi_init();
		$this->_lastStreamActivity = time();
		curl_multi_add_handle($this->_multiStream,$this->_currentStream);
	}

	public function reconnectStream() {
		$this->closeStream();
		$this->_connectFailCounter++;
		// if we've failed a few times, back off
		if ($this->_connectFailCounter>1) {
			$sleepTime = pow(2,$this->_connectFailCounter);
			// don't sleep more than 60 seconds
			if ($sleepTime>60) {
				$sleepTime = 60;
			}
			sleep($sleepTime);
		}
		$this->httpStream('get',$this->_streamUrl);
	}

	/**
	 * Process an open stream for x microseconds, then return. This is useful if you want
	 * to be doing other things while processing the stream. If you just want to
	 * consume the stream without other actions, you can call processForever() instead.
	 * @param float @microseconds The number of microseconds to process for before
	 * returning. There are 1,000,000 microseconds in a second.
	 * @return void
	 */
	public function processStream($microseconds=null) {
		if (!$this->_multiStream) {
			throw new AppDotNetException('You must open a stream before calling processStream()');
		}
		$start = microtime(true);
		$active = null;
		$inQueue = null;
		$sleepFor = 0;
		do {
			// if we haven't received anything within 30 seconds, reconnect
			if (time()-$this->_lastStreamActivity>=30) {
				$this->reconnectStream();
			}
			curl_multi_exec($this->_multiStream, $active);
			if (!$active) {
				$httpCode = curl_getinfo($this->_currentStream,CURLINFO_HTTP_CODE);
				// don't reconnect on 400 errors
				if ($httpCode>=400 && $httpCode<=499) {
					throw new AppDotNetException('Received HTTP error '.$httpCode.' check your URL and credentials before reconnecting');
				}
				$this->reconnectStream();
			}
			// sleep for a max of 2/10 of a second
			$timeSoFar = (microtime(true)-$start)*1000000;
			$sleepFor = 200000;
			if ($timeSoFar+$sleepFor>$microseconds) {
				$sleepFor = $microseconds - $timeSoFar;
			}

			if ($sleepFor>0) {
				usleep($sleepFor);
			}
		} while ($timeSoFar+$sleepFor<$microseconds);
	}

	/**
	 * Process an open stream forever. This function will never return, if you
	 * want to perform other actions while consuming the stream, you should use
	 * processFor() instead.
	 * @return void This function will never return
	 * @see processFor();
	 */
	public function processStreamForever() {
		while (true) {
			$this->processStream(600);
		}
	}


	/**
	 * Upload a file to a user's file store
	 * @param $file path reference to file
	 * @param array $params An associative array of optional general parameters.
	 * This will likely change as the API evolves, as of this writing allowed keys
	 * are: include_annotations|include_file_annotations.
	 * @return array An associative array representing the file
	 */
	public function createFile($file = null, $params=array()) {
		$finfo = finfo_open(FILEINFO_MIME_TYPE);
		$mime = finfo_file($finfo, $file);
		finfo_close($finfo);

		$data = array('content'=>"@$file;type=$mime", 'type'=> $params['metadata'], 'name' => $params['name']);
		return $this->httpReq('post-raw',$this->_baseUrl.'files', $data, 'multipart/form-data');
	}


	public function createFilePlaceholder($file = null, $params=array()) {
		$name = basename($file);
		$data = array('annotations' => $params['annotations'], 'kind' => $params['kind'],
				'name' => $name, 'type' => $params['metadata']);
		$json = json_encode($data);
		return $this->httpReq('post',$this->_baseUrl.'files', $json, 'application/json');
	}

	public function updateFileContent($fileid, $file) {

		$data = file_get_contents($file);
		$finfo = finfo_open(FILEINFO_MIME_TYPE);
		$mime = finfo_file($finfo, $file);
		finfo_close($finfo);

		return $this->httpReq('put',$this->_baseUrl.'files/' . $fileid
						.'/content', $data, $mime);
	}

	 /**
         * Allows for file rename and annotation changes.
	 * @param integer $file_id The ID of the file to update
	 * @param array $params An associative array of file parameters.
	 * @return array An associative array representing the updated file
         */
        public function updateFile($file_id=null, $params=array()) {
                $data = array('annotations' => $params['annotations'] , 'name' => $params['name']);
                $json = json_encode($data);
                return $this->httpReq('put',$this->_baseUrl.'files/'.urlencode($file_id), $json, 'application/json');
        }

	/**
	 * Returns a specific File.
	 * @param integer $file_id The ID of the file to retrieve
	 * @param array $params An associative array of optional general parameters.
	 * This will likely change as the API evolves, as of this writing allowed keys
	 * are: include_annotations|include_file_annotations.
	 * @return array An associative array representing the file
	 */
	public function getFile($file_id=null,$params = array()) {
		if(is_array($file_id)) {
			$ids = '';
			foreach($file_id as $id) {
				$ids .= $id . ',';
			}
			$params['ids'] = substr($ids, 0, -1);
			return $this->httpReq('get',$this->_baseUrl.'files'
						.'?'.$this->buildQueryString($params));
		} else {
			return $this->httpReq('get',$this->_baseUrl.'files/'.urlencode($file_id)
						.'?'.$this->buildQueryString($params));
		}
	}

	/**
	 * Returns file objects.
	 * @param array $file_ids The IDs of the files to retrieve
	 * @param array $params An associative array of optional general parameters.
	 * This will likely change as the API evolves, as of this writing allowed keys
	 * are: include_annotations|include_file_annotations.
	 * @return array An associative array representing the file data.
	 */
	public function getFiles($file_ids=array(), $params = array()) {
		return $this->getFile($file_ids, $params);
	}

	/**
	 * Returns a user's file objects.
	 * @param array $params An associative array of optional general parameters.
	 * This will likely change as the API evolves, as of this writing allowed keys
	 * are: include_annotations|include_file_annotations|include_user_annotations.
	 * @return array An associative array representing the file data.
	 */
	public function getUserFiles($params = array()) {
		return $this->httpReq('get',$this->_baseUrl.'users/me/files'
						.'?'.$this->buildQueryString($params));
	}

	/**
	 * Delete a File. The current user must be the same user who created the File.
	 * It returns the deleted File on success.
	 * @param integer $file_id The ID of the file to delete
	 * @return array An associative array representing the file that was deleted
	 */
	public function deleteFile($file_id=null) {
		return $this->httpReq('delete',$this->_baseUrl.'files/'.urlencode($file_id));
	}

}

class AppDotNetException extends Exception {}
