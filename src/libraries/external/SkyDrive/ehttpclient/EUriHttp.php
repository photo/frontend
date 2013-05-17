<?php

/**
 * Modified version of Zend_Uri_Http of Zend for easy integration
 * with Yii as extension.
 *
 * Copyright (c) 2005-2010, Zend Technologies USA, Inc.
 * All rights reserved.

 * Redistribution and use in source and binary forms, with or without modification,
 * are permitted provided that the following conditions are met:
 * 
 *     * Redistributions of source code must retain the above copyright notice,
 *       this list of conditions and the following disclaimer.
 *
 *     * Redistributions in binary form must reproduce the above copyright notice,
 *       this list of conditions and the following disclaimer in the documentation
 *       and/or other materials provided with the distribution.
 * 
 *     * Neither the name of Zend Technologies USA, Inc. nor the names of its
 *       contributors may be used to endorse or promote products derived from this
 *       software without specific prior written permission.
 * 
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND
 * ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED
 * WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE
 * DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR
 * ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES
 * (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
 * LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON
 * ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
 * (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS
 * SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 */

/**
 * HTTP(S) URI handler
 *
 * @category  Yii
 * @package   EUriHttp
 * @uses      EUri
 */
class EUriHttp extends EUri {

	/**
	 * HTTP username
	 *
	 * @var string
	 */
	protected $_username = '';

	/**
	 * HTTP password
	 *
	 * @var string
	 */
	protected $_password = '';

	/**
	 * HTTP host
	 *
	 * @var string
	 */
	protected $_host = '';

	/**
	 * HTTP post
	 *
	 * @var string
	 */
	protected $_port = '';

	/**
	 * HTTP part
	 *
	 * @var string
	 */
	protected $_path = '';

	/**
	 * HTTP query
	 *
	 * @var string
	 */
	protected $_query = '';

	/**
	 * HTTP fragment
	 *
	 * @var string
	 */
	protected $_fragment = '';

	/**
	 * Regular expression grammar rules for validation; values added by constructor
	 *
	 * @var array
	 */
	protected $_regex = array();

	/**
	 * Constructor accepts a string $scheme (e.g., http, https) and a scheme-specific part of the URI
	 * (e.g., example.com/path/to/resource?query=param#fragment)
	 *
	 * @param  string $scheme         The scheme of the URI
	 * @param  string $schemeSpecific The scheme-specific part of the URI
	 * @throws CException When the URI is not valid
	 */
	protected function __construct($scheme, $schemeSpecific = '')
	{
		// Set the scheme
		$this->_scheme = $scheme;

		// Set up grammar rules for validation via regular expressions. These
		// are to be used with slash-delimited regular expression strings.
		$this->_regex['alphanum'] = '[^\W_]';
		$this->_regex['escaped'] = '(?:%[\da-fA-F]{2})';
		$this->_regex['mark'] = '[-_.!~*\'()\[\]]';
		$this->_regex['reserved'] = '[;\/?:@&=+$,]';
		$this->_regex['unreserved'] = '(?:' . $this->_regex['alphanum'] . '|' . $this->_regex['mark'] . ')';
		$this->_regex['segment'] = '(?:(?:' . $this->_regex['unreserved'] . '|' . $this->_regex['escaped']
			. '|[:@&=+$,;])*)';
		$this->_regex['path'] = '(?:\/' . $this->_regex['segment'] . '?)+';
		$this->_regex['uric'] = '(?:' . $this->_regex['reserved'] . '|' . $this->_regex['unreserved'] . '|'
			. $this->_regex['escaped'] . ')';
		// If no scheme-specific part was supplied, the user intends to create
		// a new URI with this object.  No further parsing is required.
		if (strlen($schemeSpecific) === 0)
		{
			return;
		}

		// Parse the scheme-specific URI parts into the instance variables.
		$this->_parseUri($schemeSpecific);

		// Validate the URI
		if ($this->valid() === false)
		{
			throw new CException('Invalid URI supplied');
		}
	}

	/**
	 * Creates a EUriHttp from the given string
	 *
	 * @param  string $uri String to create URI from, must start with
	 *                     'http://' or 'https://'
	 * @throws InvalidArgumentException  When the given $uri is not a string or
	 *                                   does not start with http:// or https://
	 * @throws CException        When the given $uri is invalid
	 * @return EUriHttp
	 */
	public static function fromString($uri)
	{
		if (is_string($uri) === false)
		{
			throw new InvalidArgumentException('$uri is not a string');
		}

		$uri = explode(':', $uri, 2);
		$scheme = strtolower($uri[0]);
		$schemeSpecific = isset($uri[1]) === true ? $uri[1] : '';

		if (in_array($scheme, array('http', 'https')) === false)
		{
			throw new CException("Invalid scheme: '$scheme'");
		}

		$schemeHandler = new EUriHttp($scheme, $schemeSpecific);
		return $schemeHandler;
	}

