<?php

namespace LKDev\Tests\Unit\Models\Locations;

use GuzzleHttp\Psr7\Response;
use LKDev\HetznerCloud\Models\Locations\Location;
use LKDev\Tests\TestCase;

class LocationTest extends TestCase
{
    /**
     * @var Location
     */
    protected $location;

    public function setUp(): void
    {
        parent::setUp();
        $tmp = json_decode(file_get_contents(__DIR__.'/fixtures/location.json'));
        $this->location = Location::parse($tmp->location);
    }

    public function testReload()
    {
        $this->mockHandler->append(new Response(200, [], file_get_contents(__DIR__.'/fixtures/location.json')));
        $reloaded = $this->location->reload();
        $this->assertInstanceOf(Location::class, $reloaded);
        $this->assertLastRequestEquals('GET', '/locations/1');
    }

    public function testParse()
    {
        $tmp = json_decode(file_get_contents(__DIR__.'/fixtures/location.json'));
        $parsed = Location::parse($tmp->location);
        $this->assertEquals($this->location->id, $parsed->id);
        $this->assertEquals($this->location->name, $parsed->name);
        $this->assertEquals($this->location->description, $parsed->description);
    }

    public function testDelete()
    {
        $this->expectException(\BadMethodCallException::class);
        $this->location->delete();
    }

    public function testUpdate()
    {
        $this->expectException(\BadMethodCallException::class);
        $this->location->update([]);
    }
}
