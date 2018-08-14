<?php
/**
 * Created by PhpStorm.
 * User: lukaskammerling
 * Date: 28.01.18
 * Time: 20:40
 */

namespace Tests;

use LKDev\HetznerCloud\HetznerAPIClient;
use LKDev\HetznerCloud\Models\Actions\Actions;
use LKDev\HetznerCloud\Models\Datacenters\Datacenters;
use LKDev\HetznerCloud\Models\FloatingIps\FloatingIps;
use LKDev\HetznerCloud\Models\Images\Images;
use LKDev\HetznerCloud\Models\Locations\Locations;
use LKDev\HetznerCloud\Models\Prices\Prices;
use LKDev\HetznerCloud\Models\Servers\Servers;
use LKDev\HetznerCloud\Models\Servers\Types\ServerTypes;
use LKDev\HetznerCloud\Models\SSHKeys\SSHKeys;
/**
 * Class BasicClientTest
 * @package Tests
 */
class BasicClientTest extends TestCase
{
    /**
     *
     */
    public function testGetApiToken()
    {
        $token = 'IAmTheTestToken';
        $client = new HetznerAPIClient($token);
        $this->assertEquals($token, $client->getApiToken());
    }

    /**
     *
     */
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
    }

}