	/**
	 * Parse the scheme-specific portion of the URI and place its parts into instance variables.
	 *
	 * @param  string $schemeSpecific The scheme-specific portion to parse
	 * @throws CException When scheme-specific decoposition fails
	 * @throws CException When authority decomposition fails
	 * @return void
	 */
	protected function _parseUri($schemeSpecific)
	{
		// High-level decomposition parser
		$pattern = '~^((//)([^/?#]*))([^?#]*)(\?([^#]*))?(#(.*))?$~';
		$status = @preg_match($pattern, $schemeSpecific, $matches);
		if ($status === false)
		{
			throw new EUri_Exception('Internal error: scheme-specific decomposition failed');
		}

		// Failed decomposition; no further processing needed
		if ($status === false)
		{
			return;
		}

		// Save URI components that need no further decomposition
		$this->_path = isset($matches[4]) === true ? $matches[4] : '';
		$this->_query = isset($matches[6]) === true ? $matches[6] : '';
		$this->_fragment = isset($matches[8]) === true ? $matches[8] : '';

		// Additional decomposition to get username, password, host, and port
		$combo = isset($matches[3]) === true ? $matches[3] : '';
		$pattern = '~^(([^:@]*)(:([^@]*))?@)?([^:]+)(:(.*))?$~';
		$status = @preg_match($pattern, $combo, $matches);
		if ($status === false)
		{

			throw new CException('Internal error: authority decomposition failed');
		}

		// Failed decomposition; no further processing needed
		if ($status === false)
		{
			return;
		}

		// Save remaining URI components
		$this->_username = isset($matches[2]) === true ? $matches[2] : '';
		$this->_password = isset($matches[4]) === true ? $matches[4] : '';
		$this->_host = isset($matches[5]) === true ? $matches[5] : '';
		$this->_port = isset($matches[7]) === true ? $matches[7] : '';
	}

	/**
	 * Returns a URI based on current values of the instance variables. If any
	 * part of the URI does not pass validation, then an exception is thrown.
	 *
	 * @throws CException When one or more parts of the URI are invalid
	 * @return string
	 */
	public function getUri()
	{
		if ($this->valid() === false)
		{
			throw new CException('One or more parts of the URI are invalid');
		}

		$password = strlen($this->_password) > 0 ? ":$this->_password" : '';
		$auth = strlen($this->_username) > 0 ? "$this->_username$password@" : '';
		$port = strlen($this->_port) > 0 ? ":$this->_port" : '';
		$query = strlen($this->_query) > 0 ? "?$this->_query" : '';
		$fragment = strlen($this->_fragment) > 0 ? "#$this->_fragment" : '';

		return $this->_scheme
			. '://'
			. $auth
			. $this->_host
			. $port
			. $this->_path
			. $query
			. $fragment;
	}

	/**
	 * Validate the current URI from the instance variables. Returns true if and only if all
	 * parts pass validation.
	 *
	 * @return boolean
	 */
	public function valid()
	{
		// Return true if and only if all parts of the URI have passed validation
		return $this->validateUsername()
			and $this->validatePassword()
			and $this->validateHost()
			and $this->validatePort()
			and $this->validatePath()
			and $this->validateQuery()
			and $this->validateFragment();
	}

	/**
	 * Returns the username portion of the URL, or FALSE if none.
	 *
	 * @return string
	 */
	public function getUsername()
	{
		return strlen($this->_username) > 0 ? $this->_username : false;
	}

	/**
	 * Returns true if and only if the username passes validation. If no username is passed,
	 * then the username contained in the instance variable is used.
	 *
	 * @param  string $username The HTTP username
	 * @throws CException When username validation fails
	 * @return boolean
	 * @link   http://www.faqs.org/rfcs/rfc2396.html
	 */
	public function validateUsername($username = null)
	{
		if ($username === null)
		{
			$username = $this->_username;
		}

		// If the username is empty, then it is considered valid
		if (strlen($username) === 0)
		{
			return true;
		}

		// Check the username against the allowed values
		$status = @preg_match('/^(' . $this->_regex['alphanum'] . '|' . $this->_regex['mark'] . '|'
				. $this->_regex['escaped'] . '|[;:&=+$,])+$/', $username);
		if ($status === false)
		{
			throw new CException('Internal error: username validation failed');
		}

		return $status === 1;
	}

	/**
	 * Sets the username for the current URI, and returns the old username
	 *
	 * @param  string $username The HTTP username
	 * @throws CException When $username is not a valid HTTP username
	 * @return string
	 */
	public function setUsername($username)
	{
		if ($this->validateUsername($username) === false)
		{
			throw new CException("Username \"$username\" is not a valid HTTP username");
		}

		$oldUsername = $this->_username;
		$this->_username = $username;

		return $oldUsername;
	}

