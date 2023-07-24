<?php

namespace Model;

class Buku
{
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

    static function getSemuaAuthor(...$pengarang)
    {
        // Untuk mengambil semua author, maka perlu untuk membuang yg nilainya null / string kosong
        return self::filterEmpty(...$pengarang);
    }

    static function getSitasiAPAStyle($judul, $th_terbit, $penerbit, $kota, ...$pengarang)
    {
        $semuaPengarang = self::filterEmpty(...$pengarang);

        // Kalo item pertama nya adalah '-', maka itu artinya pengarang nya tidak ada
        if ($semuaPengarang[0] == '-') {
            $semuaPengarang = '';
        }
        // Kalo bukan, maka
        else {
            // Lakukan map untuk memotong nama depan pengarang
            $semuaPengarang = array_map(function ($pengarang) {
                // Pecah nama depan dan belakang berdasarkan koma
                $exp = explode(',', $pengarang);
                // Ambil nama belakang
                $namabelakang = trim($exp[0]);
                // Ambil nama depan, klo gk ada maka ganti dengan string kosong, dan lakukan trim
                $namadepan = trim($exp[1] ?? '');
                // Ambil 1 huruf saja dari nama depan pengarang
                $namadepan = substr($namadepan, 0, 1);

                // Return sesuai format
                // [namabelakang] koma spasi [satu huruf namadepan] titik
                return $namabelakang . (($namadepan) ? ", $namadepan" : '') . '.';
            }, $semuaPengarang);

            // Gabungkan semua pengarang yang sudah diformat, menjadi satu dengan pemisah koma
            $semuaPengarang = implode(', ', $semuaPengarang);
        }

        /**
         * Berikut contoh hasil output dari sitasi gaya APA :
         * Douglas, K., Douglas, S. (2003). PostgreSQL : a comprehensive guide to building, programming, and administering PostgreSQL databases (1st ed.). Indianapolis: Sams.
         * Valade, J. (2004). PHP 5 for dummies . Hoboken, NJ: Wiley.
         */
        $out = $semuaPengarang . " ";
        $out .= ($th_terbit) ? "($th_terbit). " : '';
        $out .= "$judul. ";
        $out .= ($kota) ? "$kota: " : '';
        $out .= ($penerbit) ? "$penerbit. " : '';

        return $out;
    }

    static function filterEmpty(...$data)
    {
        // Buang item array yang null
        $out = array_filter($data);

        // Jika hasil filter ada isinya, maka kembalikan hasil filter nya
        // Jika kosong, maka kembalikan array dengan satu item string '-'
        return count($out) ? $out : ['-'];
    }
}
