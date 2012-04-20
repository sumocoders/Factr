<?php

/**
 * Factr class
 *
 * This source file can be used to communicate with Factr (http://factr.be)
 *
 * The class is documented in the file itself. If you find any bugs help me out and report them. Reporting can be done by sending an email to php-factr-bugs[at]verkoyen[dot]eu.
 * If you report a bug, make sure you give me enough information (include your code).
 *
 * License
 * Copyright (c) Tijs Verkoyen. All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without modification, are permitted provided that the following conditions are met:
 *
 * 1. Redistributions of source code must retain the above copyright notice, this list of conditions and the following disclaimer.
 * 2. Redistributions in binary form must reproduce the above copyright notice, this list of conditions and the following disclaimer in the documentation and/or other materials provided with the distribution.
 * 3. The name of the author may not be used to endorse or promote products derived from this software without specific prior written permission.
 *
 * This software is provided by the author "as is" and any express or implied warranties, including, but not limited to, the implied warranties of merchantability and fitness for a particular purpose are disclaimed. In no event shall the author be liable for any direct, indirect, incidental, special, exemplary, or consequential damages (including, but not limited to, procurement of substitute goods or services; loss of use, data, or profits; or business interruption) however caused and on any theory of liability, whether in contract, strict liability, or tort (including negligence or otherwise) arising in any way out of the use of this software, even if advised of the possibility of such damage.
 *
 * @author			Tijs Verkoyen <php-factr@verkoyen.eu>
 * @version			1.0.0
 *
 * @copyright		Copyright (c) Tijs Verkoyen. All rights reserved.
 * @license			BSD License
 */
class Factr
{
	// internal constant to enable/disable debugging
	const DEBUG = false;

	// url for the API
	const API_URL = 'https://app.factr.be/api';

	// port for the factr-API
	const API_PORT = 443;

	// version of the API
	const API_VERSION = 'v1';

	// current version
	const VERSION = '1.0.0';

	/**
	 * cURL instance
	 *
	 * @var	resource
	 */
	private $curl;

	/**
	 * The password
	 *
	 * @var	string
	 */
	private $password;

	/**
	 * The timeout
	 *
	 * @var	int
	 */
	private $timeOut = 30;

	/**
	 * The user agent
	 *
	 * @var	string
	 */
	private $userAgent;

	/**
	 * The username
	 *
	 * @var	string
	 */
	private $username;

// class methods
	/**
	 * Default constructor
	 *
	 * @param string $username	The username to authenticate with.
	 * @param string $password	The password to use for authenticating.
	 */
	public function __construct($username, $password)
	{
		// set some properties
		$this->setUsername($username);
		$this->setPassword($password);
	}

	/**
	 * Default destructor
	 */
	public function __destruct()
	{
		// close the curl-instance if needed
		if($this->curl !== null) curl_close($this->curl);
	}

