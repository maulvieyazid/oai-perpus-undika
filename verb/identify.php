<?php

/* NOTE : FORMAT XML NYA NIRU / DUPLIKASI DARI SLIMS */
// Tambahkan isian Identify ke array $data
$data['Identify'] = [
    'repositoryName' => 'Library Undika',
    'baseURL' => Helper\URL::getUrl(),
    'protocolVersion' => '2.0',
    'adminEmail' => 'perpus@dinamika.ac.id',
    'earliestDatestamp' => '2023-07-20T09:05:00Z',
    'deletedRecord' => 'transient',
    'granularity' => 'YYYY-MM-DDThh:mm:ssZ',
    'description' => [
        'oai-identifier' => [
            '_attributes' => [
                'xmlns' => 'http://www.openarchives.org/OAI/2.0/oai-identifier',
                'xmlns:xsi' => 'http://www.w3.org/2001/XMLSchema-instance',
                'xsi:schemaLocation' => "http://www.openarchives.org/OAI/2.0/oai-identifier http://www.openarchives.org/OAI/2.0/oai-identifier.xsd",
            ],
            'scheme' => 'oai',
            'repositoryIdentifier' => 'library.dinamika.ac.id',
            'delimiter' => ':',
            'sampleIdentifier' => 'oai:library.dinamika.ac.id:book-3505',
        ],
    ],
];
