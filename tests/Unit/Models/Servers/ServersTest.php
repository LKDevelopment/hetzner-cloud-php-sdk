<?php

/**
 * Created by PhpStorm.
 * User: lukaskammerling
 * Date: 11.07.18
 * Time: 18:31.
 */

namespace LKDev\Tests\Integration\Servers;

use GuzzleHttp\Psr7\Response;
use LKDev\HetznerCloud\Models\Datacenters\Datacenter;
use LKDev\HetznerCloud\Models\Images\Image;
use LKDev\HetznerCloud\Models\Locations\Location;
use LKDev\HetznerCloud\Models\Servers\Servers;
use LKDev\HetznerCloud\Models\Servers\Types\ServerType;
use LKDev\Tests\TestCase;

class ServersTest extends TestCase
{
    /**
     * @var Servers
     */
    protected $servers;

    public function setUp(): void
    {
        parent::setUp();
        $this->servers = new Servers($this->hetznerApi->getHttpClient());
    }

    public function testGet()
    {
        $this->mockHandler->append(new Response(200, [], file_get_contents(__DIR__.'/fixtures/server.json')));
        $server = $this->servers->get(42);
        $this->assertEquals($server->id, 42);
        $this->assertEquals($server->name, 'my-server');
        $this->assertEquals($server->status, 'running');
        $this->assertLastRequestEquals('GET', '/servers/42');
    }

    public function testGetByName()
    {
        $this->mockHandler->append(new Response(200, [], file_get_contents(__DIR__.'/fixtures/servers.json')));
        $server = $this->servers->getByName('my-server');
        $this->assertEquals($server->id, 42);
        $this->assertEquals($server->name, 'my-server');
        $this->assertEquals($server->status, 'running');
        $this->assertLastRequestEquals('GET', '/servers');
        $this->assertLastRequestQueryParametersContains('name', 'my-server');
    }

    public function testAll()
    {
        $this->mockHandler->append(new Response(200, [], file_get_contents(__DIR__.'/fixtures/servers.json')));
        $servers = $this->servers->all();

        $this->assertEquals(count($servers), 1);
        $this->assertEquals($servers[0]->id, 42);
        $this->assertEquals($servers[0]->name, 'my-server');
        $this->assertLastRequestEquals('GET', '/servers');
    }

    public function testList()
    {
        $this->mockHandler->append(new Response(200, [], file_get_contents(__DIR__.'/fixtures/servers.json')));
        $servers = $this->servers->list()->servers;

        $this->assertEquals(count($servers), 1);
        $this->assertEquals($servers[0]->id, 42);
        $this->assertEquals($servers[0]->name, 'my-server');
        $this->assertLastRequestEquals('GET', '/servers');
    }

    public function testCreateInLocationOmitsPublicNetByDefault()
    {
        $this->mockHandler->append(new Response(201, [], file_get_contents(__DIR__.'/fixtures/server_create.json')));
        $this->servers->createInLocation('my-server', new ServerType(1), new Image(4711), new Location(1, 'fsn1'));

        $body = json_decode((string) $this->mockHandler->getLastRequest()->getBody(), true);
        $this->assertArrayNotHasKey('public_net', $body);
        $this->assertLastRequestEquals('POST', '/servers');
    }

    public function testCreateInLocationSendsPublicNetWhenProvided()
    {
        $this->mockHandler->append(new Response(201, [], file_get_contents(__DIR__.'/fixtures/server_create.json')));
        $publicNet = ['enable_ipv4' => true, 'enable_ipv6' => false];
        $this->servers->createInLocation('my-server', new ServerType(1), new Image(4711), new Location(1, 'fsn1'), public_net: $publicNet);

        $this->assertLastRequestEquals('POST', '/servers');
        $this->assertLastRequestBodyParametersEqual(['public_net' => $publicNet]);
    }

    public function testCreateInDatacenterOmitsPublicNetByDefault()
    {
        $this->mockHandler->append(new Response(201, [], file_get_contents(__DIR__.'/fixtures/server_create.json')));
        $this->servers->createInDatacenter('my-server', new ServerType(1), new Image(4711), new Datacenter(1, 'fsn1-dc8', 'Falkenstein 1 DC 8', new Location(1, 'fsn1')));

        $body = json_decode((string) $this->mockHandler->getLastRequest()->getBody(), true);
        $this->assertArrayNotHasKey('public_net', $body);
        $this->assertLastRequestEquals('POST', '/servers');
    }

    public function testCreateInDatacenterSendsPublicNetWhenProvided()
    {
        $this->mockHandler->append(new Response(201, [], file_get_contents(__DIR__.'/fixtures/server_create.json')));
        $publicNet = ['enable_ipv4' => true, 'enable_ipv6' => false];
        $this->servers->createInDatacenter('my-server', new ServerType(1), new Image(4711), new Datacenter(1, 'fsn1-dc8', 'Falkenstein 1 DC 8', new Location(1, 'fsn1')), public_net: $publicNet);

        $this->assertLastRequestEquals('POST', '/servers');
        $this->assertLastRequestBodyParametersEqual(['public_net' => $publicNet]);
    }
}