	/**
	 * Make the call
	 *
	 * @param string $url				The URL to call.
	 * @param array $parameters			The parameters that should be passed.
	 * @param string[optional] $method	Which method should be used? Possible values are: GET, POST.
	 * @return array
	 */
	private function doCall($url, array $parameters = null, $method = 'GET')
	{
		// redefine
		$url = (string) $url;
		$method = (string) $method;
		$options = array();

		// init var
		$queryString = '';

		// through GET
		if($method == 'GET')
		{
			// remove POST-specific stuff
			unset($options[CURLOPT_POST]);
			unset($options[CURLOPT_POSTFIELDS]);

			// append to url
			if(!empty($parameters)) $url .= '?' . http_build_query($parameters, null, '&');
		}

		// through POST
		elseif($method == 'POST')
		{
			$options[CURLOPT_POST] = true;
			$options[CURLOPT_POSTFIELDS] = self::flattenArray($parameters);
		}

		// prepend
		$url = self::API_URL . '/' . self::API_VERSION . '/' . $url;

		// set options
		$options[CURLOPT_URL] = $url;
		$options[CURLOPT_PORT] = self::API_PORT;
		$options[CURLOPT_USERAGENT] = $this->getUserAgent();
		$options[CURLOPT_FOLLOWLOCATION] = true;
		$options[CURLOPT_SSL_VERIFYPEER] = false;
		$options[CURLOPT_SSL_VERIFYHOST] = false;
		$options[CURLOPT_RETURNTRANSFER] = true;
		$options[CURLOPT_TIMEOUT] = (int) $this->getTimeOut();
		$options[CURLOPT_HTTPAUTH] = CURLAUTH_BASIC;
		$options[CURLOPT_USERPWD] = $this->getUsername() . ':' . $this->getPassword();

		// init
		$this->curl = curl_init();

		// set options
		curl_setopt_array($this->curl, $options);

		// execute
		$response = curl_exec($this->curl);
		$headers = curl_getinfo($this->curl);

		// fetch errors
		$errorNumber = curl_errno($this->curl);
		$errorMessage = curl_error($this->curl);

		// check status code
		if($headers['http_code'] >= 400)
		{
			// try to read JSON
			$json = @json_decode($response, true);

			// throw
			if($json !== null && $json !== false) throw new FactrException($response, $headers['http_code']);

			// unknow error
			throw new FactrException('Invalid response', $headers['http_code']);
		}

		// error?
		if($errorNumber != '') throw new FactrException($errorMessage, $errorNumber);

		// we expect JSON so decode it
		$json = @json_decode($response, true);

		// validate json
		if($json === false) throw new FactrException('Invalid JSON-response');

		// return
		return $json;
	}

	/**
	 * Flatten an array
	 *
	 * @param array $array				The array to flatten.
	 * @param mixed[optional] $prefix	A prefix that will be prepende before the key.
	 * @return array
	 */
	private static function flattenArray(array $array, $prefix = false)
	{
		// init var
		$return = array();

		// loop values
		foreach($array as $key => $value)
		{
			// build prefix
			$prefix = ($prefix !== false) ? $prefix . '[' . $key . ']' : $key;

			if(!is_array($value)) $return[$prefix] = $value;
			else $return = array_merge($return, self::flattenArray($value, $prefix));
		}

		return $return;
	}

	/**
	 * Get the password
	 *
	 * @return string
	 */
	public function getPassword()
	{
		return $this->password;
	}

	/**
	 * Get the timeout that will be used
	 *
	 * @return int
	 */
	public function getTimeOut()
	{
		return (int) $this->timeOut;
	}

	/**
	 * Get the useragent that will be used. Our version will be prepended to yours.
	 * It will look like: "PHP Factr/<version> <your-user-agent>"
	 *
	 * @return string
	 */
	public function getUserAgent()
	{
		return (string) 'PHP Factr/' . self::VERSION . ' ' . $this->userAgent;
	}

	/**
	 * Get the username
	 *
	 * @return string
	 */
	public function getUsername()
	{
		return $this->username;
	}

	/**
	 * Set the password
	 *
	 * @param string $password
	 */
	public function setPassword($password)
	{
		$this->password = (string) $password;
	}

	/**
	 * Set the timeout
	 * After this time the request will stop. You should handle any errors triggered by this.
	 *
	 * @param int $seconds	The timeout in seconds.
	 */
	public function setTimeOut($seconds)
	{
		$this->timeOut = (int) $seconds;
	}

	/**
	 * Set the user-agent for you application
	 * It will be appended to ours, the result will look like: "PHP Bitly/<version> <your-user-agent>"
	 *
	 * @param string $userAgent		Your user-agent, it should look like <app-name>/<app-version>.
	 */
	public function setUserAgent($userAgent)
	{
		$this->userAgent = (string) $userAgent;
	}

	/**
	 * Set the username
	 *
	 * @param string $username
	 */
	public function setUsername($username)
	{
		$this->username = (string) $username;
	}
}

/**
 * Factr Exception class
 *
 * @author	Tijs Verkoyen <php-factr@verkoyen.eu>
 */
class FactrException extends Exception
{
}