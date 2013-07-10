<?php

require_once '../../../autoload.php';
require_once 'config.php';
require_once 'PHPUnit/Framework/TestCase.php';

use \SumoCoders\Factr\Factr;

/**
 * test case.
 */
class FactrTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Factr
     */
    private $factr;

    /**
     * Prepares the environment before running a test.
     */
    protected function setUp()
    {
        parent::setUp();
        $this->factr = new Factr();
        $this->factr->setApiToken(API_TOKEN);
    }

    /**
     * Cleans up the environment after running a test.
     */
    protected function tearDown()
    {
        $this->factr = null;
        parent::tearDown();
    }

    /**
     * Tests Factr->getTimeOut()
     */
    public function testGetTimeOut()
    {
        $this->factr->setTimeOut(5);
        $this->assertEquals(5, $this->factr->getTimeOut());
    }

    /**
     * Tests Factr->getUserAgent()
     */
    public function testGetUserAgent()
    {
        $this->factr->setUserAgent('testing/1.0.0');
        $this->assertEquals('PHP Factr/' . Factr::VERSION . ' testing/1.0.0', $this->factr->getUserAgent());
    }

    /**
     * Tests Factr->accountApiToken()
     */
    public function testAccountApiToken()
    {
        $response = $this->factr->accountApiToken(USERNAME, PASSWORD);
        $this->assertInternalType('string', $response);
    }

    /**
     * Tests Factr->clients
     */
    public function testClients()
    {
        $response = $this->factr->clients();
        $this->assertInternalType('array', $response);
        foreach ($response as $item) {
            $this->assertInstanceOf('\SumoCoders\Factr\Client\Client', $item);
        }
    }

    /**
     * Tests Factr->clientsCreate
     */
    public function testClientsCreate()
    {
        $address = new \SumoCoders\Factr\Client\Address();
        $address->setStreet('Kerkstraat');
        $address->setNumber('108');
        $address->setZip('9050');
        $address->setCity('Gentbrugge');
        $address->setCountry('BE');

        $client = new \SumoCoders\Factr\Client\Client();
        $client->setFirstName('Tijs');
        $client->setLastName('Verkoyen');
        $client->addEmail('php-factr@verkoyen.eu');
        $client->setBillingAddress($address);
        $client->setCompanyAddress($address);
        $client->setRemarks('Created by the Wrapperclass. ' . time());

        $response = $this->factr->clientsCreate($client);

        $this->assertInstanceOf('\SumoCoders\Factr\Client\Client', $response);

        // cleanup
        $this->factr->clientsDelete($response->getId());
    }

    /**
     * Tests Factr->clientsUpdate
     */
    public function testClientsUpdate()
    {
        $address = new \SumoCoders\Factr\Client\Address();
        $address->setStreet('Kerkstraat');
        $address->setNumber('108');
        $address->setZip('9050');
        $address->setCity('Gentbrugge');
        $address->setCountry('BE');

        $client = new \SumoCoders\Factr\Client\Client();
        $client->setFirstName('Tijs');
        $client->setLastName('Verkoyen');
        $client->addEmail('php-factr@verkoyen.eu');
        $client->setBillingAddress($address);
        $client->setCompanyAddress($address);
        $client->setRemarks('Created by the Wrapperclass. ' . time());

        $response = $this->factr->clientsCreate($client);
        $id = $response->getId();
        $client->setRemarks('Updated by the Wrappercass. ' . time());
        $response = $this->factr->clientsUpdate($id, $client);

        $this->assertTrue($response);

        // cleanup
        $this->factr->clientsDelete($id);
    }

    /**
     * Tests Factr->clientsDelete
     */
    public function testClientsDelete()
    {
        $address = new \SumoCoders\Factr\Client\Address();
        $address->setStreet('Kerkstraat');
        $address->setNumber('108');
        $address->setZip('9050');
        $address->setCity('Gentbrugge');
        $address->setCountry('BE');

        $client = new \SumoCoders\Factr\Client\Client();
        $client->setFirstName('Tijs');
        $client->setLastName('Verkoyen');
        $client->addEmail('php-factr@verkoyen.eu');
        $client->setBillingAddress($address);
        $client->setCompanyAddress($address);
        $client->setRemarks('Created by the Wrapperclass. ' . time());

        $response = $this->factr->clientsCreate($client);
        $response = $this->factr->clientsDelete($response->getId());

        $this->assertTrue($response);
    }

    /**
     * Tests Factr->clientsGet
     */
    public function testClientsGet()
    {
        $address = new \SumoCoders\Factr\Client\Address();
        $address->setStreet('Kerkstraat');
        $address->setNumber('108');
        $address->setZip('9050');
        $address->setCity('Gentbrugge');
        $address->setCountry('BE');

        $client = new \SumoCoders\Factr\Client\Client();
        $client->setFirstName('Tijs');
        $client->setLastName('Verkoyen');
        $client->addEmail('php-factr@verkoyen.eu');
        $client->setBillingAddress($address);
        $client->setCompanyAddress($address);
        $client->setRemarks('Created by the Wrapperclass. ' . time());

        $response = $this->factr->clientsCreate($client);
        $id = $response->getId();
        $response = $this->factr->clientsGet($id);

        $this->assertInstanceOf('\SumoCoders\Factr\Client\Client', $response);

        // cleanup
        $this->factr->clientsDelete($id);
    }

    /**
     * Tests Factr->invoices
     */
    public function testInvoices()
    {
        $response = $this->factr->invoices();
        $this->assertInternalType('array', $response);
        foreach ($response as $item) {
            $this->assertInstanceOf('\SumoCoders\Factr\Invoice\Invoice', $item);
        }
    }

    /**
     * Tests Factr->invoicesAddPayment
     */
    public function testInvoicesAddPayment()
    {
        $address = new \SumoCoders\Factr\Client\Address();
        $address->setStreet('Kerkstraat');
        $address->setNumber('108');
        $address->setZip('9050');
        $address->setCity('Gentbrugge');
        $address->setCountry('BE');

        $client = new \SumoCoders\Factr\Client\Client();
        $client->setFirstName('Tijs');
        $client->setLastName('Verkoyen');
        $client->addEmail('php-factr@verkoyen.eu');
        $client->setBillingAddress($address);
        $client->setCompanyAddress($address);
        $client->setRemarks('Created by the Wrapperclass. ' . time());

        $client = $this->factr->clientsCreate($client);

        $item = new \SumoCoders\Factr\Invoice\Item();
        $item->setDescription('just an item');
        $item->setPrice(123.45);
        $item->setAmount(67);
        $item->setVat(21);

        $invoice = new \SumoCoders\Factr\Invoice\Invoice();
        $invoice->setDescription('Created by the Wrapperclass. ' . time());
        $invoice->setClient($this->factr->clientsGet($client->getId()));
        $invoice->addItem($item);

        $invoice = $this->factr->invoicesCreate($invoice);

        $payment = new \SumoCoders\Factr\Invoice\Payment();
        $payment->setAmount(10);

        $response = $this->factr->invoicesAddPayment($invoice->getId(), $payment);
        $this->assertInstanceOf('\SumoCoders\Factr\Invoice\Payment', $response);

        // cleanup
        $this->factr->invoicesDelete($invoice->getId());
        $this->factr->clientsDelete($client->getId());
    }

    /**
     * Tests Factr->invoicesCreate
     */
    public function testInvoicesCreate()
    {
        $address = new \SumoCoders\Factr\Client\Address();
        $address->setStreet('Kerkstraat');
        $address->setNumber('108');
        $address->setZip('9050');
        $address->setCity('Gentbrugge');
        $address->setCountry('BE');

        $client = new \SumoCoders\Factr\Client\Client();
        $client->setFirstName('Tijs');
        $client->setLastName('Verkoyen');
        $client->addEmail('php-factr@verkoyen.eu');
        $client->setBillingAddress($address);
        $client->setCompanyAddress($address);
        $client->setRemarks('Created by the Wrapperclass. ' . time());

        $client = $this->factr->clientsCreate($client);

        $item = new \SumoCoders\Factr\Invoice\Item();
        $item->setDescription('just an item');
        $item->setPrice(123.45);
        $item->setAmount(67);
        $item->setVat(21);

        $invoice = new \SumoCoders\Factr\Invoice\Invoice();
        $invoice->setDescription('Created by the Wrapperclass. ' . time());
        $invoice->setClient($this->factr->clientsGet($client->getId()));
        $invoice->addItem($item);

        $invoice = $this->factr->invoicesCreate($invoice);
        $this->assertInstanceOf('\SumoCoders\Factr\Invoice\Invoice', $invoice);

        // cleanup
        $this->factr->invoicesDelete($invoice->getId());
        $this->factr->clientsDelete($client->getId());
    }

    /**
     * Tests Factr->invoicesDelete
     */
    public function testInvoicesDelete()
    {
        $address = new \SumoCoders\Factr\Client\Address();
        $address->setStreet('Kerkstraat');
        $address->setNumber('108');
        $address->setZip('9050');
        $address->setCity('Gentbrugge');
        $address->setCountry('BE');

        $client = new \SumoCoders\Factr\Client\Client();
        $client->setFirstName('Tijs');
        $client->setLastName('Verkoyen');
        $client->addEmail('php-factr@verkoyen.eu');
        $client->setBillingAddress($address);
        $client->setCompanyAddress($address);
        $client->setRemarks('Created by the Wrapperclass. ' . time());

        $client = $this->factr->clientsCreate($client);

        $item = new \SumoCoders\Factr\Invoice\Item();
        $item->setDescription('just an item');
        $item->setPrice(123.45);
        $item->setAmount(67);
        $item->setVat(21);

        $invoice = new \SumoCoders\Factr\Invoice\Invoice();
        $invoice->setDescription('Created by the Wrapperclass. ' . time());
        $invoice->setClient($this->factr->clientsGet($client->getId()));
        $invoice->addItem($item);

        $invoice = $this->factr->invoicesCreate($invoice);
        $response = $this->factr->invoicesDelete($invoice->getId());

        $this->assertTrue($response);

        // cleanup
        $this->factr->clientsDelete($client->getId());
    }

    /**
     * Tests Factr->invoiceSendByMail
     */
    public function tesInvoiceSendByMail()
    {
        $address = new \SumoCoders\Factr\Client\Address();
        $address->setStreet('Kerkstraat');
        $address->setNumber('108');
        $address->setZip('9050');
        $address->setCity('Gentbrugge');
        $address->setCountry('BE');

        $client = new \SumoCoders\Factr\Client\Client();
        $client->setFirstName('Tijs');
        $client->setLastName('Verkoyen');
        $client->addEmail('php-factr@verkoyen.eu');
        $client->setBillingAddress($address);
        $client->setCompanyAddress($address);
        $client->setRemarks('Created by the Wrapperclass. ' . time());

        $response = $this->factr->clientsCreate($client);

        $item = new \SumoCoders\Factr\Invoice\Item();
        $item->setDescription('just an item');
        $item->setPrice(123.45);
        $item->setAmount(67);
        $item->setVat(21);

        $invoice = new \SumoCoders\Factr\Invoice\Invoice();
        $invoice->setDescription('Created by the Wrapperclass. ' . time());
        $invoice->setClient($this->factr->clientsGet($response->getId()));
        $invoice->addItem($item);

        $response = $this->factr->invoicesCreate($invoice);

        $response = $this->factr->invoiceSendByMail($response->getIid());
        $this->assertInstanceOf('\SumoCoders\Factr\Invoice\Mail', $response);

        // cleanup
        $this->factr->invoicesDelete($invoice->getId());
        $this->factr->clientsDelete($client->getId());
    }

    /**
     * Tests Factr->invoicesGet
     */
    public function testInvoicesGet()
    {
        $address = new \SumoCoders\Factr\Client\Address();
        $address->setStreet('Kerkstraat');
        $address->setNumber('108');
        $address->setZip('9050');
        $address->setCity('Gentbrugge');
        $address->setCountry('BE');

        $client = new \SumoCoders\Factr\Client\Client();
        $client->setFirstName('Tijs');
        $client->setLastName('Verkoyen');
        $client->addEmail('php-factr@verkoyen.eu');
        $client->setBillingAddress($address);
        $client->setCompanyAddress($address);
        $client->setRemarks('Created by the Wrapperclass. ' . time());

        $client = $this->factr->clientsCreate($client);

        $item = new \SumoCoders\Factr\Invoice\Item();
        $item->setDescription('just an item');
        $item->setPrice(123.45);
        $item->setAmount(67);
        $item->setVat(21);

        $invoice = new \SumoCoders\Factr\Invoice\Invoice();
        $invoice->setDescription('Created by the Wrapperclass. ' . time());
        $invoice->setClient($this->factr->clientsGet($client->getId()));
        $invoice->addItem($item);

        $invoice = $this->factr->invoicesCreate($invoice);

        $response = $this->factr->invoicesGet($invoice->getId());
        $this->assertInstanceOf('\SumoCoders\Factr\Invoice\Invoice', $response);

        // cleanup
        $this->factr->invoicesDelete($invoice->getId());
        $this->factr->clientsDelete($client->getId());
    }

    /**
     * Tests Factr->invoicesGet
     */
    public function testInvoicesGetByIid()
    {
        $address = new \SumoCoders\Factr\Client\Address();
        $address->setStreet('Kerkstraat');
        $address->setNumber('108');
        $address->setZip('9050');
        $address->setCity('Gentbrugge');
        $address->setCountry('BE');

        $client = new \SumoCoders\Factr\Client\Client();
        $client->setFirstName('Tijs');
        $client->setLastName('Verkoyen');
        $client->addEmail('php-factr@verkoyen.eu');
        $client->setBillingAddress($address);
        $client->setCompanyAddress($address);
        $client->setRemarks('Created by the Wrapperclass. ' . time());

        $client = $this->factr->clientsCreate($client);

        $item = new \SumoCoders\Factr\Invoice\Item();
        $item->setDescription('just an item');
        $item->setPrice(123.45);
        $item->setAmount(67);
        $item->setVat(21);

        $invoice = new \SumoCoders\Factr\Invoice\Invoice();
        $invoice->setDescription('Created by the Wrapperclass. ' . time());
        $invoice->setClient($this->factr->clientsGet($client->getId()));
        $invoice->addItem($item);

        $invoice = $this->factr->invoicesCreate($invoice);
        $this->factr->invoiceSendByMail($invoice->getId());
        $invoice = $this->factr->invoicesGet($invoice->getId());

        $response = $this->factr->invoicesGetByIid($invoice->getIid());
        $this->assertInstanceOf('\SumoCoders\Factr\Invoice\Invoice', $response);

        // cleanup
        $this->factr->invoicesDelete($invoice->getId());
        $this->factr->clientsDelete($client->getId());
    }
}
