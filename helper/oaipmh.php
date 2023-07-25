<?php

namespace Helper;

use Defuse\Crypto\Crypto;

class OAIPMH
{
    // Jumlah maksimal data yang harus ditampilkan per data katalog (buku, majalah, software, TA)
    const LIMIT_MAX_QUERY = 25;

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

    static function getResumptionToken()
    {
        /** Contoh nilai dari query param resumptionToken adalah seperti berikut : 
         * (encrypt) : def5020098c21089ba0ffa82f56c9e116d677a78...
         * (decrypt) : 26~50
         * 
         * (encrypt) : def50200a69caf1a1e63dbdefe38ed34931eb1e5...
         * (decrypt) : 51~75
         */
        $resumptionToken = $_GET['resumptionToken'] ?? null;

        $min_row = 1;
        $max_row = self::LIMIT_MAX_QUERY;

        // Kalo query param resumptionToken tidak ada, berarti ini page pertama
        if (!$resumptionToken) {
            // Generate resumptionToken selanjutnya dengan memasukkan
            // ($max_row + 1) sebagai min_row
            // ($max_row + self::LIMIT_MAX_QUERY) sebagai max_row
            $next_resumption_token = self::generateResumptionToken(($max_row + 1), ($max_row + self::LIMIT_MAX_QUERY));

            // Return variabel2 ini sebagai array
            return compact('min_row', 'max_row', 'next_resumption_token');
        }

        /* Kalo query param resumptionToken nya ada, maka */
        // Lakukan decrypt terhadap resumptionToken
        $resumptionToken = self::parseResumptionToken($resumptionToken);

        // Pecah string token nya berdasarkan "~"
        $resumptionToken = explode('~', $resumptionToken);

        // Ambil nilai min_row dan max_row
        $min_row = (int) $resumptionToken[0];
        $max_row = (int) $resumptionToken[1];

        // Generate resumptionToken selanjutnya dengan memasukkan
        // ($max_row + 1) sebagai min_row
        // ($max_row + self::LIMIT_MAX_QUERY) sebagai max_row
        $next_resumption_token = self::generateResumptionToken(($max_row + 1), ($max_row + self::LIMIT_MAX_QUERY));

        // Return variabel2 ini sebagai array
        return compact('min_row', 'max_row', 'next_resumption_token');
    }

    static function parseResumptionToken($resumptionToken)
    {
        // Ambil key dari config file
        $key = Helper::loadEncryptionKeyFromConfig();

        // Return hasil decrypt resumptionToken
        return Crypto::decrypt($resumptionToken, $key);
    }

    static function generateResumptionToken($min_row, $max_row)
    {
        // Bentuk string untuk diencrypt sebagai resumptionToken
        $text = "$min_row~$max_row";

        // Ambil key dari config file
        $key = Helper::loadEncryptionKeyFromConfig();

        // Kembalikan hasil encrypt sebagai resumptionToken
        return Crypto::encrypt($text, $key);
    }
}
