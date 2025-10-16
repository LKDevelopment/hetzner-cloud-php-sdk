<?php

namespace LKDev\Tests\Unit\Models\Zones;

use GuzzleHttp\Psr7\Response;
use LKDev\HetznerCloud\Models\Zones\PrimaryNameserver;
use LKDev\HetznerCloud\Models\Zones\Record;
use LKDev\HetznerCloud\Models\Zones\RRSet;
use LKDev\HetznerCloud\Models\Zones\ZoneMode;
use LKDev\HetznerCloud\Models\zones\zones;
use LKDev\Tests\TestCase;

class ZonesTest extends TestCase
{
    /**
     * @var Zones
     */
    protected $zones;

    public function setUp(): void
    {
        parent::setUp();
        $this->zones = new zones($this->hetznerApi->getHttpClient());
    }

    public function testCreatePrimarySimple()
    {
        $this->mockHandler->append(new Response(200, [], file_get_contents(__DIR__ . '/fixtures/zone_create.json')));
        $resp = $this->zones->create('example.com', ZoneMode::PRIMARY);

        $Zone = $resp->getResponsePart('zone');
        $this->assertEquals($Zone->id, 4711);
        $this->assertEquals($Zone->name, 'example.com');

        $this->assertNotNull($resp->action);

        $this->assertLastRequestEquals('POST', '/zones');
        $this->assertLastRequestBodyParametersEqual(['name' => 'example.com', 'mode' => 'primary']);
    }

    public function testCreatePrimaryFull()
    {
        $this->mockHandler->append(new Response(200, [], file_get_contents(__DIR__ . '/fixtures/zone_create.json')));
        $resp = $this->zones->create('example.com', ZoneMode::PRIMARY, 10, ['key' => 'value'], [], [
            (RRSet::create('@', 'A', [
                new Record('192.0.2.1', 'my comment'),
            ], 3600, [])),
        ]);

        $Zone = $resp->getResponsePart('zone');
        $this->assertEquals($Zone->id, 4711);
        $this->assertEquals($Zone->name, 'example.com');

        $this->assertNotNull($resp->action);

        $this->assertLastRequestEquals('POST', '/zones');
        $this->assertLastRequestBodyParametersEqual(['name' => 'example.com',
            'mode' => 'primary',
            'ttl' => 10,
            'labels' => ['key' => 'value'],
            'rrsets' => [['type' => 'A', 'name' => '@', 'ttl' => 3600, 'records' => [['value' => '192.0.2.1', 'comment' => 'my comment']]]]]);
    }

    public function testCreateSecondary()
    {
        $this->mockHandler->append(new Response(200, [], file_get_contents(__DIR__ . '/fixtures/zone_create.json')));
        $resp = $this->zones->create('example.com', ZoneMode::SECONDARY, 10, ['key' => 'value'], [
            new PrimaryNameserver('192.168.178.1', 53),
        ],);

        $Zone = $resp->getResponsePart('zone');
        $this->assertEquals($Zone->id, 4711);
        $this->assertEquals($Zone->name, 'example.com');

        $this->assertNotNull($resp->action);

        $this->assertLastRequestEquals('POST', '/zones');
        $this->assertLastRequestBodyParametersEqual([
            'name' => 'example.com',
            'mode' => 'secondary',
            'ttl' => 10,
            'labels' => ['key' => 'value'],
            'primary_nameservers' => [['address' => '192.168.178.1', 'port' => 53]]]);
    }

    public function testGetByName()
    {
        $this->mockHandler->append(new Response(200, [], file_get_contents(__DIR__ . '/fixtures/zone.json')));
        $Zone = $this->zones->getByName('example.com');
        $this->assertEquals(4711, $Zone->id);
        $this->assertEquals('example.com', $Zone->name);

        $this->assertLastRequestEquals('GET', '/zones/example.com');
    }

    public function testGet()
    {
        $this->mockHandler->append(new Response(200, [], file_get_contents(__DIR__ . '/fixtures/zone.json')));
        $Zone = $this->zones->get(4711);
        $this->assertEquals($Zone->id, 4711);
        $this->assertEquals($Zone->name, 'example.com');

        $this->assertLastRequestEquals('GET', '/zones/4711');
    }

    public function testAll()
    {
        $this->mockHandler->append(new Response(200, [], file_get_contents(__DIR__ . '/fixtures/zones.json')));
        $zones = $this->zones->all();
        $this->assertCount(1, $zones);
        $Zone = $zones[0];
        $this->assertEquals($Zone->id, 4711);
        $this->assertEquals($Zone->name, 'example.com');

        $this->assertLastRequestEquals('GET', '/zones');
    }

    public function testList()
    {
        $this->mockHandler->append(new Response(200, [], file_get_contents(__DIR__ . '/fixtures/zones.json')));
        $zones = $this->zones->list()->zones;
        $this->assertCount(1, $zones);
        $Zone = $zones[0];
        $this->assertEquals($Zone->id, 4711);
        $this->assertEquals($Zone->name, 'example.com');

        $this->assertLastRequestEquals('GET', '/zones');
    }
}
