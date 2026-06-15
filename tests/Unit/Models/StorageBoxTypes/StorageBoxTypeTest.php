<?php

namespace LKDev\Tests\Unit\Models\StorageBoxTypes;

use GuzzleHttp\Psr7\Response;
use LKDev\HetznerCloud\Clients\GuzzleClient;
use LKDev\HetznerCloud\Models\StorageBoxTypes\StorageBoxType;
use LKDev\HetznerCloud\Models\StorageBoxTypes\StorageBoxTypePrice;
use LKDev\HetznerCloud\Models\StorageBoxTypes\StorageBoxTypes;
use LKDev\Tests\TestCase;

class StorageBoxTypeTest extends TestCase
{
    protected StorageBoxType $storageBoxType;

    public function setUp(): void
    {
        parent::setUp();
        $this->hetznerApi->setApiHetznerComClient(
            new GuzzleClient($this->hetznerApi, ['handler' => $this->mockHandler])
        );
        $tmp = new StorageBoxTypes($this->hetznerApi->getApiHetznerComClient());
        $this->mockHandler->append(new Response(200, [], file_get_contents(__DIR__.'/fixtures/storage_box_type.json')));
        $this->storageBoxType = $tmp->getById(1);
    }

    public function testFields()
    {
        $this->assertEquals(1, $this->storageBoxType->id);
        $this->assertEquals('bx11', $this->storageBoxType->name);
        $this->assertEquals('BX11', $this->storageBoxType->description);
        $this->assertEquals(10, $this->storageBoxType->snapshot_limit);
        $this->assertEquals(10, $this->storageBoxType->automatic_snapshot_limit);
        $this->assertEquals(200, $this->storageBoxType->subaccounts_limit);
        $this->assertEquals(1073741824, $this->storageBoxType->size);
        $this->assertIsArray($this->storageBoxType->prices);
        $this->assertInstanceOf(StorageBoxTypePrice::class, $this->storageBoxType->prices[0]);
        $this->assertEquals('fsn1', $this->storageBoxType->prices[0]->location);
        $this->assertEquals('0.0051', $this->storageBoxType->prices[0]->price_hourly->net);
        $this->assertEquals('3.2000', $this->storageBoxType->prices[0]->price_monthly->net);
        $this->assertEquals('0.0000', $this->storageBoxType->prices[0]->setup_fee->net);
        $this->assertNull($this->storageBoxType->deprecation);
    }
}
