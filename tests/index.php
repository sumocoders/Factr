<?php

//require
require_once '../../../autoload.php';
require_once 'config.php';

use \SumoCoders\Factr\Factr;
use \SumoCoders\Factr\Client\Client;
use \SumoCoders\Factr\Client\Address;
use \SumoCoders\Factr\Invoice\Invoice;

// create instance
$factr = new Factr(USERNAME, PASSWORD);

$address = new Address();
$address->setStreet('Kerkstraat');
$address->setNumber('108');
$address->setZip('9050');
$address->setCity('Gentbrugge');
$address->setCountry('BE');

$client = new Client();
$client->setFirstName('Tijs');
$client->setLastName('Verkoyen');
$client->addEmail('php-factr@verkoyen.eu');
$client->setBillingAddress($address);
$client->setCompanyAddress($address);
$client->setRemarks('Created by the Wrapperclass. ' . time());

$invoice = new Invoice();

try {
//    $response = $factr->clients();
//    $response = $factr->clientsGet(2292);
//    $response = $factr->clientsCreate($client);

//    $response = $factr->invoices();
//    $response = $factr->invoicesGet(5258);
//    $response = $factr->invoicesGetByIid('IV08004');
//	$response = $factr->invoicesCreate($invoice);
//    $response = $factr->invoiceSendByMail(5261, 'foo@bar.com');
//    $response = $factr->invoicesAddPayment(5261, 123.45);
} catch (Exception $e) {
    var_dump($e);
}

// output
var_dump($response);
