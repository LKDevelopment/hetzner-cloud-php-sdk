<?php
require_once __DIR__.'/../vendor/autoload.php';
$apiKey = '{InsertYourAPIKeyHear}';

$hetznerClient = new \LKDev\HetznerCloud\HetznerAPIClient($apiKey);
