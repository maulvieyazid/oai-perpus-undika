<?php

use Model\TugasAkhir;

/* ============================================================================== */
/* KATALOG TUGAS AKHIR */
/* ============================================================================== */

$whereThTerima = null;

// Jika ada query param "set", maka tambahkan where th_terima
// ini untuk memfilter data tugas akhir berdasarkan th_terima
if ($set) {
    // Susun where nya
    // Jika set nya bukan "-", maka query nya menggunakan parameter
    // Jika set nya "-", maka query nya menggunakan IS NULL
    $whereThTerima = ($set != '-')
        ? "AND TO_CHAR(tgl_terima, 'YYYY') = :TH_TERIMA"
        : 'AND tgl_terima IS NULL';
}

// Query Tugas Akhir
$sql = <<<SQL
     SELECT * FROM (
        SELECT x.*, ROWNUM rnum FROM (
            SELECT urut_olah, induk, th_terbit, tgl_terima, TO_CHAR(tgl_terima, 'YYYY') AS th_terima
            FROM b_ta a
            WHERE (status IS NULL OR status ='P' OR status ='*' OR status ='D' OR status ='G')
              $whereThTerima
        ) x WHERE rownum <= :MAX_ROW
    ) WHERE rnum >= :MIN_ROW
SQL;

/* Pengelolaan Parameter Binding untuk query Tugas Akhir */
$paramBinding = [
    'MIN_ROW' => $min_row,
    'MAX_ROW' => $max_row,
];

// Masukkan set sebagai param "TH_TERIMA", jika where th terima nya tidak kosong
if ($whereThTerima) {
    $paramBinding['TH_TERIMA'] = $set;
}

// Jalankan Query
$result = $db->GetAll($sql, $paramBinding);

foreach ($result as $tugas) {
    $tugas = (object) $tugas; // <- Cast Array to Object
    $tipe = TugasAkhir::TIPE;

    // Key "__custom" bertujuan agar bisa mendefine nama key yg sama, dalam hal ini yaitu "header"
    // karena bila ada dua / lebih key dengan nama yg sama, maka yg tampil hanya satu key saja
    // Untuk mengetahui lebih detil cara penggunaan custom key, silahkan lihat dokumentasi berikut.
    // https://github.com/spatie/array-to-xml#using-custom-keys

    $data['ListIdentifiers']["__custom:header:{$helper->random_unique_id()}"] = [
        'identifier' => "oai:library.dinamika.ac.id:{$tipe}-{$tugas->INDUK}",
        'datestamp'  => $helper->parseDateStringToGranularity($tugas->TGL_TERIMA),
        'setSpec'    => $tugas->TH_TERIMA ?? '-',
    ];
}

/* ============================================================================== */
/* AKHIR KATALOG TUGAS AKHIR */
/* ============================================================================== */