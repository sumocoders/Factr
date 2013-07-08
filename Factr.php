<?php
namespace SumoCoders\Factr;

use SumoCoders\Factr\Exception;
use SumoCoders\Factr\Client\Client;
use SumoCoders\Factr\Invoice\Invoice;
use SumoCoders\Factr\Invoice\Mail;
use SumoCoders\Factr\Invoice\Payment;

/**
 * Factr class
 *
 * @author		Tijs Verkoyen <php-factr@verkoyen.eu>
 * @version		2.0.0
 * @copyright	Copyright (c), Tijs Verkoyen. All rights reserved.
 * @license		BSD License
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
    const VERSION = '2.0.0';

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
     * @param string $username The username to authenticate with.
     * @param string $password The password to use for authenticating.
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
        if($this->curl != null) curl_close($this->curl);
    }

    /**
     * Decode the response
     *
     * @param mixed  $value
     * @param string $key
     */
    private function decodeResponse(&$value, $key)
    {
        // convert to float
        if (in_array($key, array('amount', 'price', 'total_without_vat', 'total_with_vat', 'total_vat', 'total'), true)) {
            $value = (float) $value;
        }
    }

    /**
     * Encode data for usage in the API
     *
     * @param  mixed            $data
     * @param  array            $array
     * @param  string[optional] $prefix
     * @return array
     */
    private function encodeData($data, $array = array(), $prefix = null)
    {
        if (is_object($data)) {
            $data = get_object_vars($data);
        }

        foreach ($data as $key => $value) {
            if($value === null) continue;

            $k = isset($prefix) ? $prefix . '[' . $key . ']' : $key;
            if (is_array($value) || is_object($value)) {
                $array = $this->encodeData($value, $array, $k);
            } else {
                $array[$k] = $value;
            }
        }

        return $array;
    }

    /**
     * Make the call
     *
     * @param  string           $url        The URL to call.
     * @param  array            $parameters The parameters that should be passed.
     * @param  string[optional] $method     Which method should be used? Possible values are: GET, POST.
     * @return array
     */
    private function doCall($url, array $parameters = null, $method = 'GET')
    {
        // redefine
        $url = (string) $url;
        $method = (string) $method;
        $options = array();

        // through GET
        if ($method == 'GET') {
            // remove POST-specific stuff
            unset($options[CURLOPT_HTTPHEADER]);
            unset($options[CURLOPT_POST]);
            unset($options[CURLOPT_POSTFIELDS]);

            // append to url
            if(!empty($parameters)) $url .= '?' . http_build_query($parameters, null, '&');
        }

        // through POST
        elseif ($method == 'POST') {
            $options[CURLOPT_POST] = true;

            // the data should be encoded, and because we can't use numeric
            // keys for Rails, we replace them.
            $data = $this->encodeData($parameters);
            $data = http_build_query($data, null, '&');
            $data = preg_replace('/%5B([0-9]*)%5D/iU', '%5B%5D', $data);

            $options[CURLOPT_POSTFIELDS] = $data;
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
        if ($headers['http_code'] >= 400) {
            // try to read JSON
            $json = @json_decode($response, true);

            // throw
            if ($json !== null && $json !== false) {

                // errors?
                if (isset($json['errors'])) {
                    $message = '';
                    foreach($json['errors'] as $key => $value) $message .= $key . ': ' . implode(', ', $value) . "\n";

                    throw new Exception($message);
                } else throw new Exception($response, $headers['http_code']);
            }

            // unknown error
            throw new Exception('Invalid response (' . $headers['http_code'] . ')', $headers['http_code']);
        }

        // error?
        if($errorNumber != '') throw new Exception($errorMessage, $errorNumber);

        // we expect JSON so decode it
        $json = @json_decode($response, true);

        // validate json
        if($json === false) throw new Exception('Invalid JSON-response');

        // decode the response
        array_walk_recursive($json, array(__CLASS__, 'decodeResponse'));

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
     * Get the user agent that will be used. Our version will be prepended to yours.
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
     * @param int $seconds The timeout in seconds.
     */
    public function setTimeOut($seconds)
    {
        $this->timeOut = (int) $seconds;
    }

    /**
     * Set the user-agent for you application
     * It will be appended to ours, the result will look like: "PHP Factr/<version> <your-user-agent>"
     *
     * @param string $userAgent Your user-agent, it should look like <app-name>/<app-version>.
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
        $clients = array();
        $rawData = $this->doCall('clients.json');
        if (!empty($rawData)) {
            foreach ($rawData as $data) {
                $clients[] = Client::initializeWithRawData($data);
            }
        }

        return $clients;
    }

    /**
     * Get all of the available information for a single client. You 'll need the id of the client.
     *
     * @param  string $id The id of the client.
     * @return Client
     */
    public function clientsGet($id)
    {
        $rawData = $this->doCall('clients/' . (string) $id . '.json');
        if(empty($rawData)) return false;

        return Client::initializeWithRawData($rawData);
    }

    /**
     * Create a new client.
     *
     * @param  Client      $client The information of the client.
     * @return Client|bool
     */
    public function clientsCreate(Client $client)
    {
        // build parameters
        $parameters['client'] = $client->toArray();

        // make the call
        $rawData = $this->doCall('clients.json', $parameters, 'POST');

        if (isset($rawData['client'])) {
            return Client::initializeWithRawData($rawData['client']);
        }

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
        $invoices = array();
        $rawData = $this->doCall('invoices.json');
        if (!empty($rawData)) {
            foreach ($rawData as $data) {
                $invoices[] = Invoice::initializeWithRawData($data);
            }
        }

        return $invoices;
    }

    /**
     * Get all of the available information for a single invoice. You 'll need the id of the invoice.
     *
     * @param  string  $id The id of the invoice.
     * @return Invoice
     */
    public function invoicesGet($id)
    {
        $rawData = $this->doCall('invoices/' . (string) $id . '.json');
        if(empty($rawData)) return false;

        return Invoice::initializeWithRawData($rawData);
    }

    /**
     * Get all of the available information for a single invoice. You 'll need the iid of the invoice.
     *
     * @param  string $iid The iid of the invoice.
     * @return array
     */
    public function invoicesGetByIid($iid)
    {
        $rawData = $this->doCall('invoices/by_iid/' . (string) $iid . '.json');
        if(empty($rawData)) return false;

        return Invoice::initializeWithRawData($rawData);
    }

    /**
     * Create a new invoice.
     *
     * @param  Invoice $invoice The invoice information.
     * @return Invoice
     */
    public function invoicesCreate(Invoice $invoice)
    {
        // build parameters
        $parameters['invoice'] = $invoice->toArray();

        // make the call
        $rawData = $this->doCall('invoices.json', $parameters, 'POST');

        if (isset($rawData['invoice'])) {
            return Invoice::initializeWithRawData($rawData['invoice']);
        }

        return false;
    }

    /**
     * Sending an invoice by mail.
     *
     * @param  string           $id
     * @param  string[optional] $to
     * @param  string[optional] $cc
     * @param  string[optional] $bcc
     * @param  string[optional] $subject
     * @param  string[optional] $text
     * @return Mail
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
        $rawData = $this->doCall('invoices/' . (string) $id . '/mails.json', $parameters, 'POST');
        if(empty($rawData)) return false;

        return Mail::initializeWithRawData($rawData);
    }

    /**
     * Adding a payment to an invoice.
     *
     * @param  string              $id     The id of the invoice.
     * @param  float               $amount The amount payed.
     * @param  \DateTime[optional] $date   The date the payment was made (as a UNIX timestamp).
     * @return Payment
     */
    public function invoicesAddPayment($id, $amount, \DateTime $date = null)
    {
        // build parameters
        $parameters['payment']['amount'] = (float) $amount;
        if($date != null) $parameters['payment']['date'] = date('Y-m-d\TH:i:s', $date->getTimestamp());

        // make the call
        $rawData = $this->doCall('invoices/' . (string) $id . '/payments.json', $parameters, 'POST');

        // @todo	this should be altered in the API
        if (isset($rawData['payment'])) {
            return Payment::initializeWithRawData($rawData['payment']);
        }

        return false;
    }
}
