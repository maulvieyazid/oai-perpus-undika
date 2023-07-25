<?php

namespace Model;

use Model\Traits\KatalogTrait;

class Software
{
    use KatalogTrait;

    const TIPE = 'soft';

    static function getLinkDetil($induk)
    {
        return "https://library.dinamika.ac.id/detil-software-catalog.html?id=$induk";
    }

    static function getLinkGambar($jud_urut)
    {
        return "https://library.dinamika.ac.id/images/cover_cd/$jud_urut.jpg";
    }

    static function getType($jddc)
    {
        // Type default yang harus selalu ada
        $out = ['CD-ROM'];

        $jenis_khusus = ['CDR', 'CDRT'];

        // Jika JDDC termasuk salah satu dari jenis khusus, maka
        if (in_array($jddc, $jenis_khusus)) {
            // Tambahkan type ini
            $out[] = 'Computer Software';
        }

        return $out;
    }
}
