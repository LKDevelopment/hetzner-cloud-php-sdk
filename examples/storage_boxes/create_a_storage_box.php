<?php

use LKDev\HetznerCloud\Models\StorageBoxes\StorageBoxAccessSettings;

require_once __DIR__.'/../bootstrap.php';

$serverType = $hetznerClient->serverTypes()->get(1);
$location = $hetznerClient->locations()->getByName('fsn1');
$type = $hetznerClient->storageBoxTypes()->get('bx11');

$response = $hetznerClient->storageBoxes()->create(
    name: 'my-storage-box',
    location: $location->name,
    storageBoxType: $type->name,
    password: '{my s3cr3t p@ssword}',
    labels: ['type' => 'untest'],
    accessSettings: new StorageBoxAccessSettings(reachable_externally:true),
);

$response->getResponsePart('action')->waitUntilCompleted();
$box = $response->getResponsePart('storage_box')->reload();

echo "Name: {$box->name}" . PHP_EOL;
echo "ID: {$box->id}" . PHP_EOL;
