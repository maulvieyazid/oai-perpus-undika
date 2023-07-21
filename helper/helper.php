<?php

namespace Helper;

class Helper
{
    function random_unique_id()
    {
        return substr(bin2hex(random_bytes(16)), 0, 7);
    }

    function loadEncryptionKeyFromConfig()
    {
        $keyAscii = file_get_contents(__DIR__ . '/../encrypt-secret-key.txt');
        return \Defuse\Crypto\Key::loadFromAsciiSafeString($keyAscii);
    }
}
