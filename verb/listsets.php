<?php

$helper = new Helper\Helper;

// Tambahkan isian ListSets ke array $data
$data['ListSets'] = [
    "__custom:set:{$helper->random_unique_id()}" => [
        "setSpec" => 'class:activity',
        "setName" => 'Activities',
    ],
    "__custom:set:{$helper->random_unique_id()}" => [
        "setSpec" => 'class:collection',
        "setName" => 'Collections',
    ],
    "__custom:set:{$helper->random_unique_id()}" => [
        "setSpec" => 'class:party',
        "setName" => 'Parties',
    ],
];
