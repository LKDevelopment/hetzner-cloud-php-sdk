<?php
/**
 * Created by PhpStorm.
 * User: lukaskammerling
 * Date: 30.05.18
 * Time: 14:13
 */

namespace Tests;


use GuzzleHttp\Client;

class Server
{

    /** @var \GuzzleHttp\Client */
    protected $client;

    public function __construct(Client $client)
    {
        static::boot();
        $this->client = $client;
    }

    public static function boot()
    {
        if (!file_exists(__DIR__ . '/server/vendor')) {
            exec('cd "' . __DIR__ . '/server"; composer install');
        }
        if (static::serverHasBooted()) {
            return;
        }
        $pid = exec('php -S ' . static::getServerUrl() . ' -t ./tests/server/public > /dev/null 2>&1 & echo $!');
        while (!static::serverHasBooted()) {
            usleep(1000);
        }
        register_shutdown_function(function () use ($pid) {
            exec('kill ' . $pid);
        });
    }

    public static function getServerUrl(string $endPoint = ''): string
    {
        return 'localhost:' . getenv('TEST_SERVER_PORT') . '/' . $endPoint;
    }

    public static function serverHasBooted(): bool
    {
        return @file_get_contents('http://' . self::getServerUrl('booted')) != false;
    }
}
