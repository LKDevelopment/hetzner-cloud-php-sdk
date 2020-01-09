<?php
/**
 * Created by PhpStorm.
 * User: lukaskammerling
 * Date: 28.01.18
 * Time: 20:40.
 */

namespace Tests\Unit;

use GuzzleHttp\Client;
use LKDev\HetznerCloud\HetznerAPIClient;
use LKDev\HetznerCloud\Models\Actions\Actions;
use LKDev\HetznerCloud\Models\Datacenters\Datacenters;
use LKDev\HetznerCloud\Models\FloatingIps\FloatingIps;
use LKDev\HetznerCloud\Models\Images\Images;
use LKDev\HetznerCloud\Models\Locations\Locations;
use LKDev\HetznerCloud\Models\Networks\Networks;
use LKDev\HetznerCloud\Models\Prices\Prices;
use LKDev\HetznerCloud\Models\Servers\Servers;
use LKDev\HetznerCloud\Models\Servers\Types\ServerTypes;
use LKDev\HetznerCloud\Models\SSHKeys\SSHKeys;
use LKDev\HetznerCloud\Models\Volumes\Volumes;
use Tests\TestCase;

/**
 * Class BasicClientTest.
 */
class BasicClientTest extends TestCase
{
    public function testGetApiToken()
    {
        $token = 'IAmTheTestToken';
        $client = new HetznerAPIClient($token);
        $this->assertEquals($token, $client->getApiToken());
    }

    public function testSetBaseUrl()
    {
        $baseUrl = 'https://api.hetzner.cloud/v1/';
        $client = new HetznerAPIClient('IAmTheTestToken', $baseUrl);
        $this->assertEquals($baseUrl, $client->getBaseUrl());
        $client->setBaseUrl('changed');
        $this->assertEquals('changed', $client->getBaseUrl());
    }

    public function testSetUserAgent()
    {
        $userAgent = 'UserAgent';
        $client = new HetznerAPIClient('IAmTheTestToken', '', $userAgent);
        $this->assertEquals($userAgent, $client->getUserAgent());
        $client->setUserAgent('changed');
        $this->assertEquals('changed', $client->getUserAgent());
    }

    public function testSetHttpClient()
    {
        $client = new HetznerAPIClient('IAmTheTestToken', '');
        $httpClient = new Client();
        $client->setHttpClient($httpClient);
        $this->assertEquals($httpClient, $client->getHttpClient());
    }

    public function testMethodsReturnCorrectInstance()
    {
        $this->assertInstanceOf(Actions::class, $this->hetznerApi->actions());
        $this->assertInstanceOf(Servers::class, $this->hetznerApi->servers());
        $this->assertInstanceOf(ServerTypes::class, $this->hetznerApi->serverTypes());
        $this->assertInstanceOf(Images::class, $this->hetznerApi->images());
        $this->assertInstanceOf(Prices::class, $this->hetznerApi->prices());
        $this->assertInstanceOf(Locations::class, $this->hetznerApi->locations());
        $this->assertInstanceOf(Datacenters::class, $this->hetznerApi->datacenters());
        $this->assertInstanceOf(FloatingIps::class, $this->hetznerApi->floatingIps());
        $this->assertInstanceOf(SSHKeys::class, $this->hetznerApi->sshKeys());
        $this->assertInstanceOf(Volumes::class, $this->hetznerApi->volumes());
        $this->assertInstanceOf(Networks::class, $this->hetznerApi->networks());
    }
}
