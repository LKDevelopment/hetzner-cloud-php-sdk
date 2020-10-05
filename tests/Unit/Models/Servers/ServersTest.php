<?php
/**
 * Created by PhpStorm.
 * User: lukaskammerling
 * Date: 11.07.18
 * Time: 18:31.
 */

namespace Tests\Integration\Servers;

use GuzzleHttp\Psr7\Response;
use LKDev\HetznerCloud\Models\Servers\Servers;
use Tests\TestCase;

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
}
