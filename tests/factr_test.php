<?php

require_once 'config.php';
require_once '../factr.php';

require_once 'PHPUnit/Framework/TestCase.php';

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
	 * Tests Factr->clients()
	 */
	public function testClients()
	{
		$var = $this->factr->clients();

		$this->assertType('array', $var);
		foreach($var as $row)
		{
			$this->assertArrayHasKey('id', $row);
			$this->assertArrayHasKey('cid', $row);
			$this->assertArrayHasKey('company_address', $row);
			$this->assertArrayHasKey('billing_address', $row);
			$this->assertArrayHasKey('email', $row);
		}
	}

	/**
	 * Tests Factr->clientsGet()
	 */
	public function testClientsGet()
	{
		$client = array(
				'email' => 'factr@verkoyen.eu',
				'first_name' => 'Tijs',
				'last_name' => 'Verkoyen',
				'company' => 'SumoCoders',
				'billing_address' => array(
						'street' => 'Kerkstraat',
						'number' => '108',
						'city' => '9050',
						'zip' => 'Gentbrugge',
						'country' => 'BE'
				),
				'company_address' => array(
						'street' => 'Kerkstraat',
						'number' => '108',
						'city' => '9050',
						'zip' => 'Gentbrugge',
						'country' => 'BE'
				),
				'vat' => 'BE 0829.564.289'
		);

		$var = $this->factr->clientsCreate($client);

		$this->assertType('array', $var);
		$this->assertArrayHasKey('id', $var);
		$this->assertArrayHasKey('cid', $var);
		$this->assertArrayHasKey('email', $var);
		$this->assertEquals($client['email'], $var['email'][0]);

		$var = $this->factr->clientsGet($var['id']);

		$this->assertType('array', $var);
		$this->assertArrayHasKey('id', $var);
		$this->assertArrayHasKey('cid', $var);
		$this->assertArrayHasKey('company_address', $var);
		$this->assertArrayHasKey('billing_address', $var);
		$this->assertArrayHasKey('email', $var);
	}

	{
		$client = array(
				'email' => 'factr@verkoyen.eu',
				'first_name' => 'Tijs',
				'last_name' => 'Verkoyen',
				'company' => 'SumoCoders',
				'billing_address' => array(
						'street' => 'Kerkstraat',
						'number' => '108',
						'city' => '9050',
						'zip' => 'Gentbrugge',
						'country' => 'BE'
				),
				'company_address' => array(
						'street' => 'Kerkstraat',
						'number' => '108',
						'city' => '9050',
						'zip' => 'Gentbrugge',
						'country' => 'BE'
				),
				'vat' => 'BE 0829.564.289'
		);

		$var = $this->factr->clientsCreate($client);

		$this->assertType('array', $var);
		$this->assertArrayHasKey('id', $var);
		$this->assertArrayHasKey('cid', $var);
		$this->assertArrayHasKey('email', $var);
		$this->assertEquals($client['email'], $var['email'][0]);
	}
}

