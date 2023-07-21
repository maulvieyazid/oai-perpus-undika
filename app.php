<?php
// Meload / memuat autoload composer
require_once __DIR__ . "/vendor/autoload.php";

// Meload / memuat file error handler ADODB
require_once __DIR__ . '/vendor/adodb/adodb-php/adodb-errorhandler.inc.php';

// Atur tanggal error nya selalu hari ini
$errordate = date_create('now')->format('Y-m-d');

error_reporting(E_ALL);

// Ini agar ADODB dapat me log error nya ke file
define('ADODB_ERROR_LOG_TYPE', 3);
define('ADODB_ERROR_LOG_DEST', __DIR__ . "/logs/adodb_error-{$errordate}.log");

// Lokasi file .env
$envDir = __DIR__;

// Init / mulai Dotenv
$dotenv = Dotenv\Dotenv::createImmutable($envDir);
$dotenv->load();

// Jika file encrypt-secret-key.txt tidak ada
// maka generate file tersebut
if (!file_exists(__DIR__ . '/encrypt-secret-key.txt')) {
    // Generate secret key dari package Defuse
    $key = Defuse\Crypto\Key::createNewRandomKey()->saveToAsciiSafeString();
    // Taruh secret key di file
    file_put_contents('encrypt-secret-key.txt', $key);
}
