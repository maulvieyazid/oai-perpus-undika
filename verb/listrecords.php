<?php

/* NOTE : FORMAT XML NYA NIRU / DUPLIKASI DARI SLIMS */
// karena file ini digunakan untuk mengisi GetRecord dan ListRecords, 
// maka key array nya menggunakan verb nya langsung
$data[$verb] = [
    'record' => [
        [
            'header' => [
                'identifier' => 'oai:localhost:slims-1',
                'datestamp' => '2007-11-29T15:36:50Z',
                'setSpec' => '2004',
            ],
            'metadata' => [
                'oai_dc:dc' => [
                    '_attributes' => [
                        'xmlns:oai_dc' => 'http://www.openarchives.org/OAI/2.0/oai_dc/',
                        'xmlns:dc' => 'http://purl.org/dc/elements/1.1/',
                        'xmlns:xsi' => 'http://www.w3.org/2001/XMLSchema-instance',
                        'xsi:schemaLocation' => 'http://www.openarchives.org/OAI/2.0/oai_dc/ http://www.openarchives.org/OAI/2.0/oai_dc.xsd',
                    ],
                    'dc:title' => 'PHP 5 for dummies',
                    'dc:creator' => 'Valade, Janet',
                    'dc:subject' => [
                        'Website',
                        'Programming',
                        '005.13/3 22',
                    ],
                    "__custom:dc\\:subject:{$helper->random_unique_id()}" => 'Cobacoba 1',
                    "__custom:dc\\:subject:{$helper->random_unique_id()}" => 'Cobacoba 2',
                    'dc:publisher' => 'Wiley',
                    'dc:date' => '2004',
                    'dc:language' => 'en',
                    'dc:type' => 'Text',
                    'dc:identifier' => [
                        'http://localhost/slims9.6.1//index.php?p=show_detail&amp;id=1',
                        '005.13/3-22 Jan p',
                        'http://localhost/slims9.6.1//lib/minigalnano/createthumb.php?filename=images/docs/php5_dummies.jpg&amp;width=200',
                    ],
                    'dc:identifier_isbn' => '0764541668',
                    'dc:coverage' => 'Hoboken, NJ',
                    'dc:source' => 'For dummies',
                    'dc:format' => 'xiv, 392 p. : ill. ; 24 cm.',
                ],
            ],
        ],
        [
            'header' => [
                'identifier' => 'oai:localhost:slims-2',
                'datestamp' => '2007-11-29T15:53:35Z',
                'setSpec' => '2005',
            ],
            'metadata' => [
                'oai_dc:dc' => [
                    '_attributes' => [
                        'xmlns:oai_dc' => 'http://www.openarchives.org/OAI/2.0/oai_dc/',
                        'xmlns:dc' => 'http://purl.org/dc/elements/1.1/',
                        'xmlns:xsi' => 'http://www.w3.org/2001/XMLSchema-instance',
                        'xsi:schemaLocation' => 'http://www.openarchives.org/OAI/2.0/oai_dc/ http://www.openarchives.org/OAI/2.0/oai_dc.xsd',
                    ],
                    'dc:title' => 'Linux In a Nutshell',
                    'dc:creator' => [
                        'Siever, Ellen',
                        'Love, Robert',
                        'Robbins, Arnold',
                        'Figgins, Stephen',
                        'Weber, Aaron',
                    ],
                    'dc:subject' => [
                        'Computer',
                        'Linux',
                        'Operating System',
                    ],
                    'dc:publisher' => 'OReilly',
                    'dc:date' => '2005',
                    'dc:language' => 'en',
                    'dc:type' => 'Text',
                    'dc:identifier' => [
                        'http://localhost/slims9.6.1//index.php?p=show_detail&amp;id=2',
                        '005.4/32-22 Ell l',
                        'http://localhost/slims9.6.1//lib/minigalnano/createthumb.php?filename=images/docs/linux_in_a_nutshell.jpg&amp;width=200',
                    ],
                    'dc:identifier_isbn' => '9780596009304',
                    'dc:coverage' => 'Sebastopol, CA',
                    'dc:source' => 'In a Nutshell',
                    'dc:format' => 'xiv, 925 p. : ill. ; 23 cm.',
                    "__custom:dc\\:subject:{$helper->random_unique_id()}" => '005.4/32 22',
                ],
            ],
        ],
    ],

];
