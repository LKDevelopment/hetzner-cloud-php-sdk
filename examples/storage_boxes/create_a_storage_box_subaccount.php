<?php

use LKDev\HetznerCloud\Models\StorageBoxes\StorageBoxAccessSettings;

require_once __DIR__.'/../bootstrap.php';

$box = $hetznerClient->storageBoxes()->get('some-existing-box');

$response = $box->createSubaccount(
    'my_home_dir',
    'MySecret!1234',
    'some_name',
    new StorageBoxAccessSettings(
        reachable_externally: true,
        ssh_enabled: true,
    ),
    'Description',
    [
        'some' => 'label',
    ],
);
$response->getResponsePart('action')->waitUntilCompleted();
$account = $response->getResponsePart('subaccount')->reload();

echo "Name: {$account->name}" . PHP_EOL;
echo "ID: {$account->id}" . PHP_EOL;
echo "HomeDir: {$account->home_directory}" . PHP_EOL;


