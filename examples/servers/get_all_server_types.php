<?php
require_once __DIR__.'/../bootstrap.php';

$servers = new \LKDev\HetznerCloud\Models\Servers\Servers();
$serverTypes = new \LKDev\HetznerCloud\Models\Servers\Types\ServerTypes();
foreach ($serverTypes->all() as $serverType) {
    echo $serverType->name.PHP_EOL;
}