<?php

use Helper\OAIPMH;
use Model\Buku;

$helper = new Helper\Helper;

$verb = $_GET['verb'] ?? '';

// Init Connection
$db = Core\DB::setConnection('oracle');

// Fungsi getIdentifier() digunakan untuk mengambil identifier pada query param
// Identifier digunakan untuk mengambil satu data katalog saja
// dikarenakan ada 4 data katalog yang akan ditampilkan dalam OAI ini sehingga perlu dipisahkan berdasarkan tipe katalog nya
// 4 data katalog tsb adalah Buku, Majalah, Software, TA (Tugas Akhir)
$getIdentifier = (object) OAIPMH::getIdentifier();
$tipe_katalog = $getIdentifier->tipe_katalog;
$id_katalog = $getIdentifier->id_katalog;


// Fungsi getResumptionToken() digunakan untuk melakukan parsing resumptionToken pada query param
// dan menggenerate resumptionToken selanjutnya
// resumptionToken ini sebenarnya adalah nilai limit dan offset untuk query data katalog
// resumptionToken ini diencrypt sehingga lebih aman
$resumptionToken =  (object) OAIPMH::getResumptionToken();
$min_row = $resumptionToken->min_row;
$max_row = $resumptionToken->max_row;
$next_resumption_token = $resumptionToken->next_resumption_token;


/* ============================================================================== */
/* KATALOG BUKU */
/* ============================================================================== */
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


/* Pengelolaan Parameter Binding untuk query Buku */
$paramBinding = [
    'MIN_ROW' => $min_row,
    'MAX_ROW' => $max_row,
];

// Masukkan id katalog sebagai param "INDUK", jika where induk nya tidak kosong
if ($whereInduk) {
    $paramBinding['INDUK'] = $id_katalog;
}


/** NOTE : Untuk meningkatkan performa aplikasi
 * maka sebelum menjalankan query perlu dilakukan pengecekan terlebih dahulu terhadap tipe katalog
 */
// Jalankan query hanya jika :
// - Tipe Katalog nya kosong
// OR
// - Tipe Katalog nya adalah buku
$result = [];

if (!$tipe_katalog || $tipe_katalog == Buku::TIPE) {

    // Jalankan Query
    $result = $db->GetAll($sql, $paramBinding);
}


foreach ($result as $buku) {
    $buku = (object) $buku; // <- Cast Array to Object

    // Karena file ini digunakan untuk mengisi GetRecord dan ListRecords, 
    // maka key array nya menggunakan verb nya langsung
    $data[$verb]["__custom:record:{$helper->random_unique_id()}"] = [
        'header' => [
            'identifier' => "oai:library.dinamika.ac.id:book-{$buku->INDUK}",
            'datestamp'  => $helper->parseDateStringToGranularity($buku->TGL_DATANG),
            'setSpec'    => $buku->TH_TERBIT ?? '',
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
                'dc:title' => $buku->JUDUL ?? '-',
                'dc:creator' => Buku::getSemuaAuthor($buku->PENGARANG1, $buku->PENGARANG2, $buku->PENGARANG3),
                'dc:subject' => [
                    ...(Buku::getSemuaAuthor($buku->SUBYEK1, $buku->SUBYEK2)),
                    $buku->DDC ?? '-',
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

/* ============================================================================== */
/* AKHIR KATALOG BUKU */
/* ============================================================================== */


/* ============================================================================== */
/* KATALOG MAJALAH */
/* ============================================================================== */

/* ============================================================================== */
/* AKHIR KATALOG MAJALAH */
/* ============================================================================== */

// Untuk menambahkan resumption token
$data[$verb]['resumptionToken'] = [
    '_attributes' => [
        'expirationDate' => $helper->parseDateStringToGranularity('tomorrow'),
        'completeListSize' => 43836,
        'cursor' => 0,
    ],
    '_value' => $next_resumption_token,
];
