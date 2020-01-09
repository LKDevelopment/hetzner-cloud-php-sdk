<?php

require_once __DIR__.'/../bootstrap.php';

$serverId = 494200;
$server = $hetznerClient->servers()->get($serverId);
var_dump($server);
