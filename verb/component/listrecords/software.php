<?php

use Model\Software;

/* ============================================================================== */
/* KATALOG SOFTWARE */
/* ============================================================================== */

$whereInduk = null;

// Jika tipe katalog nya adalah software, maka tambahkan where induk
// ini untuk memfilter data software berdasarkan id induk nya
if ($tipe_katalog == Software::TIPE) {
    $whereInduk = "AND a.induk = :INDUK";
}

// Query Software
$sql = <<<SQL
    SELECT * FROM (
        SELECT x.*, ROWNUM rnum FROM (
            SELECT DISTINCT(a.jud_urut), a.induk, b.judul, b.ddc, b.jumlah, b.jml_disk, a.jddc, c.th_terbit, c.keterangan,
                c.tgl_datang, b.abstrak_jud, c.kota, b.bahasa, c.penerbit, c.pengarang1, c.pengarang2, c.pengarang3,
                b.subyek1, b.subyek2
            FROM b_software a, juduls b, pengolahans c 
            WHERE a.jud_urut = b.urut 
              AND a.pngo_urut_olah = c.urut_olah 
              AND (a.status IS NULL OR a.status ='P' OR a.status ='*' OR a.status ='D')
              $whereInduk
            ORDER BY c.th_terbit DESC
        ) x WHERE rownum <= :MAX_ROW
    ) WHERE rnum >= :MIN_ROW
SQL;

/* Pengelolaan Parameter Binding untuk query Software */
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
// - Tipe Katalog nya adalah Software
$result = [];

if (!$tipe_katalog || $tipe_katalog == Software::TIPE) {

    // Jalankan Query
    $result = $db->GetAll($sql, $paramBinding);
}

foreach ($result as $software) {
    $software = (object) $software; // <- Cast Array to Object
    $tipe = Software::TIPE;

    // Karena file ini digunakan untuk mengisi GetRecord dan ListRecords, 
    // maka key array nya menggunakan verb nya langsung
    $data[$verb]["__custom:record:{$helper->random_unique_id()}"] = [
        'header' => [
            'identifier' => "oai:library.dinamika.ac.id:{$tipe}-{$software->INDUK}",
            'datestamp'  => $helper->parseDateStringToGranularity($software->TGL_DATANG),
            'setSpec'    => $software->TH_TERBIT ?? '',
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
                'dc:relation' => Software::getLinkDetil($software->INDUK),
                'dc:title' => $software->JUDUL ?? '-',
                'dc:creator' => Software::getSemuaAuthor($software->PENGARANG1, $software->PENGARANG2, $software->PENGARANG3),
                'dc:subject' => [
                    ...(Software::getSemuaSubject($software->SUBYEK1, $software->SUBYEK2)),
                    $software->DDC ?? '-',
                ],
                'dc:publisher' => $software->PENERBIT ?? '-',
                'dc:date' => $software->TH_TERBIT ?? '-',
                'dc:description' => $software->ABSTRAK_JUD ?? '-',
                'dc:format' => $software->KETERANGAN ?? '-',
                'dc:coverage' => $software->KOTA ?? '-',
                'dc:language' => $software->BAHASA ?? '-',
                'dc:type' => Software::getType($software->JDDC),
                'dc:identifier' => [
                    Software::getLinkGambar($software->JUD_URUT),
                    $software->INDUK ?? '-',
                    Software::getSitasiAPAStyle($software->JUDUL, $software->TH_TERBIT, $software->PENERBIT, $software->KOTA, $software->PENGARANG1, $software->PENGARANG2, $software->PENGARANG3),
                ],
            ],
        ],
    ];
}


/* ============================================================================== */
/* AKHIR KATALOG SOFTWARE */
/* ============================================================================== */