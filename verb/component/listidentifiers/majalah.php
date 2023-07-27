<?php

use Model\Majalah;

/* ============================================================================== */
/* KATALOG MAJALAH */
/* ============================================================================== */

$whereThTerbit = null;

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
            SELECT DISTINCT (a.jud_urut), a.induk, c.th_terbit, c.tgl_datang 
            FROM b_majalah a, juduls b, pengolahans c 
            WHERE a.jud_urut = b.urut 
              AND a.pngo_urut_olah = c.urut_olah 
              AND (a.status IS NULL OR a.status ='P' OR a.status ='*' OR a.status ='D')
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

// Masukkan $set sebagai param "TH_TERBIT", jika where th terbit nya tidak kosong
if ($whereThTerbit) {
    $paramBinding['TH_TERBIT'] = $set;
}

// Jalankan Query
$result = $db->GetAll($sql, $paramBinding);

foreach ($result as $majalah) {
    $majalah = (object) $majalah; // <- Cast Array to Object
    $tipe = Majalah::TIPE;

    // Key "__custom" bertujuan agar bisa mendefine nama key yg sama, dalam hal ini yaitu "header"
    // karena bila ada dua / lebih key dengan nama yg sama, maka yg tampil hanya satu key saja
    // Untuk mengetahui lebih detil cara penggunaan custom key, silahkan lihat dokumentasi berikut.
    // https://github.com/spatie/array-to-xml#using-custom-keys

    $data['ListIdentifiers']["__custom:header:{$helper->random_unique_id()}"] = [
        'identifier' => "oai:library.dinamika.ac.id:{$tipe}-{$majalah->INDUK}",
        'datestamp'  => $helper->parseDateStringToGranularity($majalah->TGL_DATANG),
        'setSpec'    => $majalah->TH_TERBIT ?? '-',
    ];
}


/* ============================================================================== */
/* AKHIR KATALOG MAJALAH */
/* ============================================================================== */