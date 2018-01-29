<?php
require_once __DIR__.'/../bootstrap.php';

$servers = new \LKDev\HetznerCloud\Models\Servers\Servers();
foreach ($servers->all() as $server) {
    echo 'ID: '.$server->id.' Name:'.$server->name.' Status: '.$server->status.PHP_EOL;
}