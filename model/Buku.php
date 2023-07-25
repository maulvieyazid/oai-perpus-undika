<?php

namespace Model;

use Model\Traits\KatalogTrait;

class Buku
{
    use KatalogTrait;

    const TIPE = 'book';

    static function getLinkDetil($induk)
    {
        return "https://library.dinamika.ac.id/detil-buku-catalog.html?id=$induk";
    }

    static function getLinkGambar($jud_urut)
    {
        return "https://library.dinamika.ac.id/images/cover/$jud_urut.jpg";
    }

    static function getCallNumber($ddc, $pengarang1, $judul)
    {
        // Ambil 3 huruf depan Pengarang 1
        $substrpengarang1 = ($pengarang1 != '') ? substr($pengarang1, 0, 3) : '';

        // Ambil 1 huruf depan judul buku
        $substrjudul = ($judul != '') ? substr($judul, 0, 1) : '';

        // Susun sesuai urutan call number buku
        // [DDC] spasi [3 huruf depan Pengarang 1] spasi [1 huruf depan judul buku]
        return strtolower("$ddc $substrpengarang1 $substrjudul");
    }
}
