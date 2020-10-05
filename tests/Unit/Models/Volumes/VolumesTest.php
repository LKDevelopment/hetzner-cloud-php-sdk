<?php

namespace Tests\Unit\Models\Volumes;

use GuzzleHttp\Psr7\Response;
use LKDev\HetznerCloud\Models\Locations\Location;
use LKDev\HetznerCloud\Models\Volumes\Volumes;
use Tests\TestCase;

class VolumesTest extends TestCase
{
    /**
     * @var Volumes
     */
    protected $volumes;

    public function setUp(): void
    {
        parent::setUp();
        $this->volumes = new Volumes($this->hetznerApi->getHttpClient());
    }

    public function testCreate()
    {
        $this->mockHandler->append(new Response(200, [], file_get_contents(__DIR__.'/fixtures/volume_create.json')));
        $resp = $this->volumes->create('database-storage', 42, null, new Location(1, 'nbg1'));

        $volume = $resp->getResponsePart('volume');
        $this->assertEquals($volume->id, 4711);
        $this->assertEquals($volume->name, 'database-storage');
        $this->assertEquals($volume->server, 12);
        $this->assertEquals($volume->location->id, 1);

        $this->assertNotNull($resp->actions);
        $this->assertIsArray($resp->next_actions);

        $this->assertLastRequestEquals('POST', '/volumes');
        $this->assertLastRequestBodyParametersEqual(['name' => 'database-storage', 'size' => 42, 'location' => 'nbg1']);
    }

    public function testGetByName()
    {
        $this->mockHandler->append(new Response(200, [], file_get_contents(__DIR__.'/fixtures/volumes.json')));
        $volume = $this->volumes->getByName('database-storage');
        $this->assertEquals($volume->id, 4711);
        $this->assertEquals($volume->name, 'database-storage');
        $this->assertEquals($volume->server, 12);
        $this->assertEquals($volume->location->id, 1);

        $this->assertLastRequestEquals('GET', '/volumes');
        $this->assertLastRequestQueryParametersContains('name', 'database-storage');
    }

    public function testGet()
    {
        $this->mockHandler->append(new Response(200, [], file_get_contents(__DIR__.'/fixtures/volume.json')));
        $volume = $this->volumes->get(4711);
        $this->assertEquals($volume->id, 4711);
        $this->assertEquals($volume->name, 'database-storage');
        $this->assertEquals($volume->server, 12);
        $this->assertEquals($volume->location->id, 1);

        $this->assertLastRequestEquals('GET', '/volumes/4711');
    }

    public function testAll()
    {
        $this->mockHandler->append(new Response(200, [], file_get_contents(__DIR__.'/fixtures/volumes.json')));
        $volumes = $this->volumes->all();
        $this->assertCount(1, $volumes);
        $volume = $volumes[0];
        $this->assertEquals($volume->id, 4711);
        $this->assertEquals($volume->name, 'database-storage');
        $this->assertEquals($volume->server, 12);
        $this->assertEquals($volume->location->id, 1);

        $this->assertLastRequestEquals('GET', '/volumes');
    }

    public function testList()
    {
        $this->mockHandler->append(new Response(200, [], file_get_contents(__DIR__.'/fixtures/volumes.json')));
        $volumes = $this->volumes->list()->volumes;
        $this->assertCount(1, $volumes);
        $volume = $volumes[0];
        $this->assertEquals($volume->id, 4711);
        $this->assertEquals($volume->name, 'database-storage');
        $this->assertEquals($volume->server, 12);
        $this->assertEquals($volume->location->id, 1);

        $this->assertLastRequestEquals('GET', '/volumes');
    }
}
