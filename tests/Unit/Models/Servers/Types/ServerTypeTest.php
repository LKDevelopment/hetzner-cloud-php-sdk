<?php

namespace LKDev\Tests\Unit\Models\Servers\Types;

use LKDev\HetznerCloud\Models\DeprecationInfo;
use LKDev\HetznerCloud\Models\Prices\ServerTypePrice;
use LKDev\HetznerCloud\Models\Servers\Types\ServerType;
use LKDev\Tests\TestCase;

class ServerTypeTest extends TestCase
{
    public function testParse()
    {
        $tmp = json_decode(file_get_contents(__DIR__.'/../fixtures/server.json'));
        $serverType = ServerType::parse($tmp->server->server_type);
        $this->assertInstanceOf(ServerType::class, $serverType);
        $this->assertEquals(1, $serverType->id);
        $this->assertEquals('cx11', $serverType->name);
        $this->assertEquals('CX11', $serverType->description);
        $this->assertEquals(1, $serverType->cores);
        $this->assertEquals(1, $serverType->memory);
        $this->assertEquals(25, $serverType->disk);
        $this->assertEquals('local', $serverType->storageType);
        $this->assertEquals('shared', $serverType->cpuType);
        $this->assertNull($serverType->architecture);
        $this->assertFalse($serverType->deprecated);
        $this->assertNull($serverType->deprecation);

        $inputWithPrice = $tmp->server->server_type;
        $inputWithPrice->price = $inputWithPrice->prices[0];
        $serverTypeWithPrice = ServerType::parse($inputWithPrice);
        $this->assertInstanceOf(ServerTypePrice::class, $serverTypeWithPrice->price);
        $this->assertEquals('fsn1', $serverTypeWithPrice->price->location);
    }

    public function testParseWithDeprecation()
    {
        $input = json_decode('{
            "id": 2,
            "name": "cx11-ceph",
            "description": "CX11 (Ceph)",
            "cores": 1,
            "memory": 1,
            "disk": 25,
            "deprecated": true,
            "deprecation": {
                "announced": "2023-06-01T00:00:00Z",
                "unavailable_after": "2023-09-01T00:00:00Z"
            },
            "prices": [],
            "storage_type": "network",
            "cpu_type": "shared",
            "architecture": "x86"
        }');

        $serverType = ServerType::parse($input);
        $this->assertTrue($serverType->deprecated);
        $this->assertInstanceOf(DeprecationInfo::class, $serverType->deprecation);
        $this->assertEquals('2023-06-01T00:00:00Z', $serverType->deprecation->announced);
        $this->assertEquals('2023-09-01T00:00:00Z', $serverType->deprecation->unavailableAfter);
    }
}
