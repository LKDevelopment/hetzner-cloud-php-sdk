<?php
/**
 * Created by PhpStorm.
 * User: lukaskammerling
 * Date: 30.05.18
 * Time: 14:13
 */

namespace Tests;


use GuzzleHttp\Client;
use LKDev\HetznerCloud\HetznerAPIClient;

abstract class TestCase extends \PHPUnit\Framework\TestCase
{

    protected $hetznerApi;

    public function setUp()
    {
        $this->server = new Server(new Client());
        Server::boot();
        $this->hetznerApi = new HetznerAPIClient('abcdef', 'http://localhost:8000/v1/');
    }

    public function getExpectedResponse($response): string
    {
        $tmp = json_decode(file_get_contents(__DIR__ . '/server/response/' . $response . '.json'));
        $mtp = \GuzzleHttp\json_encode($tmp);

        return $mtp;
    }
}