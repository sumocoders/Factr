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

	/**
	 * Tests Factr->invoices()
	 */
	public function testInvoices()
	{
		$var = $this->factr->invoices();

		$this->assertType('array', $var);
		foreach($var as $row)
		{
			$this->assertArrayHasKey('id', $row);
			$this->assertArrayHasKey('client_id', $row);
			$this->assertArrayHasKey('iid', $row);
			$this->assertArrayHasKey('items', $row);
			foreach($row['items'] as $item)
			{
				$this->assertArrayHasKey('description', $item);
				$this->assertArrayHasKey('amount', $item);
				$this->assertArrayHasKey('price', $item);
				$this->assertArrayHasKey('vat', $item);
				$this->assertArrayHasKey('reference_id', $item);
				$this->assertArrayHasKey('total_without_vat', $item);
				$this->assertArrayHasKey('total_vat', $item);
				$this->assertArrayHasKey('total_with_vat', $item);
			}
		}
	}

	/**
	 * Tests Factr->invoicesGet()
	 */
	public function testInvoicesGet()
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

		// create client
		$var = $this->factr->clientsCreate($client);

		$invoice = array(
			'client_id' => $var['id'],
			'items' => array(
				array(
					'description' => 'foo',
					'price' => 123.45,
					'amount' => 67,
					'vat' => 21
				),
				array(
					'description' => 'bar',
					'price' => 543.21,
					'amount' => 76,
					'vat' => 6
				)
			)
		);

		// create invoice
		$var = $this->factr->invoicesCreate($invoice);

		// get the invoice
		$var = $this->factr->invoicesGet($var['id']);

		$this->assertType('array', $var);
		$this->assertArrayHasKey('id', $var);
		$this->assertArrayHasKey('client_id', $var);
		$this->assertArrayHasKey('iid', $var);
		$this->assertArrayHasKey('items', $var);
		foreach($var['items'] as $item)
		{
			$this->assertArrayHasKey('description', $item);
			$this->assertArrayHasKey('amount', $item);
			$this->assertArrayHasKey('price', $item);
			$this->assertArrayHasKey('vat', $item);
			$this->assertArrayHasKey('reference_id', $item);
			$this->assertArrayHasKey('total_without_vat', $item);
			$this->assertArrayHasKey('total_vat', $item);
			$this->assertArrayHasKey('total_with_vat', $item);
		}
	}

	/**
	 * Tests Factr->invoicesGetByIid()
	 */
	public function testInvoicesGetByIid()
	{
		$var = $this->factr->invoicesGetByIid('IV08004');
			$this->assertType('array', $var);
		$this->assertArrayHasKey('id', $var);
		$this->assertArrayHasKey('client_id', $var);
		$this->assertArrayHasKey('iid', $var);
		$this->assertArrayHasKey('items', $var);
		foreach($var['items'] as $item)
		{
			$this->assertArrayHasKey('description', $item);
			$this->assertArrayHasKey('amount', $item);
			$this->assertArrayHasKey('price', $item);
			$this->assertArrayHasKey('vat', $item);
			$this->assertArrayHasKey('reference_id', $item);
			$this->assertArrayHasKey('total_without_vat', $item);
			$this->assertArrayHasKey('total_vat', $item);
			$this->assertArrayHasKey('total_with_vat', $item);
		}
	}

	/**
	 * Tests Factr->invoicesSendByMail()
	 */
	public function testInvoiceSendByMail()
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

		// create client
		$var = $this->factr->clientsCreate($client);

		$invoice = array(
			'client_id' => $var['id'],
			'items' => array(
				array(
					'description' => 'foo',
					'price' => 123.45,
					'amount' => 67,
					'vat' => 21
				),
				array(
					'description' => 'bar',
					'price' => 543.21,
					'amount' => 76,
					'vat' => 6
				)
			)
		);

		// create invoice
		$var = $this->factr->invoicesCreate($invoice);

		// send the invoice
		$var = $this->factr->invoiceSendByMail($var['id'], 'foo@bar.com');

		$this->assertType('array', $var);
		$this->assertArrayHasKey('bcc', $var);
		$this->assertArrayHasKey('cc', $var);
		$this->assertArrayHasKey('subject', $var);
		$this->assertArrayHasKey('text', $var);
		$this->assertArrayHasKey('to', $var);
		$this->assertEquals('foo@bar.com', $var['to'][0]);
	}

	/**
	 * Tests Factr->invoicesAddPayment()
	 */
	public function testInvoicesAddPayment()
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

		// create client
		$var = $this->factr->clientsCreate($client);

		$invoice = array(
			'client_id' => $var['id'],
			'items' => array(
				array(
					'description' => 'foo',
					'price' => 123.45,
					'amount' => 67,
					'vat' => 21
				),
				array(
					'description' => 'bar',
					'price' => 543.21,
					'amount' => 76,
					'vat' => 6
				)
			)
		);

		// create invoice
		$var = $this->factr->invoicesCreate($invoice);

		// add payment
		$var = $this->factr->invoicesAddPayment($var['id'], 12345.67);

		$this->assertType('array', $var);
		$this->assertArrayHasKey('amount', $var);
		$this->assertArrayHasKey('id', $var);
		$this->assertArrayHasKey('invoice_id', $var);
		$this->assertArrayHasKey('amount', $var);
		$this->assertArrayHasKey('paid_at', $var);
	}
}

