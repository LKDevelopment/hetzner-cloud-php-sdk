<?php

namespace LKDev\Tests\Unit\Models\Datacenters;

use GuzzleHttp\Psr7\Response;
use LKDev\HetznerCloud\Models\Datacenters\Datacenter;
use LKDev\Tests\TestCase;

class DatacenterTest extends TestCase
{
    /**
     * @var Datacenter
     */
    protected $datacenter;

    public function setUp(): void
    {
        parent::setUp();
        $tmp = json_decode(file_get_contents(__DIR__.'/fixtures/datacenter.json'));
        $this->datacenter = Datacenter::parse($tmp->datacenter);
    }

    public function testReload()
    {
        $this->mockHandler->append(new Response(200, [], file_get_contents(__DIR__.'/fixtures/datacenter.json')));
        $reloaded = $this->datacenter->reload();
        $this->assertInstanceOf(Datacenter::class, $reloaded);
        $this->assertLastRequestEquals('GET', '/datacenters/1');
    }

    public function testParse()
    {
        $tmp = json_decode(file_get_contents(__DIR__.'/fixtures/datacenter.json'));
        $parsed = Datacenter::parse($tmp->datacenter);
        $this->assertEquals($this->datacenter->id, $parsed->id);
        $this->assertEquals($this->datacenter->name, $parsed->name);
        $this->assertEquals($this->datacenter->description, $parsed->description);
    }

    public function testDelete()
    {
        $this->expectException(\BadMethodCallException::class);
        $this->datacenter->delete();
    }

    public function testUpdate()
    {
        $this->expectException(\BadMethodCallException::class);
        $this->datacenter->update([]);
    }
}
