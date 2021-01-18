<?php

require_once __DIR__.'/../bootstrap.php';

$serverType = $hetznerClient->serverTypes()->get(1);
$location = $hetznerClient->locations()->getByName('fsn1');
$image = $hetznerClient->images()->getByName('ubuntu-20.04');
$apiResponse = $hetznerClient->servers()->createInLocation('my-example-server.test', $serverType, $image, $location);
$server = $apiResponse->getResponsePart('server');
$action = $apiResponse->getResponsePart('action');
$next_actions = $apiResponse->getResponsePart('next_actions');
echo 'Server: '.$server->name.PHP_EOL;
echo 'IP: '.$server->publicNet->ipv4->ip.PHP_EOL;
echo 'Password: '.$apiResponse->getResponsePart('root_password').PHP_EOL;
echo 'Now we wait on the success of the server creation!'.PHP_EOL;
echo date('H:i:s').PHP_EOL;
$action->waitUntilCompleted();

foreach ($next_actions as $na) {
    $na->waitUntilCompleted();
}
echo date('H:i:s').PHP_EOL;
echo 'Done!';
