<?php

require_once '../../../autoload.php';
require_once 'config.php';
require_once 'PHPUnit/Framework/TestCase.php';

use \TijsVerkoyen\Factr\Factr;

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
        $this->factr = new Factr(USERNAME, PASSWORD);
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
     * Tests Factr->clients
     */
    public function testClients()
    {
        $response = $this->factr->clients();
        $this->assertInternalType('array', $response);
        foreach ($response as $item) {
            $this->assertInstanceOf('\TijsVerkoyen\Factr\Client\Client', $item);
        }
    }

    /**
     * Tests Factr->clientsCreate
     */
    public function testClientsCreate()
    {
        $address = new \TijsVerkoyen\Factr\Client\Address();
        $address->setStreet('Kerkstraat');
        $address->setNumber('108');
        $address->setZip('9050');
        $address->setCity('Gentbrugge');
        $address->setCountry('BE');

        $client = new \TijsVerkoyen\Factr\Client\Client();
        $client->setFirstName('Tijs');
        $client->setLastName('Verkoyen');
        $client->addEmail('php-factr@verkoyen.eu');
        $client->setBillingAddress($address);
        $client->setCompanyAddress($address);
        $client->setRemarks('Created by the Wrapperclass. ' . time());

        $response = $this->factr->clientsCreate($client);

        $this->assertInstanceOf('\TijsVerkoyen\Factr\Client\Client', $response);

        // @todo remove client
    }

    /**
     * Tests Factr->clientsGet
     */
    public function testClientsGet()
    {
        $address = new \TijsVerkoyen\Factr\Client\Address();
        $address->setStreet('Kerkstraat');
        $address->setNumber('108');
        $address->setZip('9050');
        $address->setCity('Gentbrugge');
        $address->setCountry('BE');

        $client = new \TijsVerkoyen\Factr\Client\Client();
        $client->setFirstName('Tijs');
        $client->setLastName('Verkoyen');
        $client->addEmail('php-factr@verkoyen.eu');
        $client->setBillingAddress($address);
        $client->setCompanyAddress($address);
        $client->setRemarks('Created by the Wrapperclass. ' . time());

        $response = $this->factr->clientsCreate($client);
        $response = $this->factr->clientsGet($response->getId());

        $this->assertInstanceOf('\TijsVerkoyen\Factr\Client\Client', $response);

        // @todo remove client
    }
}
