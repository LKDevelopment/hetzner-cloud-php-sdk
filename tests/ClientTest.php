<?php
/**
 * Created by PhpStorm.
 * User: lukaskammerling
 * Date: 28.01.18
 * Time: 20:40
 */

namespace Tests;

use LKDev\HetznerCloud\HetznerAPIClient;
use PHPUnit\Framework\TestCase;

class GuzzleClientClientTest extends TestCase
{
    public function testGetApiToken()
    {
        $token = 'IAmTheTestToken';
        $client = new HetznerAPIClient($token);
        $this->assertEquals($token, $client->getApiToken());
    }

}
