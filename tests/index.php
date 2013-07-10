<?php

//require
require_once '../../../autoload.php';
require_once 'config.php';

use \SumoCoders\Factr\Factr;
use \SumoCoders\Factr\Client\Client;
use \SumoCoders\Factr\Client\Address;
use \SumoCoders\Factr\Invoice\Invoice;
use \SumoCoders\Factr\Invoice\Item;

// create instance
$factr = new Factr();
$factr->setApiToken(API_TOKEN);

$address = new Address();
$address->setCity('Gentbrugge');
$address->setCountry('BE');
$address->setFullAddress('Kerkstraat 108' . "\n" . '9050 Gentbrugge');
$address->setNumber('108');
$address->setStreet('Kerkstraat');
$address->setZip('9050');

$client = new Client();
$client->setPaymentDays(30);
$client->setCid('CID' . time());
$client->setCompany('company');
$client->setVat('VAT');
$client->setFirstName('first_name');
$client->setLastName('last_name');
$client->setPhone('phone');
$client->setFax('fax');
$client->setCellphone('cellphone');     // @remark doesn't work
$client->setWebsite('website');
$client->setRemarks('remarks');
$client->setCompanyAddress($address);
$client->setBillingAddress($address);
$client->addEmail('e@mail.com');
$client->setInvoiceableByEmail(false);
$client->setInvoiceableBySnailMail(false);
$client->setInvoiceableByFactr(false);

$item = new Item();
$item->setDescription('just an item');
$item->setPrice(123.45);
$item->setAmount(67);
$item->setVat(21);

$invoice = new Invoice();
$invoice->setClient($factr->clientsGet(2292));
$invoice->addItem($item);

try {
//    $response = $factr->accountApiToken(USERNAME, PASSWORD);

//    $response = $factr->clients();
//    $response = $factr->clientsGet(2882);
//    $response = $factr->clientsCreate($client);
//    $client->setRemarks('Updated by the wrapper class');
//    $response = $factr->clientsUpdate('2882', $client);
//    $response = $factr->clientsDelete(123);

//    $response = $factr->invoices();
//    $response = $factr->invoicesGet(5261);
//    $response = $factr->invoicesGetByIid('IV08004');
//    $response = $factr->invoicesCreate($invoice);
//    $response = $factr->invoiceSendByMail(5261, 'foo@bar.com');
//    $response = $factr->invoicesAddPayment(5261, 123.45);
} catch (Exception $e) {
    var_dump($e);
}

// output
var_dump($response);
