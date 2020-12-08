<?php

require_once __DIR__.'/../bootstrap.php';

$location = $hetznerClient->locations()->getById(1);

$fip = $hetznerClient->floatingIps()->create('ipv4', null, $location, null, 'my-floatingip', ['key' => 'value']);
echo 'FIP: '.$fip->name.PHP_EOL;
var_dump($fip);
