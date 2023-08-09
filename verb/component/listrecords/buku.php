<?php

use Model\Buku;


/* ============================================================================== */
/* KATALOG BUKU */
/* ============================================================================== */

$whereInduk = null;
$whereThTerbit = null;


// Jika tipe katalog nya adalah buku, maka tambahkan where induk
// ini untuk memfilter data buku berdasarkan id induk nya
if ($tipe_katalog == Buku::TIPE) {
    $whereInduk = "AND a.induk = :INDUK";
}

// Jika ada query param "set", maka tambahkan where th_terbit
// ini untuk memfilter data buku berdasarkan th_terbit
if ($set) {
    // Susun where nya
    // Jika set nya bukan "-", maka query nya menggunakan parameter
    // Jika set nya "-", maka query nya menggunakan IS NULL
    $whereThTerbit = "AND c.th_terbit ";
    $whereThTerbit .= ($set != '-') ? '= :TH_TERBIT' : 'IS NULL';
}

// Query Buku
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
              $whereThTerbit
            ORDER BY a.induk ASC
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

// Masukkan $set sebagai param "TH_TERBIT", jika where th terbit nya tidak kosong
if ($whereThTerbit) {
    $paramBinding['TH_TERBIT'] = $set;
}


/** NOTE : Untuk meningkatkan performa aplikasi
 * maka sebelum menjalankan query perlu dilakukan pengecekan terlebih dahulu terhadap tipe katalog
 */
// Jalankan query hanya jika :
// - Tipe Katalog nya kosong
// OR
// - Tipe Katalog nya adalah Buku
$result = [];

if (!$tipe_katalog || $tipe_katalog == Buku::TIPE) {

    // Jalankan Query
    $result = $db->GetAll($sql, $paramBinding);
}


foreach ($result as $buku) {
    $buku = (object) $buku; // <- Cast Array to Object
    $tipe = Buku::TIPE;

    // Key "__custom" bertujuan agar bisa mendefine nama key yg sama, dalam hal ini yaitu "record"
    // karena bila ada dua / lebih key dengan nama yg sama, maka yg tampil hanya satu key saja
    // Untuk mengetahui lebih detil cara penggunaan custom key, silahkan lihat dokumentasi berikut.
    // https://github.com/spatie/array-to-xml#using-custom-keys

    // Karena file ini digunakan untuk mengisi GetRecord dan ListRecords, 
    // maka key array nya menggunakan verb nya langsung
    $data[$verb]["__custom:record:{$helper->random_unique_id()}"] = [
        'header' => [
            'identifier' => "oai:library.dinamika.ac.id:{$tipe}-{$buku->INDUK}",
            'datestamp'  => $helper->parseDateStringToGranularity($buku->TGL_DATANG),
            'setSpec'    => $buku->TH_TERBIT ?? '-',
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
                    ...(Buku::getSemuaSubject($buku->SUBYEK1, $buku->SUBYEK2)),
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