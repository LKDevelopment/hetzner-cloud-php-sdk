<?php

namespace LKDev\Tests\Unit\Models\StorageBoxes;

use GuzzleHttp\Psr7\Response;
use LKDev\HetznerCloud\Clients\GuzzleClient;
use LKDev\HetznerCloud\Models\StorageBoxes\StorageBoxAccessSettings;
use LKDev\HetznerCloud\Models\StorageBoxes\StorageBoxes;
use LKDev\HetznerCloud\Models\StorageBoxes\StorageBoxStats;
use LKDev\Tests\TestCase;

class StorageBoxesTest extends TestCase
{
    protected StorageBoxes $storageBoxes;

    public function setUp(): void
    {
        parent::setUp();
        $this->hetznerApi->setApiHetznerComClient(
            new GuzzleClient($this->hetznerApi, ['handler' => $this->mockHandler])
        );
        $this->storageBoxes = new StorageBoxes($this->hetznerApi->getApiHetznerComClient());
    }

    public function testCreate()
    {
        $this->mockHandler->append(new Response(201, [], file_get_contents(__DIR__.'/fixtures/storage_box_create.json')));
        $resp = $this->storageBoxes->create('new-storage-box', 'fsn1', 'bx11', 'SecurePass123!');

        $box = $resp->getResponsePart('storage_box');
        $this->assertEquals(2, $box->id);
        $this->assertEquals('my-resource', $box->name);
        $this->assertEquals('initializing', $box->status);

        $this->assertNotNull($resp->action);
        $this->assertEquals('create', $resp->action->command);

        $this->assertLastRequestEquals('POST', '/storage_boxes');
        $this->assertLastRequestBodyParametersEqual([
            'name' => 'new-storage-box',
            'location' => 'fsn1',
            'storage_box_type' => 'bx11',
            'password' => 'SecurePass123!',
        ]);
    }

    public function testGetById()
    {
        $this->mockHandler->append(new Response(200, [], file_get_contents(__DIR__.'/fixtures/storage_box.json')));
        $box = $this->storageBoxes->getById(1);

        $this->assertEquals(1, $box->id);
        $this->assertEquals('my-storage-box', $box->name);
        $this->assertEquals('active', $box->status);
        $this->assertEquals('u1337', $box->username);
        $this->assertNotNull($box->location);
        $this->assertEquals('fsn1', $box->location->name);
        $this->assertNotNull($box->storage_box_type);
        $this->assertEquals('bx11', $box->storage_box_type->name);
        $this->assertInstanceOf(StorageBoxAccessSettings::class, $box->access_settings);
        $this->assertTrue($box->access_settings->ssh_enabled);
        $this->assertFalse($box->access_settings->samba_enabled);
        $this->assertInstanceOf(StorageBoxStats::class, $box->stats);
        $this->assertEquals(0, $box->stats->size);

        $this->assertLastRequestEquals('GET', '/storage_boxes/1');
    }

    public function testGetByName()
    {
        $this->mockHandler->append(new Response(200, [], file_get_contents(__DIR__.'/fixtures/storage_boxes.json')));
        $box = $this->storageBoxes->getByName('my-storage-box');

        $this->assertEquals(1, $box->id);
        $this->assertEquals('my-storage-box', $box->name);

        $this->assertLastRequestEquals('GET', '/storage_boxes');
        $this->assertLastRequestQueryParametersContains('name', 'my-storage-box');
    }

    public function testList()
    {
        $this->mockHandler->append(new Response(200, [], file_get_contents(__DIR__.'/fixtures/storage_boxes.json')));
        $resp = $this->storageBoxes->list();

        $this->assertCount(1, $resp->storage_boxes);
        $this->assertEquals(1, $resp->storage_boxes[0]->id);
        $this->assertNotNull($resp->meta);

        $this->assertLastRequestEquals('GET', '/storage_boxes');
    }

    public function testAll()
    {
        $this->mockHandler->append(new Response(200, [], file_get_contents(__DIR__.'/fixtures/storage_boxes.json')));
        $boxes = $this->storageBoxes->all();

        $this->assertCount(1, $boxes);
        $this->assertEquals(1, $boxes[0]->id);
    }
}
