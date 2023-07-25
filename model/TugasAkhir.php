<?php

namespace Model;

use Model\Traits\KatalogTrait;

class TugasAkhir
{
    use KatalogTrait;

    const TIPE = 'akhr';
    const PENERBIT = "Universitas Dinamika";

    static function getLinkDetil($induk)
    {
        return "https://library.dinamika.ac.id/detil-ta-catalog.html?id=$induk";
    }

    static function getLinkGambar($jud_urut)
    {
        return "https://library.dinamika.ac.id/images/cover_ta/$jud_urut.jpg";
    }
}
