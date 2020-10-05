<?php
/**
 * Created by PhpStorm.
 * User: lukaskammerling
 * Date: 11.07.18
 * Time: 18:31.
 */

namespace Tests\Unit\Models\ServerTypes;

use GuzzleHttp\Psr7\Response;
use LKDev\HetznerCloud\Models\Servers\Types\ServerTypes;
use Tests\TestCase;

class ServerTypesTest extends TestCase
{
    /**
     * @var  \LKDev\HetznerCloud\Models\Servers\Types\ServerTypes
     */
    protected $server_types;

    public function setUp(): void
    {
        parent::setUp();
        $this->server_types = new ServerTypes($this->hetznerApi->getHttpClient());
    }

    public function testGet()
    {
        $this->mockHandler->append(new Response(200, [], file_get_contents(__DIR__.'/fixtures/server_type.json')));
        $server_type = $this->server_types->get(1);
        $this->assertEquals($server_type->id, 1);
        $this->assertEquals($server_type->name, 'cx11');
        $this->assertLastRequestEquals('GET', '/server_types/1');
    }

    public function testGetByName()
    {
        $this->mockHandler->append(new Response(200, [], file_get_contents(__DIR__.'/fixtures/server_types.json')));
        $server_type = $this->server_types->getByName('cx11');
        $this->assertEquals($server_type->id, 1);
        $this->assertEquals($server_type->name, 'cx11');
        $this->assertLastRequestEquals('GET', '/server_types');
        $this->assertLastRequestQueryParametersContains('name', 'cx11');
    }

    public function testAll()
    {
        $this->mockHandler->append(new Response(200, [], file_get_contents(__DIR__.'/fixtures/server_types.json')));
        $server_types = $this->server_types->all();

        $this->assertEquals(count($server_types), 1);
        $this->assertEquals($server_types[0]->id, 1);
        $this->assertEquals($server_types[0]->name, 'cx11');
        $this->assertLastRequestEquals('GET', '/server_types');
    }

    public function testList()
    {
        $this->mockHandler->append(new Response(200, [], file_get_contents(__DIR__.'/fixtures/server_types.json')));
        $server_types = $this->server_types->list()->server_types;

        $this->assertEquals(count($server_types), 1);
        $this->assertEquals($server_types[0]->id, 1);
        $this->assertEquals($server_types[0]->name, 'cx11');
        $this->assertLastRequestEquals('GET', '/server_types');
    }
}
