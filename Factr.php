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
    const API_URL = 'https://app.defactuur.be/api';

    // port for the factr-API
    const API_PORT = 443;

    // version of the API
    const API_VERSION = 'v1';

    // current version
    const VERSION = '2.5.1';

    /**
     * The token to use
     *
     * @var string
     */
    private $apiToken;

    /**
     * cURL instance
     *
     * @var	resource
     */
    private $curl;

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

// class methods
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
     * @param  string           $url           The URL to call.
     * @param  array[optional]  $parameters    The parameters that should be passed.
     * @param  string[optional] $method        Which method should be used? Possible values are: GET, POST, DELETE, PUT.
     * @param  bool[optional]   $returnHeaders Return the headers instead of the data?
     * @return array
     */
    private function doCall($url, array $parameters = null, $method = 'GET', $returnHeaders = false)
    {
        // redefine
        $url = (string) $url;
        $method = (string) $method;
        $options = array();

        // add credentials
        $parameters['api_key'] = $this->getApiToken();

        // through GET
        if ($method == 'GET') {
            // remove POST-specific stuff
            unset($options[CURLOPT_HTTPHEADER]);
            unset($options[CURLOPT_POST]);
            unset($options[CURLOPT_POSTFIELDS]);

            // build url
            $url .= '?' . http_build_query($parameters, null, '&');
            $url = $this->removeIndexFromArrayParameters($url);
        } elseif ($method == 'POST') {
            $data = $this->encodeData($parameters);

            if ($this->areWeSendingAFile($data) === false) {
                $data = http_build_query($data, null, '&');
                $data = $this->removeIndexFromArrayParameters($data);
            }

            $options[CURLOPT_POST] = true;
            $options[CURLOPT_POSTFIELDS] = $data;
        } elseif ($method == 'DELETE') {
            unset($options[CURLOPT_HTTPHEADER]);
            unset($options[CURLOPT_POST]);
            unset($options[CURLOPT_POSTFIELDS]);
            $options[CURLOPT_CUSTOMREQUEST] = 'DELETE';

            // build url
            $url .= '?' . http_build_query($parameters, null, '&');
        } elseif ($method == 'PUT') {
            $data = $this->encodeData($parameters);
            $data = http_build_query($data, null, '&');
            $data = $this->removeIndexFromArrayParameters($data);

            $options[CURLOPT_POSTFIELDS] = $data;
            $options[CURLOPT_CUSTOMREQUEST] = 'PUT';
        } else throw new Exception('Unsupported method (' . $method . ')');

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
                if (is_array($json) && array_key_exists('errors', $json)) {
                    $message = '';
                    foreach($json['errors'] as $key => $value) $message .= $key . ': ' . implode(', ', $value) . "\n";

                    throw new Exception(trim($message));
                } else {
                    if(is_array($json) && array_key_exists('message', $json)) $response = $json['message'];
                    throw new Exception($response, $headers['http_code']);
                }
            }

            // unknown error
            throw new Exception('Invalid response (' . $headers['http_code'] . ')', $headers['http_code']);
        }

        // error?
        if($errorNumber != '') throw new Exception($errorMessage, $errorNumber);

        // return the headers if needed
        if($returnHeaders) return $headers;

        if (stristr($url, '.pdf')) {
            // Return pdf contents immediately without tampering with them
            return $response;
        }

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
     * Detect from flattened parameter array if we're sending a file
     *
     * @param array $parameters The flattened parameter array
     *
     * @return bool
     */
    private function areWeSendingAFile($parameters)
    {
        foreach ($parameters as $key => $value) {
            if (substr($value, 0, 1) === '@') {
                return true;
            }
        }

        return false;
    }

    /**
     * Remove indexes from http array parameters
     *
     * The Factr application doesn't like numerical indexes in http parameters too much.
     * We'll just remove them.
     *
     * ?foo[1]=bar becomes ?foo[]=bar
     *
     * @param mixed $query The query string or flattened array
     *
     * @return mixed the cleaned up query string or array
     */
    private function removeIndexFromArrayParameters($query)
    {
        return preg_replace('/%5B([0-9]*)%5D/iU', '%5B%5D', $query);
    }

    /**
     * Get the API token
     *
     * @return string
     */
    public function getApiToken()
    {
        return $this->apiToken;
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
     * Set the API token
     *
     * @param string $apiToken
     */
    public function setApiToken($apiToken)
    {
        $this->apiToken = $apiToken;
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

// account methods
    /**
     * Get an API token
     *
     * @param  string    $username Your username
     * @param  string    $password Tour password
     * @return string
     * @throws Exception
     */
    public function accountApiToken($username, $password)
    {
        $url = self::API_URL . '/' . self::API_VERSION . '/account/api_token.json';

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
        $options[CURLOPT_USERPWD] = $username . ':' . $password;

        // init
        $this->curl = curl_init();

        // set options
        curl_setopt_array($this->curl, $options);

        // execute
        $response = curl_exec($this->curl);
        $headers = curl_getinfo($this->curl);

        // check status code
        if ($headers['http_code'] != 200) {
            throw new Exception('Could\'t authenticate you');
        }

        // we expect JSON so decode it
        $json = @json_decode($response, true);

        // validate json
        if($json === false || !isset($json['api_token'])) throw new Exception('Invalid JSON-response');

        // set the token
        $this->setApiToken($json['api_token']);

        // return
        return $json['api_token'];
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
     * Get all clients that are linked to an email address
     *
     * @param  string $email The email of the client.
     * @return Client[]
     */
    public function clientsGetByEmail($email)
    {
        $rawData = $this->doCall('clients.json', array('email' => $email));
        if (empty($rawData)) {
            return false;
        }

        return array_map(
            function (array $clientData) {
                return Client::initializeWithRawData($clientData);
            },
            $rawData
        );
    }

    /**
     * Create a new client.
     *
     * @param  Client      $client The information of the client.
     * @return Client|bool
     */
    public function clientsCreate(Client $client)
    {
        $parameters['client'] = $client->toArray(true);
        $rawData = $this->doCall('clients.json', $parameters, 'POST');

        return Client::initializeWithRawData($rawData);
    }

    /**
     * Update an existing client
     *
     * @param  string $id     The id of the client.
     * @param  Client $client The information of the client.
     * @return bool
     */
    public function clientsUpdate($id, Client $client)
    {
        $parameters['client'] = $client->toArray(true);
        $rawData = $this->doCall('clients/' . (string) $id . '.json', $parameters, 'PUT', true);

        return ($rawData['http_code'] == 204);
    }

    /**
     * Check if country is European
     *
     * @param $countryCode
     * @return bool
     */
    public function clientsIsEuropean($countryCode)
    {
        $parameters['country_code'] = $countryCode;
        $rawData = $this->doCall('clients/is_european.json', $parameters);

        return $rawData['european'];
    }

    /**
     * Delete a client
     *
     * @param  string $id The id of the client.
     * @return bool
     */
    public function clientsDelete($id)
    {
        $rawData = $this->doCall('clients/' . (string) $id . '.json', null, 'DELETE', true);

        return ($rawData['http_code'] == 204);
    }

    /**
     * Disable client in favour of another client
     *
     * @param $id
     * @param $replacedById
     *
     * @return bool
     */
    public function clientsDisable($id, $replacedById)
    {
        $parameters['replaced_by_id'] = $replacedById;
        $rawData = $this->doCall('clients/' . (string) $id . '/disable.json', $parameters, 'POST', true);

        return ($rawData['http_code'] == 201);
    }

    /**
     * Get the invoices for a client
     *
     * @param  int   $id
     * @return array
     */
    public function clientsInvoices($id)
    {
        $invoices = array();
        $rawData = $this->doCall('clients/' . (string) $id . '/invoices.json');
        if (!empty($rawData)) {
            foreach ($rawData as $data) {
                $invoices[] = Invoice::initializeWithRawData($data);
            }
        }

        return $invoices;
    }

// invoice methods
    /**
     * Get a list of all the invoices.
     *
     * @param array $filters A list of filters
     *
     * @return array
     */
    public function invoices(array $filters = null)
    {
        $parameters = null;

        if (!empty($filters)) {
            $allowedFilters = array(
                'sent',
                'unpaid',
                'paid',
                'reminder_sent',
                'partially_paid',
                'unset',
                'juridicial_proceedings',
                'late'
            );

            array_walk(
                $filters,
                function($filter) use ($allowedFilters) {
                    if (!in_array($filter, $allowedFilters)) {
                        throw new \InvalidArgumentException('Invalid filter');
                    }
                }
            );

            $parameters = array('filters' => $filters);
        }

        $invoices = array();
        $rawData = $this->doCall('invoices.json', $parameters);
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
     * Get the pdf for an invoice
     *
     * @param string $id The id of the invoice.
     *
     * @return string Raw PDF contents
     */
    public function invoicesGetAsPdf($id)
    {
        $rawData = $this->doCall('invoices/' . (string) $id . '.pdf');

        if (empty($rawData)) {
            return false;
        }

        return $rawData;
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
        $parameters['invoice'] = $invoice->toArray(true);
        $rawData = $this->doCall('invoices.json', $parameters, 'POST');

        return Invoice::initializeWithRawData($rawData);
    }

    /**
     * Create a new credit note on invoice.
     *
     * @param  string  $id
     * @param  Invoice $creditNote The credit note information.
     * @return Invoice
     */
    public function invoicesCreateCreditNote($id, Invoice $creditNote)
    {
        $parameters['credit_note'] = $creditNote->toArray(true);
        $rawData = $this->doCall('invoices/' . (string) $id . '/credit_notes.json', $parameters, 'POST');

        return Invoice::initializeWithRawData($rawData);
    }

    /**
     * Update an existing invoice
     *
     * @param  string  $id
     * @param  Invoice $invoice
     * @return bool
     */
    public function invoicesUpdate($id, Invoice $invoice)
    {
        $parameters['invoice'] = $invoice->toArray(true);
        $rawData = $this->doCall('invoices/' . (string) $id . '.json', $parameters, 'PUT', true);

        return ($rawData['http_code'] == 204);
    }

    /**
     * Delete an invoice
     *
     * @param  int  $id
     * @return bool
     */
    public function invoicesDelete($id)
    {
        $rawData = $this->doCall('invoices/' . (string) $id . '.json', null, 'DELETE', true);

        return ($rawData['http_code'] == 204);
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
        $parameters = array();
        if($to !== null) $parameters['mail']['to'] = (string) $to;
        if($cc !== null) $parameters['mail']['cc'] = (string) $cc;
        if($bcc !== null) $parameters['mail']['bcc'] = (string) $bcc;
        if($subject !== null) $parameters['mail']['subject'] = (string) $subject;
        if($text !== null) $parameters['mail']['text'] = (string) $text;
        $rawData = $this->doCall('invoices/' . (string) $id . '/mails.json', $parameters, 'POST');
        if(empty($rawData)) return false;

        return Mail::initializeWithRawData($rawData);
    }

    /**
     * Adding a payment to an invoice.
     *
     * @param  string  $id
     * @param  Payment $payment
     * @return Payment
     */
    public function invoicesAddPayment($id, Payment $payment)
    {
        $parameters['payment'] = $payment->toArray(true);
        $rawData = $this->doCall('invoices/' . (string) $id . '/payments.json', $parameters, 'POST');

        return Payment::initializeWithRawData($rawData);
    }

    /**
     * Send a reminder for an invoice
     *
     * @param string $id The invoice id
     *
     * @return array
     */
    public function invoiceSendReminder($id)
    {
        $rawData = $this->doCall('invoices/' . (string) $id . '/reminders', array(), 'POST');

        return $rawData;
    }

    /**
     * Check if vat is required
     *
     * @param string $countryCode
     * @param boolean $isCompany
     * @return boolean
     */
    public function invoicesVatRequired($countryCode, $isCompany)
    {
        $parameters['country_code'] = $countryCode;
        $parameters['company'] = (bool) $isCompany;
        $rawData = $this->doCall('invoices/vat_required.json', $parameters);

        return $rawData['vat_required'];
    }

    /**
     * Check if valid vat number
     *
     * @param string $vatNumber
     * @return boolean
     */
    public function isValidVat($vatNumber)
    {
        $parameters['vat'] = $vatNumber;
        $rawData = $this->doCall('vat/verify.json', $parameters);

        return $rawData['valid'];
    }

    /**
     * Upload a CODA file and let Factr interpret it
     *
     * @param string $filePath The file path
     *
     * @return array
     */
    public function uploadCodaFile($filePath)
    {
        $parameters = array(
            'file' => '@' . $filePath,
            'file_type' => 'coda',
        );

        $rawData = $this->doCall('payments/process_file.json', $parameters, 'POST');

        return $rawData;
    }
}
