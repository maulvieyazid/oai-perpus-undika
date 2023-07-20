<?php
return [
    'oracle' => [
        'driver' => 'oci8',
        'host' => $_ENV['DB_HOST'] ?? '',
        'port' => $_ENV['DB_PORT'] ?? '1521',
        'database' => $_ENV['DB_DATABASE'] ?? '',
        'username' => $_ENV['DB_USERNAME'] ?? '',
        'password' => $_ENV['DB_PASSWORD'] ?? '',
    ],
];
