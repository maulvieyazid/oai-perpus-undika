<?php

use Helper\OAIPMH;

$helper = new Helper\Helper;

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


/**
 * Ini adalah lokasi file dimana masing2 data katalog di query dan disusun output untuk Dublin Core nya  
 * 
 * NOTE : Karena file2 ini di include, maka variabel2 yang ada diatas maupun yg ada di index.php
 *        semuanya dapat diakses langsung di masing2 file data katalog,
 *        jika menemukan variabel di komponen yang tiba2 ada tanpa didefinisikan terlebih dulu, maka
 *        coba cari di file ini atau di index.php
 */
include_once "component/listrecords/buku.php";

include_once "component/listrecords/majalah.php";

include_once "component/listrecords/software.php";

include_once "component/listrecords/tugas_akhir.php";


// Untuk menambahkan resumption token
$data[$verb]['resumptionToken'] = [
    '_attributes' => [
        'expirationDate' => $helper->parseDateStringToGranularity('tomorrow'),
        'completeListSize' => 43836,
        'cursor' => 0,
    ],
    '_value' => $next_resumption_token,
];