	/**
	 * Returns the password portion of the URL, or FALSE if none.
	 *
	 * @return string
	 */
	public function getPassword()
	{
		return strlen($this->_password) > 0 ? $this->_password : false;
	}

	/**
	 * Returns true if and only if the password passes validation. If no password is passed,
	 * then the password contained in the instance variable is used.
	 *
	 * @param  string $password The HTTP password
	 * @throws CException When password validation fails
	 * @return boolean
	 * @link   http://www.faqs.org/rfcs/rfc2396.html
	 */
	public function validatePassword($password = null)
	{
		if ($password === null)
		{
			$password = $this->_password;
		}

		// If the password is empty, then it is considered valid
		if (strlen($password) === 0)
		{
			return true;
		}

		// If the password is nonempty, but there is no username, then it is considered invalid
		if (strlen($password) > 0 and strlen($this->_username) === 0)
		{
			return false;
		}

		// Check the password against the allowed values
		$status = @preg_match('/^(' . $this->_regex['alphanum'] . '|' . $this->_regex['mark'] . '|'
				. $this->_regex['escaped'] . '|[;:&=+$,])+$/', $password);
		if ($status === false)
		{
			throw new CException('Internal error: password validation failed.');
		}

		return $status == 1;
	}

	/**
	 * Sets the password for the current URI, and returns the old password
	 *
	 * @param  string $password The HTTP password
	 * @throws CException When $password is not a valid HTTP password
	 * @return string
	 */
	public function setPassword($password)
	{
		if ($this->validatePassword($password) === false)
		{
			throw new CException("Password \"$password\" is not a valid HTTP password.");
		}

		$oldPassword = $this->_password;
		$this->_password = $password;

		return $oldPassword;
	}

	/**
	 * Returns the domain or host IP portion of the URL, or FALSE if none.
	 *
	 * @return string
	 */
	public function getHost()
	{
		return strlen($this->_host) > 0 ? $this->_host : false;
	}

	/**
	 * Returns true if and only if the host string passes validation. If no host is passed,
	 * then the host contained in the instance variable is used.
	 *
	 * @param  string $host The HTTP host
	 * @return boolean
	 * @uses   EHostnameValidator
	 */
	public function validateHost($host = null)
	{
		if ($host === null)
		{
			$host = $this->_host;
		}

		// If the host is empty, then it is considered invalid
		if (strlen($host) === 0)
		{
			return false;
		}


		// Check the host against the allowed values; delegated to Zend_Filter.
		$validate = new EHostnameValidator(EHostnameValidator::ALLOW_ALL);

		return $validate->isValid($host);
	}

	/**
	 * Sets the host for the current URI, and returns the old host
	 *
	 * @param  string $host The HTTP host
	 * @throws CException When $host is nota valid HTTP host
	 * @return string
	 */
	public function setHost($host)
	{
		if ($this->validateHost($host) === false)
		{

			throw new CException("Host \"$host\" is not a valid HTTP host");
		}

		$oldHost = $this->_host;
		$this->_host = $host;

		return $oldHost;
	}

	/**
	 * Returns the TCP port, or FALSE if none.
	 *
	 * @return string
	 */
	public function getPort()
	{
		return strlen($this->_port) > 0 ? $this->_port : false;
	}

	/**
	 * Returns true if and only if the TCP port string passes validation. If no port is passed,
	 * then the port contained in the instance variable is used.
	 *
	 * @param  string $port The HTTP port
	 * @return boolean
	 */
	public function validatePort($port = null)
	{
		if ($port === null)
		{
			$port = $this->_port;
		}

		// If the port is empty, then it is considered valid
		if (strlen($port) === 0)
		{
			return true;
		}

		// Check the port against the allowed values
		return ctype_digit((string) $port) and 1 <= $port and $port <= 65535;
	}

	/**
	 * Sets the port for the current URI, and returns the old port
	 *
	 * @param  string $port The HTTP port
	 * @throws CException When $port is not a valid HTTP port
	 * @return string
	 */
	public function setPort($port)
	{
		if ($this->validatePort($port) === false)
		{
			throw new CException("Port \"$port\" is not a valid HTTP port.");
		}

		$oldPort = $this->_port;
		$this->_port = $port;

		return $oldPort;
	}

	/**
	 * Returns the path and filename portion of the URL, or FALSE if none.
	 *
	 * @return string
	 */
	public function getPath()
	{
		return strlen($this->_path) > 0 ? $this->_path : '/';
	}

