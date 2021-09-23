<?php

namespace SumoCoders\Factr;

use InvalidArgumentException;
use SumoCoders\Factr\Exception as FactrException;
use SumoCoders\Factr\Client\Client;
use SumoCoders\Factr\Invoice\Invoice;
use SumoCoders\Factr\Invoice\Mail;
use SumoCoders\Factr\Invoice\Payment;
use SumoCoders\Factr\Product\Product;
use Exception;

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
     */
    private string $apiToken;

    /**
     * cURL instance
     *
     * @var	resource
     */
    private $curl;

    /**
     * The timeout
     */
    private int $timeOut = 30;

    /**
     * The user agent
     */
    private string $userAgent;

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
     */
    private function decodeResponse(&$value, string $key)
    {
        // convert to float
        if (in_array($key, array('amount', 'price', 'total_without_vat', 'total_with_vat', 'total_vat', 'total'), true)) {
            $value = (float) $value;
        }
    }

    /**
     * Encode data for usage in the API
     *
     * @param  mixed $data
     */
    private function encodeData($data, array $array = [], ?string $prefix = null): array
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
     * @return array|bool|string
     * @throws FactrException
     */
    private function doCall(
        string $url,
        ?array $parameters = null,
        string $method = 'GET',
        bool $returnHeaders = false
    ) {
        // redefine
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
            $url .= '?' . http_build_query($parameters, null);
            $url = $this->removeIndexFromArrayParameters($url);
        } elseif ($method == 'POST') {
            $data = $this->encodeData($parameters);

            if ($this->areWeSendingAFile($data) === false) {
                $data = http_build_query($data, null);
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
            $url .= '?' . http_build_query($parameters, null);
        } elseif ($method == 'PUT') {
            $data = $this->encodeData($parameters);
            $data = http_build_query($data, null);
            $data = $this->removeIndexFromArrayParameters($data);

            $options[CURLOPT_POSTFIELDS] = $data;
            $options[CURLOPT_CUSTOMREQUEST] = 'PUT';
        } else throw new FactrException('Unsupported method (' . $method . ')');

        // prepend
        $url = self::API_URL . '/' . self::API_VERSION . '/' . $url;

        // set options
        $this->getCurlOptions($url);

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

                    throw new FactrException(trim($message));
                } else {
                    if(is_array($json) && array_key_exists('message', $json)) $response = $json['message'];
                    throw new FactrException($response, $headers['http_code']);
                }
            }

            // unknown error
            throw new FactrException('Invalid response (' . $headers['http_code'] . ')', $headers['http_code']);
        }

        // error?
        if($errorNumber != '') throw new FactrException($errorMessage, $errorNumber);

        // return the headers if needed
        if($returnHeaders) return $headers;

        if (stristr($url, '.pdf')) {
            // Return pdf contents immediately without tampering with them
            return $response;
        }

        // we expect JSON so decode it
        $json = @json_decode($response, true);

        // validate json
        if($json === false) throw new FactrException('Invalid JSON-response');

        // decode the response
        array_walk_recursive($json, array(__CLASS__, 'decodeResponse'));

        // return
        return $json;
    }

    /**
     * Detect from flattened parameter array if we're sending a file
     */
    private function areWeSendingAFile(array $parameters): bool
    {
        foreach ($parameters as $value) {
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
     * @return array|null|string|string[] the cleaned up query string or array
     */
    private function removeIndexFromArrayParameters($query)
    {
        return preg_replace('/%5B([0-9]*)%5D/iU', '%5B%5D', $query);
    }

    /**
     * Get the API token
     */
    public function getApiToken(): string
    {
        return $this->apiToken;
    }

    /**
     * Get the timeout that will be used
     */
    public function getTimeOut(): int
    {
        return $this->timeOut;
    }

    /**
     * Get the user agent that will be used. Our version will be prepended to yours.
     * It will look like: "PHP Factr/<version> <your-user-agent>"
     */
    public function getUserAgent(): string
    {
        return 'PHP Factr/' . self::VERSION . ' ' . $this->userAgent;
    }

    /**
     * Set the API token
     */
    public function setApiToken(string $apiToken): void
    {
        $this->apiToken = $apiToken;
    }

    /**
     * Set the timeout
     * After this time the request will stop. You should handle any errors triggered by this.
     */
    public function setTimeOut(int $seconds): void
    {
        $this->timeOut = $seconds;
    }

    /**
     * Set the user-agent for you application
     * It will be appended to ours, the result will look like: "PHP Factr/<version> <your-user-agent>"
     *
     * @param string $userAgent Your user-agent, it should look like <app-name>/<app-version>.
     */
    public function setUserAgent(string $userAgent): void
    {
        $this->userAgent = $userAgent;
    }

// account methods
    /**
     * Get an API token
     *
     * @throws FactrException
     */
    public function accountApiToken(string $username, string $password): string
    {
        $url = self::API_URL . '/' . self::API_VERSION . '/account/api_token.json';

        // set options
        $options = $this->getCurlOptions($url);
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
            throw new FactrException('Could\'t authenticate you');
        }

        // we expect JSON so decode it
        $json = @json_decode($response, true);

        // validate json
        if($json === false || !isset($json['api_token'])) throw new FactrException('Invalid JSON-response');

        // set the token
        $this->setApiToken($json['api_token']);

        // return
        return $json['api_token'];
    }

    private function getCurlOptions(string $url): array
    {
        $options[CURLOPT_URL] = $url;
        $options[CURLOPT_PORT] = self::API_PORT;
        $options[CURLOPT_USERAGENT] = $this->getUserAgent();
        $options[CURLOPT_FOLLOWLOCATION] = true;
        $options[CURLOPT_SSL_VERIFYPEER] = false;
        $options[CURLOPT_SSL_VERIFYHOST] = false;
        $options[CURLOPT_RETURNTRANSFER] = true;
        $options[CURLOPT_TIMEOUT] = $this->getTimeOut();

        return $options;
    }

// client methods
    /**
     * Get a list of all the clients for the authenticating user.
     *
     * @throws FactrException
     */
    public function clients(): array
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
     * @return Client|bool
     * @throws FactrException
     */
    public function clientsGet(string $id)
    {
        $rawData = $this->doCall('clients/' . $id . '.json');
        if(empty($rawData)) return false;

        return Client::initializeWithRawData($rawData);
    }

    /**
     * Get all clients that are linked to an email address
     *
     * @return Client[]|bool
     * @throws FactrException
     */
    public function clientsGetByEmail(string $email)
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
     * @throws FactrException
     */
    public function clientsCreate(Client $client): Client
    {
        $parameters['client'] = $client->toArray(true);
        $rawData = $this->doCall('clients.json', $parameters, 'POST');

        return Client::initializeWithRawData($rawData);
    }

    /**
     * Update an existing client
     *
     * @throws FactrException
     */
    public function clientsUpdate(string $id, Client $client): bool
    {
        $parameters['client'] = $client->toArray(true);
        $rawData = $this->doCall('clients/' . $id . '.json', $parameters, 'PUT', true);

        return ($rawData['http_code'] == 204);
    }

    /**
     * Check if country is European
     *
     * @throws FactrException
     */
    public function clientsIsEuropean(string $countryCode): bool
    {
        $parameters['country_code'] = $countryCode;
        $rawData = $this->doCall('clients/is_european.json', $parameters);

        return $rawData['european'];
    }

    /**
     * Delete a client
     *
     * @throws FactrException
     */
    public function clientsDelete(string $id): bool
    {
        $rawData = $this->doCall('clients/' . $id . '.json', null, 'DELETE', true);

        return ($rawData['http_code'] == 204);
    }

    /**
     * Disable client in favour of another client
     *
     * @throws FactrException
     */
    public function clientsDisable(string $id, string $replacedById): bool
    {
        $parameters['replaced_by_id'] = $replacedById;
        $rawData = $this->doCall('clients/' . $id . '/disable.json', $parameters, 'POST', true);

        return ($rawData['http_code'] == 201);
    }

    /**
     * Get the invoices for a client
     *
     * @throws Exception
     */
    public function clientsInvoices(int $id): array
    {
        $invoices = array();
        $rawData = $this->doCall('clients/' . $id . '/invoices.json');
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
     * @throws Exception
     */
    public function invoices(?array $filters = null): array
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
                        throw new InvalidArgumentException('Invalid filter');
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
     * @return Invoice|bool
     * @throws Exception
     */
    public function invoicesGet(string $id)
    {
        $rawData = $this->doCall('invoices/' . $id . '.json');
        if(empty($rawData)) return false;

        return Invoice::initializeWithRawData($rawData);
    }

    /**
     * Get the pdf for an invoice
     *
     * @return string|bool Raw PDF contents
     * @throws FactrException
     */
    public function invoicesGetAsPdf(string $id)
    {
        $rawData = $this->doCall('invoices/' . $id . '.pdf');

        if (empty($rawData)) {
            return false;
        }

        return $rawData;
    }

    /**
     * Get all of the available information for a single invoice. You 'll need the iid of the invoice.
     *
     * @return Invoice|bool
     * @throws Exception
     */
    public function invoicesGetByIid(string $iid)
    {
        $rawData = $this->doCall('invoices/by_iid/' . $iid . '.json');
        if(empty($rawData)) return false;

        return Invoice::initializeWithRawData($rawData);
    }

    /**
     * Create a new invoice.
     *
     * @throws Exception
     */
    public function invoicesCreate(Invoice $invoice): Invoice
    {
        if (($invoice->getVatException() && !$invoice->getVatDescription())
            || (!$invoice->getVatException() && $invoice->getVatDescription())) {
            throw new InvalidArgumentException('Vat exception and vat description are required if one of them is filled');
        }

        $parameters['invoice'] = $invoice->toArray(true);
        $rawData = $this->doCall('invoices.json', $parameters, 'POST');

        return Invoice::initializeWithRawData($rawData);
    }

    /**
     * Create a new credit note on invoice.
     *
     * @throws Exception
     */
    public function invoicesCreateCreditNote(string $id, Invoice $creditNote): Invoice
    {
        $parameters['credit_note'] = $creditNote->toArray(true);
        $rawData = $this->doCall('invoices/' . $id . '/credit_notes.json', $parameters, 'POST');

        return Invoice::initializeWithRawData($rawData);
    }

    /**
     * Update an existing invoice
     *
     * @throws FactrException
     */
    public function invoicesUpdate(string $id, Invoice $invoice): bool
    {
        $parameters['invoice'] = $invoice->toArray(true);
        $rawData = $this->doCall('invoices/' . $id . '.json', $parameters, 'PUT', true);

        return ($rawData['http_code'] == 204);
    }

    /**
     * Delete an invoice
     *
     * @throws FactrException
     */
    public function invoicesDelete(int $id): bool
    {
        $rawData = $this->doCall('invoices/' . $id . '.json', null, 'DELETE', true);

        return ($rawData['http_code'] == 204);
    }

    /**
     * Sending an invoice by mail.
     *
     * @return Mail|bool
     * @throws FactrException
     */
    public function invoiceSendByMail(
        string $id,
        ?string $to = null,
        ?string $cc = null,
        ?string $bcc = null,
        ?string $subject = null,
        ?string $text = null
    ) {
        $parameters = array();
        if($to !== null) $parameters['mail']['to'] = $to;
        if($cc !== null) $parameters['mail']['cc'] = $cc;
        if($bcc !== null) $parameters['mail']['bcc'] = $bcc;
        if($subject !== null) $parameters['mail']['subject'] = $subject;
        if($text !== null) $parameters['mail']['text'] = $text;
        $rawData = $this->doCall('invoices/' . $id . '/mails.json', $parameters, 'POST');
        if(empty($rawData)) return false;

        return Mail::initializeWithRawData($rawData);
    }

    /**
     * Marking invoice as sent by mail.
     *
     * @throws FactrException
     */
    public function invoiceMarkAsSentByMail(string $id, string $email): void
    {
        $parameters = array();
        $parameters['by'] = 'mail';
        $parameters['to'] = $email;
        $this->doCall('invoices/' . $id . '/sent', $parameters, 'POST');
    }

    /**
     * Adding a payment to an invoice.
     *
     * @throws Exception
     */
    public function invoicesAddPayment(string $id, Payment $payment): Payment
    {
        $parameters['payment'] = $payment->toArray(true);
        $rawData = $this->doCall('invoices/' . $id . '/payments.json', $parameters, 'POST');

        return Payment::initializeWithRawData($rawData);
    }

    /**
     * Send a reminder for an invoice
     *
     * @param string $id The invoice id
     *
     * @throws FactrException
     */
    public function invoiceSendReminder(string $id): array
    {
        return $this->doCall('invoices/' . $id . '/reminders', array(), 'POST');
    }

    /**
     * Check if vat is required
     *
     * @throws FactrException
     */
    public function invoicesVatRequired(string $countryCode, bool $isCompany): bool
    {
        $parameters['country_code'] = $countryCode;
        $parameters['company'] = $isCompany;
        $rawData = $this->doCall('invoices/vat_required.json', $parameters);

        return $rawData['vat_required'];
    }

    /**
     * Check if valid vat number
     *
     * @throws FactrException
     */
    public function isValidVat(string $vatNumber): bool
    {
        $parameters['vat'] = $vatNumber;
        $rawData = $this->doCall('vat/verify.json', $parameters);

        return $rawData['valid'];
    }

    /**
     * Upload a CODA file and let Factr interpret it
     *
     * @throws FactrException
     */
    public function uploadCodaFile(string $filePath): array
    {
        $parameters = array(
            'file' => '@' . $filePath,
            'file_type' => 'coda',
        );

        return $this->doCall('payments/process_file.json', $parameters, 'POST');
    }

    /**
     * @throws FactrException
     */
    public function products(): array
    {
        $products = array();
        $rawData = $this->doCall('products.json');

        if (!empty($rawData)) {
            foreach ($rawData as $data) {
                $products[] = Product::initializeWithRawData($data);
            }
        }

        return $products;
    }

    /**
     * @return Product|bool
     * @throws FactrException
     */
    public function productsGet(int $id)
    {
        $rawData = $this->doCall('products/' . $id . '.json');

        if (empty($rawData)) {
            return false;
        }

        return Product::initializeWithRawData($rawData);
    }

    /**
     * @throws FactrException
     */
    public function productsCreate(Product $product): Product
    {
        $parameters['product'] = $product->toArray();

        $rawData = $this->doCall('products.json', $parameters, 'POST');

        return Product::initializeWithRawData($rawData);
    }
}
