<?php

namespace LKDev\Tests\Unit\Models\ISOs;

use GuzzleHttp\Psr7\Response;
use LKDev\HetznerCloud\Models\ISOs\ISO;
use LKDev\Tests\TestCase;

class ISOTest extends TestCase
{
    /**
     * @var ISO
     */
    protected $iso;

    public function setUp(): void
    {
        parent::setUp();
        $tmp = json_decode(file_get_contents(__DIR__.'/fixtures/iso.json'));
        $this->iso = ISO::parse($tmp->iso);
    }

    public function testReload()
    {
        $this->mockHandler->append(new Response(200, [], file_get_contents(__DIR__.'/fixtures/iso.json')));
        $reloaded = $this->iso->reload();
        $this->assertInstanceOf(ISO::class, $reloaded);
        $this->assertLastRequestEquals('GET', '/isos/4711');
    }

    public function testParse()
    {
        $tmp = json_decode(file_get_contents(__DIR__.'/fixtures/iso.json'));
        $parsed = ISO::parse($tmp->iso);
        $this->assertEquals($this->iso->id, $parsed->id);
        $this->assertEquals($this->iso->name, $parsed->name);
        $this->assertEquals($this->iso->description, $parsed->description);
        $this->assertEquals($this->iso->type, $parsed->type);
    }

    public function testDelete()
    {
        $this->expectException(\BadMethodCallException::class);
        $this->iso->delete();
    }

    public function testUpdate()
    {
        $this->expectException(\BadMethodCallException::class);
        $this->iso->update([]);
    }
}
