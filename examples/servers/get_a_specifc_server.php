<?php
require_once __DIR__.'/../bootstrap.php';

$servers = new \LKDev\HetznerCloud\Models\Servers\Servers();
$serverId = 494200;
$server = $servers->get($serverId);
var_dump($server);