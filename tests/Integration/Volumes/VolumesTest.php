<?php

namespace Tests\Integration\Volumes;

use LKDev\HetznerCloud\Models\Locations\Location;
use LKDev\HetznerCloud\Models\Volumes\Volumes;
use Tests\TestCase;

class VolumesTest extends TestCase
{
    /**
     * @var Volumes
     */
    protected $volumes;

    /**
     *
     */
    public function setUp()
    {
        parent::setUp();
        $this->volumes = new Volumes($this->hetznerApi->getHttpClient());
    }

    public function testCreate()
    {
        $resp = $this->volumes->create("database-storage", 42, null, new Location(1, "nbg1"));

        $volume = $resp->getResponsePart("volume");
        $this->assertEquals($volume->id, 4711);
        $this->assertEquals($volume->name, "database-storage");
        $this->assertEquals($volume->server, 12);
        $this->assertEquals($volume->location->id, 1);
    }

    public function testGetByName()
    {
        $volume = $this->volumes->getByName("database-storage");
        $this->assertEquals($volume->id, 4711);
        $this->assertEquals($volume->name, "database-storage");
        $this->assertEquals($volume->server, 12);
        $this->assertEquals($volume->location->id, 1);
    }

    public function testGet()
    {
        $volume = $this->volumes->get(4711);
        $this->assertEquals($volume->id, 4711);
        $this->assertEquals($volume->name, "database-storage");
        $this->assertEquals($volume->server, 12);
        $this->assertEquals($volume->location->id, 1);
    }

    public function testAll()
    {
        $volumes = $this->volumes->all();
        $this->assertCount(1, $volumes);
        $volume = $volumes[0];
        $this->assertEquals($volume->id, 4711);
        $this->assertEquals($volume->name, "database-storage");
        $this->assertEquals($volume->server, 12);
        $this->assertEquals($volume->location->id, 1);
    }
}
