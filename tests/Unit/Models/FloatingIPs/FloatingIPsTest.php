<?php
/**
 * Created by PhpStorm.
 * User: lukaskammerling
 * Date: 11.07.18
 * Time: 18:31.
 */

namespace Tests\Unit\Models\FloatingIPs;

use GuzzleHttp\Psr7\Response;
use LKDev\HetznerCloud\Models\FloatingIps\FloatingIps;
use LKDev\HetznerCloud\Models\Locations\Location;
use LKDev\HetznerCloud\Models\Servers\Server;
use Tests\TestCase;

class FloatingIPsTest extends TestCase
{
    /**
     * @var FloatingIps
     */
    protected $floatingIps;

    public function setUp(): void
    {
        parent::setUp();
        $this->floatingIps = new FloatingIps($this->hetznerApi->getHttpClient());
    }

    public function testGet()
    {
        $this->mockHandler->append(new Response(200, [], file_get_contents(__DIR__.'/fixtures/floatingIP.json')));
        $floatingIp = $this->floatingIps->get(1);
        $this->assertEquals($floatingIp->id, 4711);
        $this->assertEquals($floatingIp->description, 'Web Frontend');
        $this->assertLastRequestEquals('GET', '/floating_ips/1');
    }

    public function testGetByName()
    {
        $this->mockHandler->append(new Response(200, [], file_get_contents(__DIR__.'/fixtures/floatingIPs.json')));
        $floatingIp = $this->floatingIps->getByName('Web Frontend');
        $this->assertEquals($floatingIp->id, 4711);
        $this->assertEquals($floatingIp->name, 'Web Frontend');

        $this->assertLastRequestQueryParametersContains('name', 'Web Frontend');
        $this->assertLastRequestEquals('GET', '/floating_ips');
    }

    public function testAll()
    {
        $this->mockHandler->append(new Response(200, [], file_get_contents(__DIR__.'/fixtures/floatingIPs.json')));
        $floatingIps = $this->floatingIps->all();

        $this->assertEquals(count($floatingIps), 1);
        $this->assertEquals($floatingIps[0]->id, 4711);
        $this->assertEquals($floatingIps[0]->description, 'Web Frontend');
        $this->assertLastRequestEquals('GET', '/floating_ips');
    }

    public function testList()
    {
        $this->mockHandler->append(new Response(200, [], file_get_contents(__DIR__.'/fixtures/floatingIPs.json')));
        $floatingIps = $this->floatingIps->list()->floating_ips;

        $this->assertEquals(count($floatingIps), 1);
        $this->assertEquals($floatingIps[0]->id, 4711);
        $this->assertEquals($floatingIps[0]->description, 'Web Frontend');
        $this->assertLastRequestEquals('GET', '/floating_ips');
    }

    /**
     * @throws \LKDev\HetznerCloud\APIException
     */
    public function testCreateWithLocation()
    {
        $this->mockHandler->append(new Response(200, [], file_get_contents(__DIR__.'/fixtures/floatingIP.json')));
        $floatingIp = $this->floatingIps->create('ipv4', 'Web Frontend', new Location(123, 'nbg1'), null, 'my-fip', ['key' => 'value']);

        $this->assertEquals($floatingIp->id, 4711);
        $this->assertEquals($floatingIp->description, 'Web Frontend');
        $this->assertLastRequestEquals('POST', '/floating_ips');
        $this->assertLastRequestBodyParametersEqual(['type' => 'ipv4', 'description' => 'Web Frontend', 'home_location' => 'nbg1', 'name' => 'my-fip', 'labels' => ['key' => 'value']]);
    }

    /**
     * @throws \LKDev\HetznerCloud\APIException
     */
    public function testCreateWithServer()
    {
        $this->mockHandler->append(new Response(200, [], file_get_contents(__DIR__.'/fixtures/floatingIP.json')));
        $floatingIp = $this->floatingIps->create('ipv4', 'Web Frontend', null, new Server(23));

        $this->assertEquals($floatingIp->id, 4711);
        $this->assertEquals($floatingIp->description, 'Web Frontend');
        $this->assertLastRequestEquals('POST', '/floating_ips');
        $this->assertLastRequestBodyParametersEqual(['type' => 'ipv4', 'description' => 'Web Frontend', 'server' => 23]);
    }

    /**
     * @throws \LKDev\HetznerCloud\APIException
     */
    public function testCreateWithName()
    {
        $this->mockHandler->append(new Response(200, [], file_get_contents(__DIR__.'/fixtures/floatingIP.json')));
        $floatingIp = $this->floatingIps->create('ipv4', 'Web Frontend', new Location(123, 'nbg1'), null, 'WebServer');

        $this->assertEquals($floatingIp->id, 4711);
        $this->assertEquals($floatingIp->description, 'Web Frontend');
        $this->assertLastRequestEquals('POST', '/floating_ips');
        $this->assertLastRequestBodyParametersEqual(['type' => 'ipv4', 'description' => 'Web Frontend', 'home_location' => 'nbg1']);
    }

    /**
     * @throws \LKDev\HetznerCloud\APIException
     */
    public function testDelete()
    {
        $this->mockHandler->append(new Response(200, [], file_get_contents(__DIR__.'/fixtures/floatingIP.json')));
        $floatingIp = $this->floatingIps->get(4711);
        $this->assertLastRequestEquals('GET', '/floating_ips/4711');

        $this->mockHandler->append(new Response(204, []));
        $this->assertTrue($floatingIp->delete());
        $this->assertLastRequestEquals('DELETE', '/floating_ips/4711');
    }
}
