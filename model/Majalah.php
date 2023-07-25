<?php

namespace Model;

use Model\Traits\KatalogTrait;

class Majalah
{
    use KatalogTrait;

    const TIPE = 'magz';

    static function getLinkDetil($induk)
    {
        return "https://library.dinamika.ac.id/detil-majalah-catalog.html?id=$induk";
    }

    static function getLinkGambar($jud_urut)
    {
        return "https://library.dinamika.ac.id/images/cover_magz/$jud_urut.jpg";
    }

    static function getCallNumber($ddc, $judul)
    {
        // Ambil 1 huruf depan judul majalah
        $substrjudul = ($judul != '') ? substr($judul, 0, 1) : '';

        // Susun sesuai urutan call number majalah
        // [DDC] spasi [1 huruf depan judul majalah]
        return strtolower("$ddc $substrjudul");
    }
}
