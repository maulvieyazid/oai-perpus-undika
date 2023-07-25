<?php

use Model\TugasAkhir;

/* ============================================================================== */
/* KATALOG TUGAS AKHIR */
/* ============================================================================== */

$whereInduk = null;

// Jika tipe katalog nya adalah tugas akhir, maka tambahkan where induk
// ini untuk memfilter data tugas akhir berdasarkan id induk nya
if ($tipe_katalog == TugasAkhir::TIPE) {
    $whereInduk = "AND a.induk = :INDUK";
}

// Query Tugas Akhir
$sql = <<<SQL
     SELECT * FROM (
        SELECT x.*, ROWNUM rnum FROM (
            SELECT urut_olah, induk, jddc, ddc, judul, pengarang, pengarang2, pengarang3, pengarang4, pengarang5, 
                   kota, th_terbit, subyek, subyek2, keterangan, pembimbing1, pembimbing2, tgl_terima, bahasa, penerbit
            FROM b_ta a
            WHERE (status IS NULL OR status ='P' OR status ='*' OR status ='D' OR status ='G')
              $whereInduk
        ) x WHERE rownum <= :MAX_ROW
    ) WHERE rnum >= :MIN_ROW
SQL;

/* Pengelolaan Parameter Binding untuk query Tugas Akhir */
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
// - Tipe Katalog nya adalah Tugas Akhir
$result = [];

if (!$tipe_katalog || $tipe_katalog == TugasAkhir::TIPE) {

    // Jalankan Query
    $result = $db->GetAll($sql, $paramBinding);
}

foreach ($result as $tugas) {
    $tugas = (object) $tugas; // <- Cast Array to Object
    $tipe = TugasAkhir::TIPE;

    // Karena file ini digunakan untuk mengisi GetRecord dan ListRecords, 
    // maka key array nya menggunakan verb nya langsung
    $data[$verb]["__custom:record:{$helper->random_unique_id()}"] = [
        'header' => [
            'identifier' => "oai:library.dinamika.ac.id:{$tipe}-{$tugas->INDUK}",
            'datestamp'  => $helper->parseDateStringToGranularity($tugas->TGL_TERIMA),
            'setSpec'    => $tugas->TGL_TERIMA ?? '',
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
                'dc:relation' => TugasAkhir::getLinkDetil($tugas->INDUK),
                'dc:title' => $tugas->JUDUL ?? '-',
                'dc:creator' => TugasAkhir::getSemuaAuthor(
                    $tugas->PENGARANG,
                    $tugas->PENGARANG2,
                    $tugas->PENGARANG3,
                    $tugas->PENGARANG4,
                    $tugas->PENGARANG5
                ),
                'dc:subject' => [
                    ...(TugasAkhir::getSemuaSubject($tugas->SUBYEK, $tugas->SUBYEK2)),
                    $tugas->DDC ?? '-',
                ],
                'dc:publisher' => TugasAkhir::PENERBIT,
                'dc:date' => $tugas->TH_TERBIT ?? '-',
                'dc:format' => $tugas->KETERANGAN ?? '-',
                'dc:coverage' => $tugas->KOTA ?? '-',
                'dc:language' => $tugas->BAHASA ?? '-',
                'dc:type' => 'Text',
                'dc:identifier' => [
                    TugasAkhir::getLinkGambar($tugas->URUT_OLAH),
                    $tugas->INDUK ?? '-',
                    TugasAkhir::getSitasiAPAStyle(
                        $tugas->JUDUL,
                        $tugas->TH_TERBIT,
                        TugasAkhir::PENERBIT,
                        $tugas->KOTA,
                        $tugas->PENGARANG,
                        $tugas->PENGARANG2,
                        $tugas->PENGARANG3,
                        $tugas->PENGARANG4,
                        $tugas->PENGARANG5
                    ),
                ],
            ],
        ],
    ];
}

/* ============================================================================== */
/* AKHIR KATALOG TUGAS AKHIR */
/* ============================================================================== */