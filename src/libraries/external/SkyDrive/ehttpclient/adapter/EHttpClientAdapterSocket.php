<?php

/**
 * Modified version of Socket Adapter of Zend for easy integration
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
 * A sockets based (stream_socket_client) adapter class for EHttpClient. Can be used
 * on almost every PHP environment, and does not require any special extensions.
 *
 * @category   Yii
 * @package    Zend_Http
 * @subpackage Client_Adapter
 */
class EHttpClientAdapterSocket implements EHttpClientAdapterInterface {

	/**
	 * The socket for server connection
	 *
	 * @var resource|null
	 */
	protected $socket = null;

	/**
	 * What host/port are we connected to?
	 *
	 * @var array
	 */
	protected $connected_to = array(null, null);

	/**
	 * Parameters array
	 *
	 * @var array
	 */
	protected $config = array(
		'persistent' => false,
		'ssltransport' => 'ssl',
		'sslcert' => null,
		'sslpassphrase' => null
	);

	/**
	 * Request method - will be set by write() and might be used by read()
	 *
	 * @var string
	 */
	protected $method = null;

	/**
	 * Adapter constructor, currently empty. Config is set using setConfig()
	 *
	 */
	public function __construct()
	{
		
	}

	/**
	 * Set the configuration array for the adapter
	 *
	 * @param array $config
	 */
	public function setConfig($config = array())
	{
		if (!is_array($config))
		{

			throw new EHttpClientException(
				Yii::t('EHttpClient', '$config expects an array, ' . gettype($config) . ' received.'));
		}

		foreach ($config as $k => $v)
		{
			$this->config[strtolower($k)] = $v;
		}
	}

	/**
	 * Connect to the remote server
	 *
	 * @param string  $host
	 * @param int     $port
	 * @param boolean $secure
	 * @param int     $timeout
	 */
	public function connect($host, $port = 80, $secure = false)
	{
		// If the URI should be accessed via SSL, prepend the Hostname with ssl://
		$host = ($secure ? $this->config['ssltransport'] : 'tcp') . '://' . $host;

		// If we are connected to the wrong host, disconnect first
		if (($this->connected_to[0] != $host || $this->connected_to[1] != $port))
		{
			if (is_resource($this->socket))
				$this->close();
		}

		// Now, if we are not connected, connect
		if (!is_resource($this->socket) || !$this->config['keepalive'])
		{
			$context = stream_context_create();
			if ($secure)
			{
				if ($this->config['sslcert'] !== null)
				{
					if (!stream_context_set_option($context, 'ssl', 'local_cert', $this->config['sslcert']))
					{
						throw new EHttpClientException(
							Yii::t('EHttpClient', 'Unable to set sslcert option'));
					}
				}
				if ($this->config['sslpassphrase'] !== null)
				{
					if (!stream_context_set_option($context, 'ssl', 'passphrase', $this->config['sslpassphrase']))
					{
						throw new EHttpClientException(
							Yii::t('EHttpClient', 'Unable to set sslpassphrase option'));
					}
				}
			}

			$flags = STREAM_CLIENT_CONNECT;
			if ($this->config['persistent'])
				$flags |= STREAM_CLIENT_PERSISTENT;

			$this->socket = @stream_socket_client($host . ':' . $port, $errno, $errstr, (int) $this->config['timeout'], $flags, $context);
			if (!$this->socket)
			{
				$this->close();
				throw new EHttpClientException(
					Yii::t('EHttpClient', 'Unable to Connect to ' . $host . ':' . $port . '. Error #' . $errno . ': ' . $errstr));
			}

			// Set the stream timeout
			if (!stream_set_timeout($this->socket, (int) $this->config['timeout']))
			{
				throw new EHttpClientException(
					Yii::t('EHttpClient', 'Unable to set the connection timeout'));
			}

			// Update connected_to
			$this->connected_to = array($host, $port);
		}
	}

