<?php


/* NOTE : FORMAT XML NYA NIRU / DUPLIKASI DARI SLIMS */
// Tambahkan isian Identify ke array $data
$data['Identify'] = [
    'repositoryName' => 'Senayan',
    'baseURL' => 'http://localhost/slims9.6.1/slims9.6.1/oai2.php',
    'protocolVersion' => '2.0',
    'earliestDatestamp' => '2011-01-01T00:00:00Z',
    'deletedRecord' => 'transient',
    'granularity' => 'YYYY-MM-DDThh:mm:ssZ',
    'adminEmail' => 'some.one@contact.com',
    'description' => [
        'oai-identifier' => [
            '_attributes' => [
                'xmlns' => 'http://www.openarchives.org/OAI/2.0/oai-identifier',
                'xmlns:xsi' => 'http://www.w3.org/2001/XMLSchema-instance',
                'xsi:schemaLocation' => 'http://www.openarchives.org/OAI/2.0/oai-identifier                    http://www.openarchives.org/OAI/2.0/oai-identifier.xsd',
            ],
            'scheme' => 'oai',
            'repositoryIdentifier' => 'localhost',
            'delimiter' => ':',
            'sampleIdentifier' => 'oai:localhost:slims-1',
        ],
    ],
];
