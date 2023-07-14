<?php

require_once "vendor/autoload.php";

require_once "helper/helper.php";

$helper = new Helper\Helper;

use Spatie\ArrayToXml\ArrayToXml;

date_default_timezone_set('Asia/Jakarta');

$http = $_SERVER['REQUEST_SCHEME'];
$host = $_SERVER['HTTP_HOST'];
$script_name = $_SERVER['SCRIPT_NAME'];

$now = date_create('now');
$now = $now->format('Y-m-d\TH:i:s\Z');

$metadataPrefix = isset($_GET['metadataPrefix']) ? $_GET['metadataPrefix'] : '';

$verb = isset($_GET['verb']) ? $_GET['verb'] : '';

/**
 * Variabel $data ini sebagai wadah penampung untuk konten XML OAI
 */
$data = [
    'responseDate' => $now,
    'request' => [
        '_attributes' => [
            'verb' => $verb,
            'metadataPrefix' => $metadataPrefix,
        ],
        '_value' => "{$http}://{$host}{$script_name}",
    ],
];


if ($verb == 'ListRecords') {
    include_once "verb/listrecords.php";
}

// Karena format <record> pada GetRecord sama dengan ListRecords, maka dari itu include nya jadi satu dengan ListRecords
if ($verb == 'GetRecord') {
    include_once "verb/listrecords.php";
}

if ($verb == 'Identify') {
    include_once "verb/identify.php";
}

if ($verb == 'ListSets') {
    include_once "verb/listsets.php";
}

if ($verb == 'ListMetadataFormats') {
    include_once "verb/listmetadataformats.php";
}

if ($verb == 'ListIdentifiers') {
    include_once "verb/listidentifiers.php";
}

// Settingan untuk root element dan XML declaration nya
$arrayToXml = new ArrayToXml(
    $data,
    [
        'rootElementName' => 'OAI-PMH',
        '_attributes' => [
            'xmlns' => 'http://www.openarchives.org/OAI/2.0/',
            'xmlns:xsi' => 'http://www.w3.org/2001/XMLSchema-instance',
            'xsi:schemaLocation' => 'http://www.openarchives.org/OAI/2.0/ http://www.openarchives.org/OAI/2.0/OAI-PMH.xsd',
        ]
    ],
    true,
    'UTF-8',
    '1.0',
);

// Menambahkan processing instruction agar bisa membaca file oai2.xsl
$arrayToXml->addProcessingInstruction('xml-stylesheet', 'type="text/xsl" href="./oai2.xsl"');

// Ubah header Content-Type agar browser dapat membaca / merender output menjadi XML
header('Content-Type: text/xml');

// Tampilkan output XML
echo $arrayToXml->prettify()->toXml();
