<?php

require_once __DIR__.'/../bootstrap.php';

foreach ($hetznerClient->serverTypes()->all() as $serverType) {
    echo $serverType->name.PHP_EOL;
}
