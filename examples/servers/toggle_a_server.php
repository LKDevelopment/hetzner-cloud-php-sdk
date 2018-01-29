<?php
require_once __DIR__.'/../bootstrap.php';

$servers = new \LKDev\HetznerCloud\Models\Servers\Servers();
$serverId = 494200;
$server = $servers->get($serverId);
echo 'Server: '.$server->name.PHP_EOL;
echo "Perform Shutdown now:".PHP_EOL;
/**
 * @var \LKDev\HetznerCloud\Models\Servers\Server $server
 */
$action = $server->shutdown();

echo "Reply from API: Action ID: ".$action->id.' '.$action->command.' '.$action->started.PHP_EOL;

echo 'Wait some seconds that the server could shutdown.'.PHP_EOL;
sleep(5);
echo "Get the Server from the API:".PHP_EOL;
$server = $servers->get($serverId);
echo "Server status: ".$server->status.PHP_EOL;
echo "Let's start it again!";
$server->powerOn();
echo 'Wait some seconds that the server could startup.'.PHP_EOL;
sleep(5);
echo "Get the Server from the API:".PHP_EOL;
$server = $servers->get($serverId);
echo "Server status: ".$server->status.PHP_EOL;
