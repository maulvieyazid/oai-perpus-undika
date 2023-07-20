<?php

namespace Core;

use Exception;

class DB
{
    private function getDbConfig()
    {
        return include_once __DIR__ . "/../config/database.php";
    }

    public static function setConnection($connection_name = '')
    {
        // Kalo nama koneksi nya null
        if (!$connection_name) throw new Exception("Nama Koneksi DB tidak boleh kosong");

        $objDb = new DB();

        // Ambil config database
        $db_config_list = $objDb->getDbConfig();

        // Jika nama koneksi tidak ada, maka lempar kan exception
        if (!array_key_exists($connection_name, $db_config_list)) throw new Exception("Nama Koneksi DB tidak ditemukan");

        // Ambil konfigurasi db, lalu jadikan object
        $db = (object) $db_config_list[$connection_name];

        // Init ADODB
        $adodb = newAdoConnection($db->driver);

        // Jika drivernya "oci8", maka perlu mengaktifkan "connectSID"
        if ($db->driver == 'oci8') {
            $adodb->connectSID = true;
        }

        // Sambungkan / Connect kan database
        $adodb->connect("{$db->host}:{$db->port}", $db->username, $db->password, $db->database);

        // Jika tidak tersambung, maka lemparkan exception
        if (!$adodb->isConnected()) throw new Exception($adodb->ErrorMsg());

        // Kembalikan instance ADODB nya
        return $adodb;
    }
}
