<?php

use Model\Buku;

$helper = new Helper\Helper;

$verb = $_GET['verb'] ?? '';

// Init Connection
$db = Core\DB::setConnection('oracle');

// UNTUK SEMENTARA
// SELECT BUKU NYA DULU, NANTI BARU DIRAPIIN LAGI

$tipe_katalog = null;

// Cek apakah ada query param "identifier"
$identifier = $_GET['identifier'] ?? '';

// Kalo ada, maka
if ($identifier) {
    // Contoh isi dari identifier
    // oai:library.dinamika.ac.id:book-23530

    // Pecah identifier nya berdasarkan string ':'
    $identifier = explode(':', $identifier);

    // Ambil item yang paling akhir, itu adalah identifier asli yg dibutuhkan
    $real_identifier = end($identifier);

    // Ambil tipe katalog berdasarkan 4 huruf awal identifier
    $tipe_katalog = substr($real_identifier, 0, 4);

    // Ambil id katalog yang berupa angka pada identifier yang dimulai dari huruf ke 5
    $id_katalog = substr($real_identifier, 5);
}


$whereInduk = null;

// Jika tipe katalog nya adalah buku, maka tambahkan where induk
// ini untuk memfilter data buku berdasarkan id induk nya
if ($tipe_katalog == Buku::TIPE) {
    $whereInduk = "AND a.induk = :INDUK";
}


$sql = <<<SQL
    SELECT * FROM (
        SELECT x.*, ROWNUM rnum FROM (
            SELECT DISTINCT (a.jud_urut), a.induk, b.judul, b.ddc, b.jumlah, b.subyek1, b.subyek2, b.isbn_issn,
                   c.pengarang1, c.pengarang2, c.pengarang3, c.th_terbit, a.copy_ke, c.tgl_datang, b.abstrak_jud,
                   c.keterangan, c.kota, c.penerbit, b.bahasa
            FROM b_buku a, juduls b, pengolahans c 
            WHERE a.jud_urut = b.urut 
              AND a.pngo_urut_olah = c.urut_olah 
              AND (a.status IS NULL OR a.status ='P' OR a.status ='*' OR a.status ='D')
              AND (a.copy_ke = '1' OR a.copy_ke IS NULL) 
              $whereInduk
            ORDER BY c.th_terbit DESC
        ) x WHERE rownum <= :MAX_ROW
    ) WHERE rnum >= :MIN_ROW
SQL;


/* ============================================== */
/* PENGELOLAAN PARAM BINDING BUKU */
/* ============================================== */
$paramBinding = [
    'MIN_ROW' => 1,
    'MAX_ROW' => 25,
];

// Masukkan id katalog sebagai param "INDUK", jika where induk nya tidak kosong
if ($whereInduk) {
    $paramBinding['INDUK'] = $id_katalog;
}

/* ============================================== */
/* AKHIR PENGELOLAAN PARAM BINDING BUKU */
/* ============================================== */

// Jalankan query
$result = $db->GetAll($sql, $paramBinding);


foreach ($result as $buku) {
    $buku = (object) $buku; // <- Cast Array to Object

    // Karena file ini digunakan untuk mengisi GetRecord dan ListRecords, 
    // maka key array nya menggunakan verb nya langsung
    $data[$verb]["__custom:record:{$helper->random_unique_id()}"] = [
        'header' => [
            'identifier' => "oai:library.dinamika.ac.id:book-{$buku->INDUK}",
            'datestamp'  => $helper->parseDatetoGranularity($buku->TGL_DATANG),
            'setSpec'    => $buku->TH_TERBIT,
        ],
        'metadata' => [
            // Seluruh attribut yang perlu ditampilkan pada OAI ini adalah hasil diskusi / kesepakatan dengan mas Agung Perpus
            // Bila kedepan ada pertanyaan atau ada yg perlu diubah, boleh didiskusikan langsung dengan beliau.
            'oai_dc:dc' => [
                '_attributes' => [
                    'xmlns:oai_dc'       => 'http://www.openarchives.org/OAI/2.0/oai_dc/',
                    'xmlns:dc'           => 'http://purl.org/dc/elements/1.1/',
                    'xmlns:xsi'          => 'http://www.w3.org/2001/XMLSchema-instance',
                    'xsi:schemaLocation' => 'http://www.openarchives.org/OAI/2.0/oai_dc/ http://www.openarchives.org/OAI/2.0/oai_dc.xsd',
                ],
                'dc:relation' => Buku::getLinkDetil($buku->INDUK),
                'dc:title' => $buku->JUDUL,
                'dc:creator' => Buku::getSemuaAuthor($buku->PENGARANG1, $buku->PENGARANG2, $buku->PENGARANG3),
                'dc:subject' => [
                    ...(Buku::getSemuaAuthor($buku->SUBYEK1, $buku->SUBYEK2)),
                    $buku->DDC,
                ],
                'dc:publisher' => $buku->PENERBIT ?? '-',
                'dc:date' => $buku->TH_TERBIT ?? '-',
                'dc:description' => $buku->ABSTRAK_JUD ?? '-',
                'dc:format' => $buku->KETERANGAN ?? '-',
                'dc:coverage' => $buku->KOTA ?? '-',
                'dc:language' => $buku->BAHASA ?? '-',
                'dc:type' => 'Text',
                'dc:identifier' => [
                    Buku::getLinkGambar($buku->JUD_URUT),
                    $buku->INDUK ?? '-',
                    $buku->ISBN_ISSN ?? '-',
                    Buku::getCallNumber($buku->DDC, $buku->PENGARANG1, $buku->JUDUL),
                    Buku::getSitasiAPAStyle($buku->JUDUL, $buku->TH_TERBIT, $buku->PENERBIT, $buku->KOTA, $buku->PENGARANG1, $buku->PENGARANG2, $buku->PENGARANG3),
                ],
            ],
        ],
    ];
}


/* $data[$verb] = [
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
                    'dc:creator' => [
                        'Valade, Janet',
                    ],
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
    ]
]; */


// Untuk menambahkan resumption token
$data[$verb]['resumptionToken'] = 'cobaobaobcoabaob';
