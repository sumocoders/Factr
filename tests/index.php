<?php

// require
require_once 'config.php';
require_once '../factr.php';

// create instance
$factr = new Factr(USERNAME, PASSWORD);

$client = array(
	'email' => 'factr@verkoyen.eu',
	'first_name' => 'Tijs',
	'last_name' => 'Verkoyen',
	'company' => 'Sumo Coders',
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

// $response = $factr->clients();
// $response = $factr->clientsGet(1384);
// $response = $factr->clientsCreate($client);


// output (Spoon::dump())
ob_start();
var_dump($response);
$output = ob_get_clean();

// cleanup the output
$output = preg_replace('/\]\=\>\n(\s+)/m', '] => ', $output);

// print
echo '<pre>' . htmlspecialchars($output, ENT_QUOTES, 'UTF-8') . '</pre>';