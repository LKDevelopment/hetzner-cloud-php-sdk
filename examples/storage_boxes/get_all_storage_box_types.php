<?php

require_once __DIR__.'/../bootstrap.php';

foreach ($hetznerClient->storageBoxTypes()->all() as $type) {
    echo "Name: {$type->name} - ID: {$type->id}" . PHP_EOL;
}
