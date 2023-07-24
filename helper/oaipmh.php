<?php

namespace Helper;

class OAIPMH
{
    static function getIdentifier()
    {
        // Ambil query param "identifier"
        $identifier = $_GET['identifier'] ?? null;
        $tipe_katalog = null;
        $id_katalog = null;

        // Kalo identifier nya gk ada, maka
        if (!$identifier) {
            // Kembalikan array "tipe_katalog", "id_katalog" 
            return compact('tipe_katalog', 'id_katalog');
        }

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

        // Kembalikan array "tipe_katalog", "id_katalog" 
        return compact('tipe_katalog', 'id_katalog');
    }
}
