<?php

//require
require_once '../../../autoload.php';
require_once 'config.php';

use \SumoCoders\Factr\Factr;
use \SumoCoders\Factr\Client\Client;
use \SumoCoders\Factr\Client\Address;
use \SumoCoders\Factr\Invoice\Invoice;
use \SumoCoders\Factr\Invoice\Item;
use \SumoCoders\Factr\Invoice\Payment;

// create instance
$factr = new Factr();
$factr->setApiToken(API_TOKEN);

$address = new Address();
$address->setCountry('BE');
$address->setFullAddress('Kerkstraat 108' . "\n" . '9050 Gentbrugge');

$client = new Client();
$client->setPaymentDays(30);
$client->setCid('CID' . time());
$client->setCompany('company');
$client->setVat('VAT');
$client->setFirstName('first_name');
$client->setLastName('last_name');
$client->setPhone('phone');
$client->setFax('fax');
$client->setCell('cell');
$client->setWebsite('website');
$client->setRemarks('remarks');
$client->setCompanyAddress($address);
$client->setBillingAddress($address);
$client->addEmail('e@mail.com');
$client->setInvoiceableByEmail(false);
$client->setInvoiceableBySnailMail(false);
$client->setInvoiceableByFactr(false);

$item = new Item();
$item->setAmount(12);
$item->setDescription('just an item');
$item->setPrice(34.56);
$item->setVat(21);

$payment = new Payment();
$payment->setAmount(12.34);
$payment->setIdentifier('identifier');
$payment->setPaidAt(new DateTime('@' . mktime(12, 13, 14, 6, 20, 2014)));

$invoice = new Invoice();
$invoice->addItem($item);
$invoice->setClientId(3026);
$invoice->setDescription('description');
$invoice->setShownRemark('shown_remark');
$invoice->setState('created');

try {
//    $response = $factr->accountApiToken(USERNAME, PASSWORD);

//    $response = $factr->clients();
//    $response = $factr->clientsGet(3026);
//    $response = $factr->clientsCreate($client);
//    $client->setRemarks('Updated by the wrapper class');
//    $response = $factr->clientsUpdate(3026, $client);
//    $response = $factr->clientsDelete(123);
//    $response = $factr->clientsInvoices(2703);

//    $response = $factr->invoices();
//    $response = $factr->invoicesGet(9256);
//    $response = $factr->invoicesGetByIid('IV08004');
//    $response = $factr->invoicesCreate($invoice);
//    $invoice->setDescription('Updated by the wrapper class');
//    $response = $factr->invoicesUpdate(9256, $invoice);
//    $response = $factr->invoiceSendByMail(5261, 'foo@bar.com');
//    $response = $factr->invoicesAddPayment(5261, $payment);
//    $response = $factr->invoicesDelete($response->getId());
} catch (Exception $e) {
    var_dump($e);
}

// output
var_dump($response);
