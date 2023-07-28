<?php
// Ini adalah file entri untuk semua dependensi
require_once __DIR__ . "/../app.php";

use Spatie\ArrayToXml\ArrayToXml;

date_default_timezone_set('Asia/Jakarta');

$now = (new Helper\Helper)->parseDateStringToGranularity('now');

$metadataPrefix = $_GET['metadataPrefix'] ?? '';

$verb = $_GET['verb'] ?? '';

/**
 * Variabel $data ini sebagai wadah penampung untuk konten XML OAI
 */
$data = [
    'responseDate' => $now,
    'request' => [
        '_attributes' => [
            'verb' => $verb,
        ],
        '_value' => Helper\URL::getUrl(),
    ],
];

// Kalo query param "metadataPrefix" nya ada isinya, maka tambahkan ke attribute request
if (!!$metadataPrefix) {
    $data['request']['_attributes']['metadataPrefix'] = $metadataPrefix;
}

/* ==================================================================== */
/* PENGECEKAN QUERY PARAM VERB NYA */
/* ==================================================================== */

// Karena format <record> pada GetRecord sama dengan ListRecords, maka dari itu include nya jadi satu dengan ListRecords
if ($verb == 'ListRecords' || $verb == 'GetRecord') {
    include_once __DIR__ . "/../verb/listrecords.php";
}

if ($verb == 'Identify') {
    include_once __DIR__ . "/../verb/identify.php";
}

if ($verb == 'ListSets') {
    include_once __DIR__ . "/../verb/listsets.php";
}

if ($verb == 'ListMetadataFormats') {
    include_once __DIR__ . "/../verb/listmetadataformats.php";
}

if ($verb == 'ListIdentifiers') {
    include_once __DIR__ . "/../verb/listidentifiers.php";
}
/* ==================================================================== */
/* AKHIR PENGECEKAN QUERY PARAM VERB NYA */
/* ==================================================================== */

// Settingan untuk root element dan XML declaration nya
$arrayToXml = new ArrayToXml(
    $data,
    $rootElement = [
        'rootElementName' => 'OAI-PMH',
        '_attributes' => [
            'xmlns' => 'http://www.openarchives.org/OAI/2.0/',
            'xmlns:xsi' => 'http://www.w3.org/2001/XMLSchema-instance',
            'xsi:schemaLocation' => 'http://www.openarchives.org/OAI/2.0/ http://www.openarchives.org/OAI/2.0/OAI-PMH.xsd',
        ]
    ],
    $replaceSpacesByUnderScoresInKeyNames = true,
    $xmlEncoding = 'UTF-8',
    $xmlVersion = '1.0',
);

// Menambahkan processing instruction agar bisa membaca file oai2.xsl
$arrayToXml->addProcessingInstruction('xml-stylesheet', 'type="text/xsl" href="../oai2.xsl"');

// Ubah header Content-Type agar browser dapat membaca / merender output menjadi XML
header('Content-Type: text/xml');

// Tampilkan output XML
echo $arrayToXml->prettify()->toXml();
