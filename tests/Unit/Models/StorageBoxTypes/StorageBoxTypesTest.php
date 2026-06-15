<?php

namespace LKDev\Tests\Unit\Models\StorageBoxTypes;

use GuzzleHttp\Psr7\Response;
use LKDev\HetznerCloud\Clients\GuzzleClient;
use LKDev\HetznerCloud\Models\StorageBoxTypes\StorageBoxTypes;
use LKDev\Tests\TestCase;

class StorageBoxTypesTest extends TestCase
{
    protected StorageBoxTypes $storageBoxTypes;

    public function setUp(): void
    {
        parent::setUp();
        $this->hetznerApi->setApiHetznerComClient(
            new GuzzleClient($this->hetznerApi, ['handler' => $this->mockHandler])
        );
        $this->storageBoxTypes = new StorageBoxTypes($this->hetznerApi->getApiHetznerComClient());
    }

    public function testGetById()
    {
        $this->mockHandler->append(new Response(200, [], file_get_contents(__DIR__.'/fixtures/storage_box_type.json')));
        $type = $this->storageBoxTypes->getById(1);

        $this->assertEquals(1, $type->id);
        $this->assertEquals('bx11', $type->name);
        $this->assertEquals(1073741824, $type->size);
        $this->assertEquals(200, $type->subaccounts_limit);

        $this->assertLastRequestEquals('GET', '/storage_box_types/1');
    }

    public function testGetByName()
    {
        $this->mockHandler->append(new Response(200, [], file_get_contents(__DIR__.'/fixtures/storage_box_types.json')));
        $type = $this->storageBoxTypes->getByName('bx11');

        $this->assertEquals(1, $type->id);
        $this->assertEquals('bx11', $type->name);

        $this->assertLastRequestEquals('GET', '/storage_box_types');
    }

    public function testList()
    {
        $this->mockHandler->append(new Response(200, [], file_get_contents(__DIR__.'/fixtures/storage_box_types.json')));
        $resp = $this->storageBoxTypes->list();

        $this->assertCount(1, $resp->storage_box_types);
        $this->assertEquals('bx11', $resp->storage_box_types[0]->name);
        $this->assertNotNull($resp->meta);

        $this->assertLastRequestEquals('GET', '/storage_box_types');
    }

    public function testAll()
    {
        $this->mockHandler->append(new Response(200, [], file_get_contents(__DIR__.'/fixtures/storage_box_types.json')));
        $types = $this->storageBoxTypes->all();

        $this->assertCount(1, $types);
        $this->assertEquals('bx11', $types[0]->name);
    }
}
