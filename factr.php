<?php

// @todo	convert prices into floats
// @todo	convert dates into timestamps

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
			$options[CURLOPT_POSTFIELDS] = http_build_query($parameters);
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
			if($json !== null && $json !== false)
			{
				// errors?
				if(isset($json['errors']))
				{
					$message = '';
					foreach($json['errors'] as $key => $value) $message .= $key . ': ' . implode(', ', $value) . "\n";

					throw new FactrException($message);
				}

				else throw new FactrException($response, $headers['http_code']);
			}

			// unknow error
			throw new FactrException('Invalid response (' . $headers['http_code'] . ')', $headers['http_code']);
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

// client methods
	/**
	 * Get a list of all the clients for the authenticating user.
	 *
	 * @return array
	 */
	public function clients()
	{
		return $this->doCall('clients.json');
	}

	/**
	 * Get all of the available information for a single client. You 'll need the id of the client.
	 *
	 * @param string $id	The id of the client.
	 * @return array
	 */
	public function clientsGet($id)
	{
		return $this->doCall('clients/' . (string) $id . '.json');
	}

	/**
	 * Create a new client.
	 *
	 * @param array $client		The information of the client.
	 * @return array|bool
	 */
	public function clientsCreate(array $client)
	{
		// build parameters
		$parameters['client'] = $client;

		// make the call
		$return = $this->doCall('clients.json', $parameters, 'POST');

		// @todo	this should be altered in the API
		if(isset($return['client'])) return $return['client'];

		return false;
	}

// invoice methods
	/**
	 * Get a list of all the invoices.
	 *
	 * @return array
	 */
	public function invoices()
	{
		return $this->doCall('invoices.json');
	}

	/**
	 * Get all of the available information for a single invoice. You 'll need the id of the invoice.
	 *
	 * @param string $id	The id of the invoice.
	 * @return array
	 */
	public function invoicesGet($id)
	{
		return $this->doCall('invoices/' . (string) $id . '.json');
	}

	/**
	 * Get all of the available information for a single invoice. You 'll need the iid of the invoice.
	 *
	 * @param string $iid	The iid of the invoice.
	 * @return array
	 */
	public function invoicesGetByIid($iid)
	{
		return $this->doCall('invoices/by_iid/' . (string) $iid . '.json');
	}

	/**
	 * Create a new invoice.
	 *
	 * @param array $invoice	The invoice information.
	 * @return array
	 */
	public function invoicesCreate(array $invoice)
	{
		// build parameters
		$parameters['invoice'] = $invoice;

		// make the call
		$return = $this->doCall('invoices.json', $parameters, 'POST');

		// @todo	this should be altered in the API
		if(isset($return['invoice'])) return $return['invoice'];

		return false;
	}

	/**
	 * Sending an invoice by mail.
	 *
	 * @param string $id
	 * @param string[optional] $to
	 * @param string[optional] $cc
	 * @param string[optional] $bcc
	 * @param string[optional] $subject
	 * @param string[optional] $text
	 * @return array
	 */
	public function invoiceSendByMail($id, $to = null, $cc = null, $bcc = null, $subject = null, $text = null)
	{
		// build parameters
		$parameters = array();
		if($to !== null) $parameters['mail']['to'] = (string) $to;
		if($cc !== null) $parameters['mail']['cc'] = (string) $cc;
		if($bcc !== null) $parameters['mail']['bcc'] = (string) $bcc;
		if($subject !== null) $parameters['mail']['subject'] = (string) $subject;
		if($text !== null) $parameters['mail']['text'] = (string) $text;

		// make the call
		return $this->doCall('invoices/' . (string) $id . '/mails.json', $parameters, 'POST');
	}

	/**
	 * Adding a payment to an invoice.
	 *
	 * @param string $id			The id of the invoice.
	 * @param float $amount			The amount payed.
	 * @param int[optional] $date	The date the payment was made (as a UNIX timestamp).
	 * @return array
	 */
	public function invoicesAddPayment($id, $amount, $date = null)
	{
		// build parameters
		$parameters['payment']['amount'] = (float) $amount;
		if($date != null) $parameters['payment']['date'] = date('Y-m-d\TH:i:s', $date);

		// make the call
		$return = $this->doCall('invoices/' . (string) $id . '/payments.json', $parameters, 'POST');

		// @todo	this should be altered in the API
		if(isset($return['payment'])) return $return['payment'];

		return false;
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