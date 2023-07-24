<?php

namespace Helper;

class Helper
{
    function random_unique_id()
    {
        return substr(bin2hex(random_bytes(16)), 0, 7);
    }

    function parseDatetoGranularity($datestring)
    {
        $datestring = trim((string) $datestring);

        // Kalo datestring nya kosong, maka return string kosong
        if (!$datestring) return '';

        // Kalo datestring nya '-', maka return string kosong
        if ($datestring == '-') return '';

        // Bentuk date dan kembalikan string seperti berikut :
        // 2010-06-03T00:00:00Z
        return date_create($datestring)->format('Y-m-d\TH:i:s\Z');
    }

    static function loadEncryptionKeyFromConfig()
    {
        // Ambil isi dari file secret key
        $keyAscii = file_get_contents(__DIR__ . '/../encrypt-secret-key.txt');

        // Return isi nya
        return \Defuse\Crypto\Key::loadFromAsciiSafeString($keyAscii);
    }

    static function regenerateEncryptionKeyConfig()
    {
        // Generate secret key dari package Defuse
        $key = Defuse\Crypto\Key::createNewRandomKey()->saveToAsciiSafeString();

        // Taruh secret key di file
        file_put_contents(__DIR__ . '/../encrypt-secret-key.txt', $key);
    }
}