	/**
	 * Returns true if and only if the path string passes validation. If no path is passed,
	 * then the path contained in the instance variable is used.
	 *
	 * @param  string $path The HTTP path
	 * @throws CException When path validation fails
	 * @return boolean
	 */
	public function validatePath($path = null)
	{
		if ($path === null)
		{
			$path = $this->_path;
		}

		// If the path is empty, then it is considered valid
		if (strlen($path) === 0)
		{
			return true;
		}

		// Determine whether the path is well-formed
		$pattern = '/^' . $this->_regex['path'] . '$/';
		$status = @preg_match($pattern, $path);
		if ($status === false)
		{
			throw new CException('Internal error: path validation failed');
		}

		return (boolean) $status;
	}

	/**
	 * Sets the path for the current URI, and returns the old path
	 *
	 * @param  string $path The HTTP path
	 * @throws CException When $path is not a valid HTTP path
	 * @return string
	 */
	public function setPath($path)
	{
		if ($this->validatePath($path) === false)
		{
			throw new CException("Path \"$path\" is not a valid HTTP path");
		}

		$oldPath = $this->_path;
		$this->_path = $path;

		return $oldPath;
	}

	/**
	 * Returns the query portion of the URL (after ?), or FALSE if none.
	 *
	 * @return string
	 */
	public function getQuery()
	{
		return strlen($this->_query) > 0 ? $this->_query : false;
	}

	/**
	 * Returns true if and only if the query string passes validation. If no query is passed,
	 * then the query string contained in the instance variable is used.
	 *
	 * @param  string $query The query to validate
	 * @throws CException When query validation fails
	 * @return boolean
	 * @link   http://www.faqs.org/rfcs/rfc2396.html
	 */
	public function validateQuery($query = null)
	{
		if ($query === null)
		{
			$query = $this->_query;
		}

		// If query is empty, it is considered to be valid
		if (strlen($query) === 0)
		{
			return true;
		}

		// Determine whether the query is well-formed
		$pattern = '/^' . $this->_regex['uric'] . '*$/';
		$status = @preg_match($pattern, $query);
		if ($status === false)
		{
			throw new CException('Internal error: query validation failed');
		}

		return $status == 1;
	}

	/**
	 * Set the query string for the current URI, and return the old query
	 * string This method accepts both strings and arrays.
	 *
	 * @param  string|array $query The query string or array
	 * @throws CException When $query is not a valid query string
	 * @return string              Old query string
	 */
	public function setQuery($query)
	{
		$oldQuery = $this->_query;

		// If query is empty, set an empty string
		if (empty($query) === true)
		{
			$this->_query = '';
			return $oldQuery;
		}

		// If query is an array, make a string out of it
		if (is_array($query) === true)
		{
			$query = http_build_query($query, '', '&');
		} else
		{
			// If it is a string, make sure it is valid. If not parse and encode it
			$query = (string) $query;
			if ($this->validateQuery($query) === false)
			{
				parse_str($query, $queryArray);
				$query = http_build_query($queryArray, '', '&');
			}
		}

		// Make sure the query is valid, and set it
		if ($this->validateQuery($query) === false)
		{
			throw new CException("'$query' is not a valid query string");
		}

		$this->_query = $query;

		return $oldQuery;
	}

	/**
	 * Returns the fragment portion of the URL (after #), or FALSE if none.
	 *
	 * @return string|false
	 */
	public function getFragment()
	{
		return strlen($this->_fragment) > 0 ? $this->_fragment : false;
	}

	/**
	 * Returns true if and only if the fragment passes validation. If no fragment is passed,
	 * then the fragment contained in the instance variable is used.
	 *
	 * @param  string $fragment Fragment of an URI
	 * @throws CException When fragment validation fails
	 * @return boolean
	 * @link   http://www.faqs.org/rfcs/rfc2396.html
	 */
	public function validateFragment($fragment = null)
	{
		if ($fragment === null)
		{
			$fragment = $this->_fragment;
		}

		// If fragment is empty, it is considered to be valid
		if (strlen($fragment) === 0)
		{
			return true;
		}

		// Determine whether the fragment is well-formed
		$pattern = '/^' . $this->_regex['uric'] . '*$/';
		$status = @preg_match($pattern, $fragment);
		if ($status === false)
		{
			throw new CException('Internal error: fragment validation failed');
		}

		return (boolean) $status;
	}

	/**
	 * Sets the fragment for the current URI, and returns the old fragment
	 *
	 * @param  string $fragment Fragment of the current URI
	 * @throws CException When $fragment is not a valid HTTP fragment
	 * @return string
	 */
	public function setFragment($fragment)
	{
		if ($this->validateFragment($fragment) === false)
		{
			throw new CException("Fragment \"$fragment\" is not a valid HTTP fragment");
		}

		$oldFragment = $this->_fragment;
		$this->_fragment = $fragment;

		return $oldFragment;
	}

}
