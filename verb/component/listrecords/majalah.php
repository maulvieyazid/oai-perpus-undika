<?php

use Model\Majalah;

/* ============================================================================== */
/* KATALOG MAJALAH */
/* ============================================================================== */

$whereInduk = null;
$whereThTerbit = null;

// Jika tipe katalog nya adalah majalah, maka tambahkan where induk
// ini untuk memfilter data majalah berdasarkan id induk nya
if ($tipe_katalog == Majalah::TIPE) {
    $whereInduk = "AND a.induk = :INDUK";
}

// Jika ada query param "set", maka tambahkan where th_terbit
// ini untuk memfilter data majalah berdasarkan th_terbit
if ($set) {
    // Susun where nya
    // Jika set nya bukan "-", maka query nya menggunakan parameter
    // Jika set nya "-", maka query nya menggunakan IS NULL
    $whereThTerbit = "AND c.th_terbit ";
    $whereThTerbit .= ($set != '-') ? '= :TH_TERBIT' : 'IS NULL';
}

// Query Majalah
$sql = <<<SQL
    SELECT * FROM (
        SELECT x.*, ROWNUM rnum FROM (
            SELECT DISTINCT (a.jud_urut), a.induk, b.judul, b.ddc, b.jumlah, b.no_majalah, c.penerbit, c.th_terbit,
                   c.tgl_datang, b.abstrak_jud, c.keterangan, c.kota, b.bahasa
            FROM b_majalah a, juduls b, pengolahans c 
            WHERE a.jud_urut = b.urut 
              AND a.pngo_urut_olah = c.urut_olah 
              AND (a.status IS NULL OR a.status ='P' OR a.status ='*' OR a.status ='D')
              $whereInduk
              $whereThTerbit
            ORDER BY c.th_terbit DESC
        ) x WHERE rownum <= :MAX_ROW
    ) WHERE rnum >= :MIN_ROW
SQL;

/* Pengelolaan Parameter Binding untuk query Majalah */
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
// - Tipe Katalog nya adalah Majalah
$result = [];

if (!$tipe_katalog || $tipe_katalog == Majalah::TIPE) {

    // Jalankan Query
    $result = $db->GetAll($sql, $paramBinding);
}

foreach ($result as $majalah) {
    $majalah = (object) $majalah; // <- Cast Array to Object
    $tipe = Majalah::TIPE;

    // Key "__custom" bertujuan agar bisa mendefine nama key yg sama, dalam hal ini yaitu "record"
    // karena bila ada dua / lebih key dengan nama yg sama, maka yg tampil hanya satu key saja
    // Untuk mengetahui lebih detil cara penggunaan custom key, silahkan lihat dokumentasi berikut.
    // https://github.com/spatie/array-to-xml#using-custom-keys

    // Karena file ini digunakan untuk mengisi GetRecord dan ListRecords, 
    // maka key array nya menggunakan verb nya langsung
    $data[$verb]["__custom:record:{$helper->random_unique_id()}"] = [
        'header' => [
            'identifier' => "oai:library.dinamika.ac.id:{$tipe}-{$majalah->INDUK}",
            'datestamp'  => $helper->parseDateStringToGranularity($majalah->TGL_DATANG),
            'setSpec'    => $majalah->TH_TERBIT ?? '-',
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
                'dc:relation' => Majalah::getLinkDetil($majalah->INDUK),
                'dc:title' => $majalah->JUDUL ?? '-',
                'dc:subject' => [
                    $majalah->DDC ?? '-',
                ],
                'dc:publisher' => $majalah->PENERBIT ?? '-',
                'dc:date' => $majalah->TH_TERBIT ?? '-',
                'dc:description' => $majalah->ABSTRAK_JUD ?? '-',
                'dc:format' => $majalah->KETERANGAN ?? '-',
                'dc:coverage' => $majalah->KOTA ?? '-',
                'dc:language' => $majalah->BAHASA ?? '-',
                'dc:type' => 'Text',
                'dc:identifier' => [
                    Majalah::getLinkGambar($majalah->JUD_URUT),
                    $majalah->INDUK ?? '-',
                    Majalah::getCallNumber($majalah->DDC, $majalah->JUDUL),
                    Majalah::getSitasiAPAStyle($majalah->JUDUL, $majalah->TH_TERBIT, $majalah->PENERBIT, $majalah->KOTA, []),
                ],
            ],
        ],
    ];
}


/* ============================================================================== */
/* AKHIR KATALOG MAJALAH */
/* ============================================================================== */