	/**
	 * Send request to the remote server
	 *
	 * @param string        $method
	 * @param EUriHttp 		$uri
	 * @param string        $http_ver
	 * @param array         $headers
	 * @param string        $body
	 * @return string Request as string
	 */
	public function write($method, $uri, $http_ver = '1.1', $headers = array(), $body = '')
	{
		// Make sure we're properly connected
		if (!$this->socket)
		{
			throw new EHttpClientException(
				Yii::t('EHttpClient', 'Trying to write but we are not connected'));
		}

		$host = $uri->getHost();
		$host = (strtolower($uri->getScheme()) == 'https' ? $this->config['ssltransport'] : 'tcp') . '://' . $host;
		if ($this->connected_to[0] != $host || $this->connected_to[1] != $uri->getPort())
		{
			throw new EHttpClientException(
				Yii::t('EHttpClient', 'Trying to write but we are connected to the wrong host'));
		}

		// Save request method for later
		$this->method = $method;

		// Build request headers
		$path = $uri->getPath();
		if ($uri->getQuery())
			$path .= '?' . $uri->getQuery();
		$request = "{$method} {$path} HTTP/{$http_ver}\r\n";
		foreach ($headers as $k => $v)
		{
			if (is_string($k))
				$v = ucfirst($k) . ": $v";
			$request .= "$v\r\n";
		}

		// Add the request body
		$request .= "\r\n" . $body;

		// Send the request
		if (!@fwrite($this->socket, $request))
		{
			throw new EHttpClientException(
				Yii::t('EHttpClient', 'Error writing request to server'));
		}

		return $request;
	}

	/**
	 * Read response from server
	 *
	 * @return string
	 */
	public function read()
	{
		// First, read headers only
		$response = '';
		$gotStatus = false;
		while ($line = @fgets($this->socket))
		{
			$gotStatus = $gotStatus || (strpos($line, 'HTTP') !== false);
			if ($gotStatus)
			{
				$response .= $line;
				if (!chop($line))
					break;
			}
		}

		$statusCode = EHttpResponse::extractCode($response);

		// Handle 100 and 101 responses internally by restarting the read again
		if ($statusCode == 100 || $statusCode == 101)
			return $this->read();

		/**
		 * Responses to HEAD requests and 204 or 304 responses are not expected
		 * to have a body - stop reading here
		 */
		if ($statusCode == 304 || $statusCode == 204 ||
			$this->method == EHttpClient::HEAD)
			return $response;

		// Check headers to see what kind of connection / transfer encoding we have
		$headers = EHttpResponse::extractHeaders($response);

		// if the connection is set to close, just read until socket closes
		if (isset($headers['connection']) && $headers['connection'] == 'close')
		{
			while ($buff = @fread($this->socket, 8192))
			{
				$response .= $buff;
			}

			$this->close();

			// Else, if we got a transfer-encoding header (chunked body)
		} elseif (isset($headers['transfer-encoding']))
		{
			if ($headers['transfer-encoding'] == 'chunked')
			{
				do
				{
					$chunk = '';
					$line = @fgets($this->socket);
					$chunk .= $line;

					$hexchunksize = ltrim(chop($line), '0');
					$hexchunksize = strlen($hexchunksize) ? strtolower($hexchunksize) : 0;

					$chunksize = hexdec(chop($line));
					if (dechex($chunksize) != $hexchunksize)
					{
						@fclose($this->socket);

						throw new EHttpClientException(
							Yii::t('EHttpClient', 'Invalid chunk size "' .
								$hexchunksize . '" unable to read chunked body'));
					}

					$left_to_read = $chunksize;
					while ($left_to_read > 0)
					{
						$line = @fread($this->socket, $left_to_read);
						$chunk .= $line;
						$left_to_read -= strlen($line);
					}

					$chunk .= @fgets($this->socket);
					$response .= $chunk;
				} while ($chunksize > 0);
			} else
			{
				throw new EHttpClientException(
					Yii::t('EHttpClient', 'Cannot handle "' .
						$headers['transfer-encoding'] . '" transfer encoding'));
			}

			// Else, if we got the content-length header, read this number of bytes
		} elseif (isset($headers['content-length']))
		{
			$left_to_read = $headers['content-length'];
			$chunk = '';
			while ($left_to_read > 0)
			{
				$chunk = @fread($this->socket, $left_to_read);
				$left_to_read -= strlen($chunk);
				$response .= $chunk;
			}

			// Fallback: just read the response (should not happen)
		} else
		{
			while ($buff = @fread($this->socket, 8192))
			{
				$response .= $buff;
			}

			$this->close();
		}

		return $response;
	}

	/**
	 * Close the connection to the server
	 *
	 */
	public function close()
	{
		if (is_resource($this->socket))
			@fclose($this->socket);
		$this->socket = null;
		$this->connected_to = array(null, null);
	}

	/**
	 * Destructor: make sure the socket is disconnected 
	 * 
	 * If we are in persistent TCP mode, will not close the connection
	 *
	 */
	public function __destruct()
	{
		if (!$this->config['persistent'])
		{
			if ($this->socket)
				$this->close();
		}
	}

}
